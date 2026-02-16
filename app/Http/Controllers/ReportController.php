<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function stockLedgerSummary()
    {
        $categories = ProductCategory::with('subCategories')->orderBy('sort_order')->get();

        $goldData = collect();
        $silverData = collect();

        foreach ($categories as $category) {
            // Handle main category products
            $this->addCategoryProducts($category, $goldData, 'gold');
            $this->addCategoryProducts($category, $silverData, 'silver');

            // Handle subcategory products
            foreach ($category->subCategories as $subCategory) {
                $this->addSubCategoryProducts($subCategory, $goldData, 'gold', $category);
                $this->addSubCategoryProducts($subCategory, $silverData, 'silver', $category);
            }
        }

        return view('reports.stock_ledger_summary', compact('goldData', 'silverData'));
    }

    private function addCategoryProducts($category, &$dataCollection, $type)
    {
        $products = Product::where('product_category_id', $category->id)
                            ->where('is_approved', 1)
                            ->where('status', 'active')
                            ->where('product_type', $type)
                            ->get();

        if ($products->count() > 0) {
            $totalWeight = $products->sum('weight');
            $stoneWeight = $products->sum('stone_weight');
            $netWeight = $totalWeight - $stoneWeight;

            $dataCollection->push([
                'sort_order'   => $category->sort_order,
                'short_code'   => $category->short_code,
                'name'         => $category->name,
                'total_items'  => $products->count(),
                'weight'       => number_format($totalWeight, 2),
                'stone_weight' => number_format($stoneWeight, 2),
                'net_weight'   => number_format($netWeight, 2),
                'is_sub'       => false,
            ]);
        }
    }

private function addSubCategoryProducts($subCategory, &$dataCollection, $type, $parentCategory)
{
    $products = Product::where('sub_category_id', $subCategory->id)
                        ->where('is_approved', 1)
                        ->where('status', 'active')
                        ->where('product_type', $type)
                        ->get();

    if ($products->count() > 0) {
        // Calculate values only for display, but DO NOT add to total sums
        $totalWeight = $products->sum('weight');
        $stoneWeight = $products->sum('stone_weight');
        $netWeight = $totalWeight - $stoneWeight;

        $dataCollection->push([
            'sort_order'   => $parentCategory->sort_order,
            'short_code'   => $subCategory->name, // Or subcategory short_code if exists
            'name'         => $subCategory->name,
            'total_items'  => $products->count(),
            'weight'       => number_format($totalWeight, 2),
            'stone_weight' => number_format($stoneWeight, 2),
            'net_weight'   => number_format($netWeight, 2),
            'is_sub'       => true,
            'exclude_from_total' => true, // NEW FLAG
        ]);
    }
}


}