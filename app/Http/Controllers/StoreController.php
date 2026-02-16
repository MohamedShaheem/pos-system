<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::all();
        return view('stores.index', compact('stores'));
    }

    // Create and Edit handled on the same page
    public function createOrEdit(Store $store = null)
    {
        return view('stores.create_edit', compact('store'));
    }

    public function storeOrUpdate(Request $request, Store $store = null)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone_no_1' => 'required|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        // Store logo if uploaded
        $logoPath = $request->file('logo') ? $request->file('logo')->store('logos') : $store->logo ?? null;

        if ($store) {
            // Update existing store
            $store->update([
                'name' => $request->name,
                'sub_name' => $request->sub_name,
                'address' => $request->address,
                'phone_no_1' => $request->phone_no_1,
                'phone_no_2' => $request->phone_no_2,
                'logo' => $logoPath,
            ]);
            return redirect()->route('stores.index')->with('success', 'Store updated successfully.');
        } else {
            // Create new store
            Store::create([
                'name' => $request->name,
                'sub_name' => $request->sub_name,
                'address' => $request->address,
                'phone_no_1' => $request->phone_no_1,
                'phone_no_2' => $request->phone_no_2,
                'logo' => $logoPath,
            ]);
            return redirect()->route('stores.index')->with('success', 'Store created successfully.');
        }
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return redirect()->route('stores.index')->with('success', 'Store deleted successfully.');
    }
}
