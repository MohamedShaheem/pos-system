<?php

namespace App\Http\Controllers;

use App\Models\StockAudit;
use App\Models\StockAuditItem;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAuditController extends Controller
{
    // List all audits
    public function index()
    {
        $audits = StockAudit::with(['category', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('stock-audits.index', compact('audits'));
    }

    // Start new audit
    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('stock-audits.create', compact('categories'));
    }

    // Store new audit
    public function store(Request $request)
    {
        // Validate audit type first
        $request->validate([
            'audit_type' => 'required|in:category,all',
            'notes' => 'nullable|string'
        ]);

        // Conditionally validate category based on audit type
        if ($request->audit_type === 'category') {
            $request->validate([
                'product_category_id' => 'required|exists:product_categories,id',
            ]);
        }

        $auditType = $request->audit_type;
        
        // Calculate expected count based on audit type
        if ($auditType === 'all') {
            $expectedCount = Product::where('qty', '>', 0)
                ->where('is_approved', 1)
                ->whereIn('product_type', ['gold', 'silver'])
                ->where('status', 'active')
                ->count();
            $categoryId = null;
        } else {
            $expectedCount = Product::where('product_category_id', $request->product_category_id)
                ->where('qty', '>', 0)
                ->where('is_approved', 1)
                ->whereIn('product_type', ['gold', 'silver'])
                ->where('status', 'active')
                ->count();
            $categoryId = $request->product_category_id;
        }

        if ($expectedCount === 0) {
            return back()->with('error', 'No products in stock to audit!');
        }

        $audit = StockAudit::create([
            'audit_type' => $auditType,
            'product_category_id' => $categoryId,
            'created_by' => Auth::id(),
            'status' => 'in_progress',
            'expected_count' => $expectedCount,
            'scanned_count' => 0,
            'notes' => $request->notes,
            'started_at' => now()
        ]);

        return redirect()->route('stock-audits.scan', $audit->id)
            ->with('success', 'Stock audit started successfully!');
    }

    // Scanning interface
    public function scan($id)
    {
        $audit = StockAudit::with(['category', 'items'])->findOrFail($id);
        
        if ($audit->status !== 'in_progress') {
            return redirect()->route('stock-audits.show', $id)
                ->with('warning', 'This audit is already ' . $audit->status);
        }

        return view('stock-audits.scan', compact('audit'));
    }

    // Process scanned barcode
    public function scanProduct(Request $request, $id)
    {
        $request->validate([
            'product_no' => 'required'
        ]);

        $audit = StockAudit::findOrFail($id);

        if ($audit->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Audit is not in progress'
            ], 400);
        }

        try {
            // Check if already scanned
            $existing = StockAuditItem::where('stock_audit_id', $id)
                ->where('product_no', $request->product_no)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already scanned!',
                    'type' => 'duplicate'
                ]);
            }

            // Verify product exists and belongs to category
            $product = Product::where('product_no', $request->product_no)->first();
            
            $warningMessage = null;
            if (!$product) {
                $warningMessage = 'Product not found in system!';
            } elseif ($audit->audit_type === 'category' && $product->product_category_id != $audit->product_category_id) {
                $warningMessage = 'Product belongs to different category!';
            } elseif ($product->qty == 0) {
                $warningMessage = 'Product is marked as sold out!';
            }

            // Create audit item
            StockAuditItem::create([
                'stock_audit_id' => $id,
                'product_no' => $request->product_no,
                'scanned_at' => now(),
                'scanned_by' => auth()->id()
            ]);

            // Update scanned count
            $audit->increment('scanned_count');

            return response()->json([
                'success' => true,
                'message' => 'Product scanned successfully',
                'warning' => $warningMessage,
                'product' => $product,
                'scanned_count' => $audit->fresh()->scanned_count,
                'expected_count' => $audit->expected_count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error scanning product: ' . $e->getMessage()
            ], 500);
        }
    }

    // Complete audit and show results
    public function complete($id)
    {
        $audit = StockAudit::findOrFail($id);
        
        $audit->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->route('stock-audits.show', $id)
            ->with('success', 'Audit completed successfully');
    }

    // View audit results
    public function show($id)
    {
        $audit = StockAudit::with(['category', 'creator', 'items'])->findOrFail($id);
        
        $missingProducts = $audit->getMissingProducts();
        $extraProducts = $audit->getExtraProducts();
        $scannedProducts = $audit->items()->with('scanner')->get();

        return view('stock-audits.show', compact('audit', 'missingProducts', 'extraProducts', 'scannedProducts'));
    }

    // Delete specific scanned item
    public function deleteItem($auditId, $itemId)
    {
        $item = StockAuditItem::where('stock_audit_id', $auditId)
            ->where('id', $itemId)
            ->firstOrFail();
        
        $item->delete();
        
        $audit = StockAudit::findOrFail($auditId);
        $audit->decrement('scanned_count');

        return response()->json([
            'success' => true,
            'message' => 'Item removed from audit'
        ]);
    }

    public function deleteAudit($id)
    {
        $item = StockAudit::findOrFail($id);

        $item->delete();

        return redirect()->route('stock-audits.index')
                        ->with('success', 'Audit deleted successfully.');
    }

}