<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('suppliers.index', compact('suppliers'));
    }

    public function createOrEdit(Supplier $supplier = null)
    {
        return view('suppliers.create_edit', compact('supplier'));
    }

    public function storeOrUpdate(Request $request, Supplier $supplier = null)
    {
        $validatedData = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'short_code' => 'required|string|max:4',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($supplier) {
            $supplier->update($validatedData);
            return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
        } else {
            Supplier::create($validatedData);
            return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
        }
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
