<?php

namespace App\Http\Controllers;

use App\Models\GoldRate;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductWeightAdjust;
use App\Models\SubCategory;
use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['goldRate:id,name', 'category:id,name', 'subCategory:id,name'])
            ->where('status', 'active')
            ->where('is_approved', 1);

        // Search by product_no, name, category, etc.
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_no', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                ->orWhereHas('subCategory', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $products = $query->orderByRaw('CAST(product_no AS UNSIGNED) DESC')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function filterGoldRates($type)
    {
        // Determine which related PCS type to include
        $pcsType = $type === 'gold' ? 'goldpcs' : 'silverpcs';

        // Fetch both main type and PCS type
        $goldRates = GoldRate::whereIn('type', [$type, $pcsType])->get();

        return response()->json($goldRates);
    }


    public function createOrEdit(Product $product = null)
    {
        $categories = ProductCategory::all();
        $subcategories = SubCategory::all();
        $suppliers = Supplier::all();
        $productType = $product->product_type ?? old('product_type', 'gold');
        $goldRates = GoldRate::where('type', $productType)->get();


        return view('products.create_edit', compact('product', 'categories', 'goldRates', 'suppliers', 'subcategories')); 
    }


    // SuperAdmin store function
    public function AdminStoreOrUpdate(Request $request, Product $product = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'qty' => 'required|integer',
            'weight' => 'required|numeric',
            'type' => 'required|integer',
            'product_type' => 'required|string|in:gold,silver',
            'product_category_id' => 'required|exists:product_categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'wastage_weight' => 'nullable|numeric',
            'stone_weight' => 'nullable|numeric',
            'making_charges' => 'nullable|numeric',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'gold_rate_id' => 'required|integer',
            'product_no' => [
                                'nullable',
                                Rule::unique('products', 'product_no')->ignore($product?->id),
                            ],

        ];

        $request->validate($rules);

        $data = $request->only([
            'name',
            'qty',
            'weight',
            'type',
            'product_type',
            'product_category_id',
            'sub_category_id',
            'supplier_id',
            'gold_rate_id',
            'wastage_weight',
            'stone_weight',
            'making_charges'
        ]);

        $data['status'] = 'active';
        $data['is_approved'] = 1; // superadmin products auto-approved
        $data['type'] = (int) $request->type;

        if (!$product) {
            $data['created_by'] = auth()->id();
        }

        // Handle product_no
        if ($request->filled('product_no')) {
            $data['product_no'] = $request->product_no;
        } else {
            do {
                $data['product_no'] = \App\Models\ProductNumberSequence::getNextProductNumber();
            } while (\App\Models\Product::where('product_no', $data['product_no'])->exists());
        }


        if ($product) {
            $product->update($data);
            return redirect()->route('products.index')
                ->with('show_approval_popup', true)
                ->with('popup_type', 'update')
                ->with('popup_message', 'Product updated successfully!')
                ->with('product_data', $product);
        } else {
            $newProduct = Product::create($data);
            return redirect()->route('products.index')
                ->with('show_approval_popup', true)
                ->with('popup_type', 'create')
                ->with('popup_message', 'Product created successfully!')
                ->with('product_data', $newProduct);
        }
    }



    public function storeOrUpdate(Request $request, Product $product = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'qty' => 'required|integer',
            'weight' => 'required|numeric',
            'type' => 'required|integer',
            'product_type' => 'required|string|in:gold,silver',
            'product_category_id' => 'required|exists:product_categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'wastage_weight' => 'nullable|numeric',
            'stone_weight' => 'nullable|numeric',
            'making_charges' => 'nullable|numeric',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'product_no' => [
                                'nullable',
                                Rule::unique('products', 'product_no')->ignore($product?->id),
                            ],

            'gold_rate_id' => 'required|integer',
        ];

        // Only require gold_rate_id if product_type is gold
        // if ($request->product_type === 'gold') {
        //     $rules['gold_rate_id'] = 'required|exists:gold_rates,id';
        // }

        $request->validate($rules);

        // Prepare product data
        $data = $request->only([
            'name',
            'qty',
            'weight',
            'type',
            'product_type',
            'product_category_id',
            'sub_category_id',
            'supplier_id',
            'gold_rate_id',
            'wastage_weight',
            'stone_weight',
            'making_charges'
        ]);

        $data['status'] = 'active';
        $data['is_approved'] = 0;
        $data['type'] = (int) $request->type;

        if (!$product) {
            $data['created_by'] = auth()->id();
        }

        // Handle product_no
        if ($request->filled('product_no')) {
            $data['product_no'] = $request->product_no;
        } else {
            do {
                $data['product_no'] = \App\Models\ProductNumberSequence::getNextProductNumber();
            } while (\App\Models\Product::where('product_no', $data['product_no'])->exists());
        }


        if ($product) {
            $product->update($data);
            return redirect()->route('products.index')
                ->with('show_approval_popup', true)
                ->with('popup_type', 'update')
                ->with('popup_message', 'Product updated successfully! Your changes are pending approval from the administrator.')
                ->with('product_data', $product);
        } else {
            $newProduct = Product::create($data);
            return redirect()->route('products.index')
                ->with('show_approval_popup', true)
                ->with('popup_type', 'create')
                ->with('popup_message', 'Product created successfully! Your product is pending approval from the administrator.')
                ->with('product_data', $newProduct);
        }
    }



    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function generateBarcode($productNo)
    {
        try {
            $generator = new BarcodeGeneratorPNG();
            
            // Printer-optimized settings
            $widthFactor = 2;  // Thicker bars for better thermal printing
            $height = 50;      // Taller for better scanning
            $foregroundColor = [0, 0, 0]; // Pure black
            
            $barcode = $generator->getBarcode(
                $productNo, 
                $generator::TYPE_CODE_128, 
                $widthFactor, 
                $height,
                $foregroundColor
            );
            
            return response($barcode)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            // Fallback remains the same
            $image = imagecreate(200, 50);
            $background = imagecolorallocate($image, 255, 255, 255);
            $textColor = imagecolorallocate($image, 0, 0, 0);
            imagestring($image, 3, 10, 10, $productNo, $textColor);
            
            ob_start();
            imagepng($image);
            $imageData = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);
            
            return response($imageData)
                ->header('Content-Type', 'image/png');
        }
    }

    public function generateThermalBarcode($productNo)
    {
        try {
            $generator = new BarcodeGeneratorPNG();
            
            // Zebra ZD230 optimized settings
            $widthFactor = 3;  // Thicker bars
            $height = 80;      // Taller barcode
            $foregroundColor = [0, 0, 0]; // Pure black
            
            $barcode = $generator->getBarcode(
                $productNo, 
                $generator::TYPE_CODE_128, 
                $widthFactor, 
                $height,
                $foregroundColor
            );
            
            return response($barcode)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            // Simple fallback with larger text
            $image = imagecreate(300, 100);
            $background = imagecolorallocate($image, 255, 255, 255);
            $textColor = imagecolorallocate($image, 0, 0, 0);
            imagestring($image, 5, 20, 40, $productNo, $textColor);
            
            ob_start();
            imagepng($image);
            $imageData = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);
            
            return response($imageData)
                ->header('Content-Type', 'image/png');
        }
    }

    public function product_aprroval_index(){
        $products = Product::where('is_approved', '0')
                    ->orderByRaw('CAST(product_no AS UNSIGNED) DESC')
                    ->get();

        return view('admin-approval.index',compact('products'));
    }

    public function productApproval($id){
        $product = Product::find($id);

        $data['is_approved'] = 1 ;
        $product->update($data);

        return redirect()->route('products.approval.index')->with('success', 'Product Approved.');
    }

    public function productReject($id){
        $product = Product::find($id);

        $data['is_approved'] = 2 ;
        $product->update($data);

        return redirect()->route('products.approval.index')->with('success', 'Product Rejected.');
    }

    public function printLabelView($product_no)
    {
        $product = Product::select(
            'id',
            'product_no',
            'name',
            'weight',
            'wastage_weight',
            'stone_weight',
            'making_charges',
            'product_type',
            'gold_rate_id',
            'supplier_id'
        )
        ->with(['goldRate:id,name,rate,rate_per_pawn,type', 'supplier:id,short_code'])
        ->where('product_no', $product_no)
        ->firstOrFail();

        return view('products.label', compact('product'));
    }


    public function weightAdjustShow(Request $request)
    {
        $query = Product::with(['goldRate:id,name', 'category:id,name', 'subCategory:id,name'])
            ->where('status', 'active')
            // ->where('is_approved', 1)
            ->where('type', 1);

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('product_no', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhereHas('category', fn($cat) => $cat->where('name', 'like', "%{$search}%"))
                ->orWhereHas('subCategory', fn($sub) => $sub->where('name', 'like', "%{$search}%"));
            });
        }

        // Now paginate the final query
        $products = $query->paginate(10);

        return view('products.weight_adjust', compact('products'));
    }

    public function adjustWeight(Request $request, Product $product)
    {
        $request->validate([
            'value' => 'required|numeric',
            'type'  => 'required|in:add,minus',
            'note'  => 'nullable|string'
        ]);

        $value = (float) $request->value;

        // Adjust the product weight
        if ($request->type === 'add') {
            $product->weight += $value;
        } else {
            $product->weight -= $value;
            if ($product->weight < 0) {
                $product->weight = 0;
            }
        }

        if (auth()->user()->role->name !== 'superadmin') {
            $product->is_approved = 0;
        }

        $product->created_by = auth()->id();
        $product->save();

        ProductWeightAdjust::create([
            'product_id'  => $product->id,
            'weight'      => $value,
            'adjust_type' => $request->type,
            'note'        => $request->note,
            'processed_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function weightAdjustDetailShow($id)
    {
        // Eager load the relationship to make sure it's not null
        $product = Product::with('productWeightAdjusts')->findOrFail($id);

        return view('products.weight_adjust_detail', compact('product'));
    }


    public function productDisable($id){
        $product = Product::find($id);

        $data['status'] = 'disabled' ;
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product Disabled.');
    }

    public function productActive($id){
        $product = Product::find($id);

        $data['status'] = 'active' ;
        $product->update($data);

        return redirect()->route('products.disable.show')->with('success', 'Product Activated.');
    }

    public function disabledProductShow(Request $request){
         $query = Product::with(['goldRate:id,name', 'category:id,name', 'subCategory:id,name'])
            ->where('status', 'disabled')
            ->where('is_approved', 1);

        // Search by product_no, name, category, etc.
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_no', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                ->orWhereHas('subCategory', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $products = $query->orderByRaw('CAST(product_no AS UNSIGNED) DESC')
            ->paginate(10)
            ->withQueryString();

        return view('products.disabled_products', compact('products'));
    }


}