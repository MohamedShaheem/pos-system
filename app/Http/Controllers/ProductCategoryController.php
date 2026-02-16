<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::all();
        return view('categories.index', compact('categories'));
    }

    public function createOrEdit(ProductCategory $category = null)
    {
        return view('categories.create_edit', compact('category'));
    }

    public function storeOrUpdate(Request $request, ProductCategory $category = null)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_code' => 'nullable|string|max:5',
            'sort_order' => 'nullable|integer',
        ]);

        if ($category) {
            $category->update($validated);
            return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
        } else {
            ProductCategory::create($validated);
            return redirect()->route('categories.index')->with('success', 'Category created successfully.');
        }
    }


    public function destroy(ProductCategory $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }



    public function subCategoryIndex()
    {
        $subcategories = SubCategory::with('category')->latest()->get();
        return view('categories.sub-categories.index', compact('subcategories'));
    }

    // Show create or edit form
    public function createOrEditSubCategory(SubCategory $subcategory = null)
    {
        $categories = ProductCategory::all();
        return view('categories.sub-categories.create_edit', compact('subcategory', 'categories'));
    }

    // Store or update logic
    public function storeOrUpdateSubCategory(Request $request, SubCategory $subcategory = null)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
        ]);

        SubCategory::updateOrCreate(
            ['id' => $subcategory->id ?? null],
            $validated
        );

        return redirect()->route('subcategories.index')->with('success', 'Sub Category saved successfully.');
    }

    // Delete subcategory
    public function destroySubCategory(SubCategory $subcategory)
    {
        $subcategory->delete();
        return redirect()->route('subcategories.index')->with('success', 'Sub Category deleted successfully.');
    }
}
