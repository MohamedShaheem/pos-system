<?php

namespace App\Http\Controllers;

use App\Models\ChitCustomer;
use Illuminate\Http\Request;

class ChitCustomerController extends Controller
{
    public function index()
    {
        $chitCustomers = ChitCustomer::all();
        return view('chit-customers.index', compact('chitCustomers'));
    }

    public function createOrEdit(ChitCustomer $chitCustomer = null)
    {
        return view('chit-customers.create_edit', compact('chitCustomer'));
    }

    public function storeOrUpdate(Request $request, ChitCustomer $chitCustomer = null)
    {
        $request->validate([
            'customer_no' => 'nullable|unique:chit_customers,customer_no,' . ($chitCustomer ? $chitCustomer->id : ''),
            'name' => 'required',
            'address' => 'nullable',
            'city' => 'nullable',
            'tel' => 'nullable'
        ]);

        if ($chitCustomer) {
            $chitCustomer->update($request->all());
            return redirect()->route('chit-customers.index')
                ->with('success', 'Chit Customer updated successfully.');
        } else {
            ChitCustomer::create($request->all());
            return redirect()->route('chit-customers.index')
                ->with('success', 'Chit Customer created successfully.');
        }
    }

    public function destroy(ChitCustomer $chitCustomer)
    {
        if ($chitCustomer->chitDetails()->exists()) {
            return redirect()->route('chit-customers.index')
                ->with('error', 'Cannot delete customer as they are assigned to one or more chits.');
        }

        $chitCustomer->delete();
        return redirect()->route('chit-customers.index')
            ->with('success', 'Chit Customer deleted successfully.');
    }
} 