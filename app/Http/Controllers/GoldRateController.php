<?php

namespace App\Http\Controllers;

use App\Models\GoldRate;
use Illuminate\Http\Request;

class GoldRateController extends Controller
{
    public function index()
    {
        $goldRates = GoldRate::all();
        return view('gold_rates.index', compact('goldRates'));
    }

    public function createOrEdit(GoldRate $goldRate = null)
    {
        return view('gold_rates.create_edit', compact('goldRate'));
    }

    public function storeOrUpdate(Request $request, GoldRate $goldRate = null)
    {
        $rules = [
            'type' => 'required|in:gold,silver,goldpcs,silverpcs',
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'percentage' => 'nullable|min:0'
        ];

        if ($request->type === 'gold') {
            $rules['rate_per_pawn'] = 'required|numeric|min:0';          
        }

    
        $validated = $request->validate($rules);

        // If type is goldpcs, set rate_per_pawn same as rate
        if ($request->type === 'goldpcs') {
            $validated['rate_per_pawn'] = $validated['rate'];
        }


        if ($goldRate && $goldRate->exists) {
            $goldRate->fill($validated);

            if (!$goldRate->isDirty()) {
                $goldRate->touch(); // Only timestamp changes
            } else {
                $goldRate->save();
            }

            return redirect()->route('gold_rates.index')->with('success', 'Rate updated successfully.');
        } else {
            GoldRate::create($validated);
            return redirect()->route('gold_rates.index')->with('success', 'Rate created successfully.');
        }

    }


    public function destroy(GoldRate $goldRate)
    {
        $goldRate->delete();
        return redirect()->route('gold_rates.index')->with('success', 'Gold rate deleted successfully.');
    }
}
