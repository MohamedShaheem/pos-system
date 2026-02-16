<?php

namespace App\Http\Controllers;

use App\Models\GoldBalance;
use App\Models\GoldRate;
use App\Models\ProductMergePending;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductMergeHistory;
use App\Models\ProductMergeHistoryDetail;
use App\Models\SubCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\MergeRequestProcessed;

class ProductMergeController extends Controller
{
    public function index()
    {
        $products = Product::with('goldRate')
                    ->where('status', 'active')
                    ->where('qty', '>', 0)
                    ->where('type', '0')
                    ->where('product_type', 'gold')
                    ->orderBy('product_no', 'desc')
                    ->with(['goldRate', 'subCategory'])
                    ->get();

        $categories = ProductCategory::all();
        $suppliers = Supplier::all();
        // $subcategories = SubCategory::all();
        $goldRates = GoldRate::all();
        return view('products.merge.index', compact('products','categories','goldRates','suppliers'));
    }

    public function getSubcategories(Request $request)
    {
        $subcategories = SubCategory::where('product_category_id', $request->category_id)->get();
        return response()->json($subcategories);
    }

    public function superMerge(Request $request){
         try {
            // Log the incoming request data
            Log::info('Product merge request data:', $request->all());

            // Validate request data
            $validated = $request->validate([
                'source_products' => 'required|array|size:2',
                'source_products.*' => 'exists:products,id',
                'merge_type' => 'required|in:2-1',
                
                // Portions to take from each product
                'product_a_weight_portion' => 'required|numeric|min:0.001',
                'product_a_wastage_portion' => 'nullable|numeric|min:0',
                'product_a_stone_portion' => 'nullable|numeric|min:0',
                'product_a_charges_portion' => 'nullable|numeric|min:0',
                
                'product_b_weight_portion' => 'required|numeric|min:0.001',
                'product_b_wastage_portion' => 'nullable|numeric|min:0',
                'product_b_stone_portion' => 'nullable|numeric|min:0',
                'product_b_charges_portion' => 'nullable|numeric|min:0',
                
                // New merged product details
                'merged_product_name' => 'required|string|max:255',
                'merged_product_desc' => 'nullable|string',
                'merged_product_category_id' => 'required|exists:product_categories,id',
                'merged_product_supplier_id' => 'nullable|exists:suppliers,id',
                'merged_product_sub_category_id' => 'nullable|exists:sub_categories,id',
                'merged_product_gold_rate_id' => 'required|exists:gold_rates,id',
                'merged_product_qty' => 'required|integer|min:1',
                
                // Combined leftover product (optional)
                'create_option' => 'required|in:leftover,damage,none',
                // Leftover fields required only if create_option is 'leftover'
                'leftover_name' => 'required_if:create_option,leftover|string|max:255|nullable',
                'leftover_desc' => 'nullable|string',
                'leftover_supplier_id' => 'required_if:create_option,leftover|exists:suppliers,id|nullable',
                'leftover_category_id' => 'required_if:create_option,leftover|exists:product_categories,id|nullable',
                'leftover_sub_category_id' => 'nullable|exists:sub_categories,id',
                'leftover_gold_rate_id' => 'required_if:create_option,leftover|exists:gold_rates,id|nullable',
                'leftover_qty' => 'required_if:create_option,leftover|integer|min:1|nullable',
            ]);

            Log::info('Validation passed. Processing merge...');

            DB::beginTransaction();

            try {
                // Get the products with locks to prevent concurrent modifications
                $productA = Product::lockForUpdate()->findOrFail($request->source_products[0]);
                $productB = Product::lockForUpdate()->findOrFail($request->source_products[1]);

                // Log the products being merged
                Log::info('Products being merged:', [
                    'product_a' => [
                        'id' => $productA->id,
                        'name' => $productA->name,
                        'qty' => $productA->qty,
                        'weight' => $productA->weight,
                        'portion_weight' => $request->product_a_weight_portion
                    ],
                    'product_b' => [
                        'id' => $productB->id,
                        'name' => $productB->name,
                        'qty' => $productB->qty,
                        'weight' => $productB->weight,
                        'portion_weight' => $request->product_b_weight_portion
                    ]
                ]);

                // Validate products are in stock
                if ($productA->qty <= 0 || $productB->qty <= 0) {
                    DB::rollBack();
                    Log::warning('Product merge failed: Products out of stock', [
                        'product_a_qty' => $productA->qty,
                        'product_b_qty' => $productB->qty
                    ]);
                    return redirect()->back()->with('error', 'One or both products are out of stock');
                }

                // Validate portions don't exceed available amounts
                $this->validatePortions($productA, $request, 'a');
                $this->validatePortions($productB, $request, 'b');

                // Create merge history record
                $mergeHistory = ProductMergeHistory::create([
                    'merged_at' => now(),
                    'merged_by' => auth()->id(),
                    'merge_type' => $request->merge_type
                ]);

                // Calculate combined leftovers
                $combinedLeftover = $this->calculateCombinedLeftovers($productA, $productB, $request);

                // Record original product details in history
                $this->recordProductHistory($mergeHistory, $productA, 'source_a', $request, 'a');
                $this->recordProductHistory($mergeHistory, $productB, 'source_b', $request, 'b');

                // Create the merged product
                $mergedProduct = $this->createMergedProduct($request, $productA, $productB);
                Log::info('Created merged product:', ['id' => $mergedProduct->id, 'name' => $mergedProduct->name]);

                // Record merged product in history
                $this->recordMergedProductHistory($mergeHistory, $mergedProduct, $request);

                // Create combined leftover product if requested
                $leftoverProduct = null;

                if ($request->create_option === 'leftover' && $this->hasSignificantLeftover($combinedLeftover)) {
                    $leftoverProduct = $this->createCombinedLeftoverProduct($request, $combinedLeftover);
                    Log::info('Created combined leftover product:', [
                        'id' => $leftoverProduct->id,
                        'name' => $leftoverProduct->name,
                    ]);
                    $this->recordLeftoverProductHistory($mergeHistory, $leftoverProduct, 'leftover');

                } elseif ($request->create_option === 'damage') {
                    $lastBalance = GoldBalance::orderBy('created_at', 'desc')->first();
                    $currentBalance = $lastBalance ? $lastBalance->gold_balance : 0;

                    $newGoldWeight = $combinedLeftover['weight'];
                    $newBalance = $currentBalance + $newGoldWeight;

                    GoldBalance::create([
                        'description' => "Damage gold (Merge)",
                        'gold_in' => $newGoldWeight,
                        'gold_out' => 0,
                        'gold_balance' => $newBalance,
                    ]);

                    Log::info('Added damage gold to GoldBalance table.', [
                        'amount' => $newGoldWeight,
                        'new_balance' => $newBalance,
                    ]);
                }


                // Set original product quantities to 0 and mark as inactive
                $productA->update([
                    'qty' => 0,
                    'status' => 'inactive'
                ]);

                $productB->update([
                    'qty' => 0,
                    'status' => 'inactive'
                ]);

                Log::info('Updated original product status and quantities:', [
                    'product_a' => ['id' => $productA->id, 'qty' => $productA->qty, 'status' => $productA->status],
                    'product_b' => ['id' => $productB->id, 'qty' => $productB->qty, 'status' => $productB->status]
                ]);


                DB::commit();
                Log::info('Product merge completed successfully');

                $message = "Products merged successfully! New product '{$mergedProduct->name}' created.";
                if ($leftoverProduct) {
                    $message .= " Combined leftover product '{$leftoverProduct->name}' created from remaining materials.";
                }

                return redirect()->route('products.merge.index')
                    ->with('success', $message);

            } catch (\Exception $innerException) {
                DB::rollBack();
                Log::error('Inner transaction error: ' . $innerException->getMessage());
                Log::error($innerException->getTraceAsString());
                throw $innerException;
            }

        } catch (\Exception $e) {
            Log::error('Product merge error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Error merging products: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function merge(Request $request)
    {
        try {
            // Log the incoming request data
            Log::info('Product merge request data:', $request->all());

            // Validate request data
            $validated = $request->validate([
                'source_products' => 'required|array|size:2',
                'source_products.*' => 'exists:products,id',
                'merge_type' => 'required|in:2-1',
                
                // Portions to take from each product
                'product_a_weight_portion' => 'required|numeric|min:0.001',
                'product_a_wastage_portion' => 'nullable|numeric|min:0',
                'product_a_stone_portion' => 'nullable|numeric|min:0',
                'product_a_charges_portion' => 'nullable|numeric|min:0',
                
                'product_b_weight_portion' => 'required|numeric|min:0.001',
                'product_b_wastage_portion' => 'nullable|numeric|min:0',
                'product_b_stone_portion' => 'nullable|numeric|min:0',
                'product_b_charges_portion' => 'nullable|numeric|min:0',
                
                // New merged product details
                'merged_product_name' => 'required|string|max:255',
                'merged_product_desc' => 'nullable|string',
                'merged_product_category_id' => 'required|exists:product_categories,id',
                'merged_product_supplier_id' => 'nullable|exists:suppliers,id',
                'merged_product_sub_category_id' => 'nullable|exists:sub_categories,id',
                'merged_product_gold_rate_id' => 'required|exists:gold_rates,id',
                'merged_product_qty' => 'required|integer|min:1',
                
                // Combined leftover product (optional)
                'create_option' => 'required|in:leftover,damage,none',
                // Leftover fields required only if create_option is 'leftover'
                'leftover_name' => 'required_if:create_option,leftover|string|max:255|nullable',
                'leftover_desc' => 'nullable|string',
                'leftover_supplier_id' => 'required_if:create_option,leftover|exists:suppliers,id|nullable',
                'leftover_category_id' => 'required_if:create_option,leftover|exists:product_categories,id|nullable',
                'leftover_sub_category_id' => 'nullable|exists:sub_categories,id',
                'leftover_gold_rate_id' => 'required_if:create_option,leftover|exists:gold_rates,id|nullable',
                'leftover_qty' => 'required_if:create_option,leftover|integer|min:1|nullable',
            ]);

            // Get the source products
            $productA = Product::find($request->source_products[0]);
            $productB = Product::find($request->source_products[1]);

            // Calculate combined leftovers
            $combinedLeftover = [
                'weight' => ($productA->weight - $request->product_a_weight_portion) + 
                            ($productB->weight - $request->product_b_weight_portion),
                'wastage_weight' => ($productA->wastage_weight - ($request->product_a_wastage_portion ?? 0)) + 
                                ($productB->wastage_weight - ($request->product_b_wastage_portion ?? 0)),
                'stone_weight' => ($productA->stone_weight - ($request->product_a_stone_portion ?? 0)) + 
                                ($productB->stone_weight - ($request->product_b_stone_portion ?? 0)),
                'making_charges' => ($productA->making_charges - ($request->product_a_charges_portion ?? 0)) + 
                                    ($productB->making_charges - ($request->product_b_charges_portion ?? 0))
            ];

            // Prepare leftover product data if option is selected
            $leftoverData = null;
            if ($request->create_option === 'leftover') {
                $leftoverData = [
                    'name' => $request->leftover_name,
                    'desc' => $request->leftover_desc,
                    'qty' => $request->leftover_qty,
                    'weight' => $combinedLeftover['weight'],
                    'wastage_weight' => $combinedLeftover['wastage_weight'],
                    'stone_weight' => $combinedLeftover['stone_weight'],
                    'making_charges' => $combinedLeftover['making_charges'],
                    'product_category_id' => $request->leftover_category_id,
                    'sub_category_id' => $request->leftover_sub_category_id,
                    'supplier_id' => $request->leftover_supplier_id,
                    'gold_rate_id' => $request->leftover_gold_rate_id,
                ];
            }

            // Store the merge request for approval
            $pendingMerge = ProductMergePending::create([
                'source_products_data' => [
                    'product_a' => $productA->toArray(),
                    'product_b' => $productB->toArray()
                ],
                'merge_details' => $request->all(),
                'merged_product_data' => [
                    'name' => $request->merged_product_name,
                    'desc' => $request->merged_product_desc,
                    'qty' => $request->merged_product_qty,
                    'product_category_id' => $request->merged_product_category_id,
                    'sub_category_id' => $request->merged_product_sub_category_id,
                    'supplier_id' => $request->merged_product_supplier_id,
                    'gold_rate_id' => $request->merged_product_gold_rate_id,
                ],
                'leftover_product_data' => $leftoverData,
                'created_by' => auth()->id(),
                'status' => 'pending'
            ]);

            return redirect()->route('products.merge.index')
                ->with('success', 'Merge request submitted for approval. Your request ID: '.$pendingMerge->id);

        } catch (\Exception $e) {
            Log::error('Product merge request error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Error submitting merge request: ' . $e->getMessage())
                ->withInput();
        }
    }

public function approvalIndex()
{
    if (!auth()->check() || auth()->user()->role->name !== 'superadmin') {
        abort(403, 'Unauthorized.');
    }

    $pendingMerges = ProductMergePending::with('creator')
        ->where('status', 'pending')
        ->latest()
        ->paginate(10);

    return view('products.product_approval.index', compact('pendingMerges'));
}

    public function approvalShow($id)
    {
        if (!auth()->check() || auth()->user()->role->name !== 'superadmin') {
            abort(403, 'Unauthorized.');
        }

        $pendingMerge = ProductMergePending::with('creator')->findOrFail($id);
        return view('products.product_approval.show', compact('pendingMerge'));
    }

    public function approve(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role->name !== 'superadmin') {
            abort(403, 'Unauthorized.');
        }


        DB::beginTransaction();

        try {
            $pendingMerge = ProductMergePending::findOrFail($id);
            $requestData = $pendingMerge->merge_details;

            // Get the products with locks to prevent concurrent modifications
            $productA = Product::lockForUpdate()->findOrFail($requestData['source_products'][0]);
            $productB = Product::lockForUpdate()->findOrFail($requestData['source_products'][1]);

            // Validate products are in stock
            if ($productA->qty <= 0 || $productB->qty <= 0) {
                DB::rollBack();
                Log::warning('Product merge failed: Products out of stock', [
                    'product_a_qty' => $productA->qty,
                    'product_b_qty' => $productB->qty
                ]);
                return redirect()->back()->with('error', 'One or both products are out of stock');
            }

            // Validate portions don't exceed available amounts
            $this->validatePortions($productA, (object)$requestData, 'a');
            $this->validatePortions($productB, (object)$requestData, 'b');

            // Create merge history record
            $mergeHistory = ProductMergeHistory::create([
                'merged_at' => now(),
                'merged_by' => auth()->id(),
                'merge_type' => $requestData['merge_type']
            ]);

            // Calculate combined leftovers
            $combinedLeftover = $this->calculateCombinedLeftovers($productA, $productB, (object)$requestData);

            // Record original product details in history
            $this->recordProductHistory($mergeHistory, $productA, 'source_a', (object)$requestData, 'a');
            $this->recordProductHistory($mergeHistory, $productB, 'source_b', (object)$requestData, 'b');

            // Create the merged product
            $mergedProduct = $this->createMergedProduct((object)$requestData, $productA, $productB);
            Log::info('Created merged product:', ['id' => $mergedProduct->id, 'name' => $mergedProduct->name]);

            // Record merged product in history
            $this->recordMergedProductHistory($mergeHistory, $mergedProduct, (object)$requestData);

            // Create combined leftover product if requested
            $leftoverProduct = null;

            if ($requestData['create_option'] === 'leftover' && $this->hasSignificantLeftover($combinedLeftover)) {
                $leftoverProduct = $this->createCombinedLeftoverProduct((object)$requestData, $combinedLeftover);
                Log::info('Created combined leftover product:', [
                    'id' => $leftoverProduct->id,
                    'name' => $leftoverProduct->name,
                ]);
                $this->recordLeftoverProductHistory($mergeHistory, $leftoverProduct, 'leftover');

            } elseif ($requestData['create_option'] === 'damage') {
                $lastBalance = GoldBalance::orderBy('created_at', 'desc')->first();
                $currentBalance = $lastBalance ? $lastBalance->gold_balance : 0;

                $newGoldWeight = $combinedLeftover['weight'];
                $newBalance = $currentBalance + $newGoldWeight;

                GoldBalance::create([
                    'description' => "Damage gold (Merge)",
                    'gold_in' => $newGoldWeight,
                    'gold_out' => 0,
                    'gold_balance' => $newBalance,
                ]);

                Log::info('Added damage gold to GoldBalance table.', [
                    'amount' => $newGoldWeight,
                    'new_balance' => $newBalance,
                ]);
            }

            // Set original product quantities to 0 and mark as inactive
            $productA->update([
                'qty' => 0,
                'status' => 'inactive'
            ]);

            $productB->update([
                'qty' => 0,
                'status' => 'inactive'
            ]);

            // Update the pending merge record
            $pendingMerge->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            // Send notification to creator
            // $pendingMerge->creator->notify(new MergeRequestProcessed($pendingMerge, true));

            DB::commit();
            Log::info('Product merge completed successfully');

            $message = "Merge request approved! New product '{$mergedProduct->name}' created.";
            if ($leftoverProduct) {
                $message .= " Combined leftover product '{$leftoverProduct->name}' created from remaining materials.";
            }

            return redirect()->route('products.merge.approval.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product merge approval error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Error approving merge: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role->name !== 'superadmin') {
            abort(403, 'Unauthorized.');
        }

        $pendingMerge = ProductMergePending::findOrFail($id);
        $pendingMerge->update([
            'status' => 'rejected',
            'rejection_reason' => null
        ]);

        // Send notification to creator
        // $pendingMerge->creator->notify(new MergeRequestProcessed($pendingMerge, false));

        return redirect()->route('products.merge.approval.index')
            ->with('success', 'Merge request rejected successfully');
    }


    private function validatePortions($product, $request, $productKey)
    {
        $weightPortion = $request->{"product_{$productKey}_weight_portion"};
        $wastagePortion = $request->{"product_{$productKey}_wastage_portion"} ?? 0;
        $stonePortion = $request->{"product_{$productKey}_stone_portion"} ?? 0;
        $chargesPortion = $request->{"product_{$productKey}_charges_portion"} ?? 0;

        if ($weightPortion > $product->weight) {
            throw new \Exception("Weight portion for {$product->name} exceeds available weight ({$product->weight}g)");
        }

        if ($wastagePortion > $product->wastage_weight) {
            throw new \Exception("Wastage portion for {$product->name} exceeds available wastage weight ({$product->wastage_weight}g)");
        }

        if ($stonePortion > $product->stone_weight) {
            throw new \Exception("Stone portion for {$product->name} exceeds available stone weight ({$product->stone_weight}g)");
        }

        if ($chargesPortion > $product->making_charges) {
            throw new \Exception("Charges portion for {$product->name} exceeds available making charges ({$product->making_charges})");
        }
    }

    private function calculateCombinedLeftovers($productA, $productB, $request)
    {
        // Calculate leftovers from product A
        $leftoverA = [
            'weight' => $productA->weight - $request->product_a_weight_portion,
            'wastage_weight' => $productA->wastage_weight - ($request->product_a_wastage_portion ?? 0),
            'stone_weight' => $productA->stone_weight - ($request->product_a_stone_portion ?? 0),
            'making_charges' => $productA->making_charges - ($request->product_a_charges_portion ?? 0),
        ];

        // Calculate leftovers from product B
        $leftoverB = [
            'weight' => $productB->weight - $request->product_b_weight_portion,
            'wastage_weight' => $productB->wastage_weight - ($request->product_b_wastage_portion ?? 0),
            'stone_weight' => $productB->stone_weight - ($request->product_b_stone_portion ?? 0),
            'making_charges' => $productB->making_charges - ($request->product_b_charges_portion ?? 0),
        ];

        // Combine leftovers
        return [
            'weight' => $leftoverA['weight'] + $leftoverB['weight'],
            'wastage_weight' => $leftoverA['wastage_weight'] + $leftoverB['wastage_weight'],
            'stone_weight' => $leftoverA['stone_weight'] + $leftoverB['stone_weight'],
            'making_charges' => $leftoverA['making_charges'] + $leftoverB['making_charges'],
        ];
    }

    private function hasSignificantLeftover($leftover)
    {
        // Check if leftover has significant weight (more than 0.1g)
        return $leftover['weight'] > 0.1 || 
               $leftover['wastage_weight'] > 0.1 || 
               $leftover['stone_weight'] > 0.1 || 
               $leftover['making_charges'] > 0.1;
    }

    private function createMergedProduct($request, $productA, $productB)
    {
        $mergedWeight = $request->product_a_weight_portion + $request->product_b_weight_portion;
        $mergedWastage = ($request->product_a_wastage_portion ?? 0) + ($request->product_b_wastage_portion ?? 0);
        $mergedStone = ($request->product_a_stone_portion ?? 0) + ($request->product_b_stone_portion ?? 0);
        $mergedCharges = ($request->product_a_charges_portion ?? 0) + ($request->product_b_charges_portion ?? 0);

        // Calculate amount based on merged properties
        $goldRate = \App\Models\GoldRate::find($request->merged_product_gold_rate_id);
        $amount = ($mergedWeight * $goldRate->rate) + $mergedCharges;

        return Product::create([
            'name' => $request->merged_product_name,
            'desc' => $request->merged_product_desc,
            'qty' => $request->merged_product_qty,
            'weight' => $mergedWeight,
            'wastage_weight' => $mergedWastage,
            'stone_weight' => $mergedStone,
            'making_charges' => $mergedCharges,
            'amount' => 0,
            'product_category_id' => $request->merged_product_category_id,
            'sub_category_id' => $request->merged_product_sub_category_id,
            'supplier_id' => $request->merged_product_supplier_id,
            'gold_rate_id' => $request->merged_product_gold_rate_id,
            'status' => 'active',
            'is_approved' => 1
        ]);
    }

    private function createCombinedLeftoverProduct($request, $combinedLeftover)
    {
        $goldRateId = $request->leftover_gold_rate_id;
        $goldRate = \App\Models\GoldRate::find($goldRateId);
        $amount = ($combinedLeftover['weight'] * $goldRate->rate) + $combinedLeftover['making_charges'];

        return Product::create([
            'name' => $request->leftover_name,
            'desc' => $request->leftover_desc,
            'qty' => $request->leftover_qty,
            'weight' => $combinedLeftover['weight'],
            'wastage_weight' => $combinedLeftover['wastage_weight'],
            'stone_weight' => $combinedLeftover['stone_weight'],
            'making_charges' => $combinedLeftover['making_charges'],
            'amount' => 0,
            'product_category_id' => $request->leftover_category_id,
            'sub_category_id' => $request->leftover_sub_category_id,
            'supplier_id' => $request->leftover_supplier_id,
            'gold_rate_id' => $goldRateId,
            'status' => 'active',
            'is_approved' => 1
        ]);
    }

    private function recordProductHistory($mergeHistory, $product, $type, $request, $productKey)
    {
        $portionData = [
            'name' => $product->name,
            'product_no' => $product->product_no,
            'original_qty' => $product->qty,
            'original_weight' => $product->weight,
            'original_wastage' => $product->wastage_weight,
            'original_stone' => $product->stone_weight,
            'original_charges' => $product->making_charges,
            'portion_weight' => $request->{"product_{$productKey}_weight_portion"},
            'portion_wastage' => $request->{"product_{$productKey}_wastage_portion"} ?? 0,
            'portion_stone' => $request->{"product_{$productKey}_stone_portion"} ?? 0,
            'portion_charges' => $request->{"product_{$productKey}_charges_portion"} ?? 0,
        ];

        return $mergeHistory->details()->create([
            'product_id' => $product->id,
            'type' => $type,
            'product_data' => json_encode($portionData)
        ]);
    }

    private function recordMergedProductHistory($mergeHistory, $mergedProduct, $request)
    {
        $mergedData = [
            'name' => $mergedProduct->name,
            'product_no' => $mergedProduct->product_no,
            'qty' => $mergedProduct->qty,
            'weight' => $mergedProduct->weight,
            'wastage_weight' => $mergedProduct->wastage_weight,
            'stone_weight' => $mergedProduct->stone_weight,
            'making_charges' => $mergedProduct->making_charges,
            'amount' => $mergedProduct->amount,
            'category_id' => $mergedProduct->product_category_id,
            'gold_rate_id' => $mergedProduct->gold_rate_id,
        ];

        return $mergeHistory->details()->create([
            'product_id' => $mergedProduct->id,
            'type' => 'merged',
            'product_data' => json_encode($mergedData)
        ]);
    }

    private function recordLeftoverProductHistory($mergeHistory, $leftoverProduct, $type)
    {
        $leftoverData = [
            'name' => $leftoverProduct->name,
            'product_no' => $leftoverProduct->product_no,
            'qty' => $leftoverProduct->qty,
            'weight' => $leftoverProduct->weight,
            'wastage_weight' => $leftoverProduct->wastage_weight,
            'stone_weight' => $leftoverProduct->stone_weight,
            'making_charges' => $leftoverProduct->making_charges,
            'amount' => $leftoverProduct->amount,
            'category_id' => $leftoverProduct->product_category_id,
            'gold_rate_id' => $leftoverProduct->gold_rate_id,
        ];

        return $mergeHistory->details()->create([
            'product_id' => $leftoverProduct->id,
            'type' => $type,
            'product_data' => json_encode($leftoverData)
        ]);
    }

    public function history()
    {
        $mergeHistory = ProductMergeHistory::with(['details.product', 'mergedBy'])->latest()->paginate(10);
        return view('products.merge.history', compact('mergeHistory'));
    }

    public function show($id)
    {
        $mergeHistory = ProductMergeHistory::with(['details.product', 'mergedBy'])->findOrFail($id);
        return view('products.merge.show', compact('mergeHistory'));
    }
}