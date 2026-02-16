<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = TaxRate::all();
        return view('tax_rates.index', compact('taxRates'));
    }

    public function createOrEdit(TaxRate $taxRate = null)
    {
        return view('tax_rates.create_edit', compact('taxRate'));
    }

    public function storeOrUpdate(Request $request, TaxRate $taxRate = null)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
        ]);

        if ($taxRate) {
            $taxRate->update($request->only('name', 'rate'));
            return redirect()->route('tax_rates.index')->with('success', 'Tax Rate updated successfully.');
        } else {
            TaxRate::create($request->only('name', 'rate'));
            return redirect()->route('tax_rates.index')->with('success', 'Tax Rate created successfully.');
        }
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();
        return redirect()->route('tax_rates.index')->with('success', 'Tax Rate deleted successfully.');
    }
}
