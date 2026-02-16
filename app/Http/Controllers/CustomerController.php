<?php

// app/Http/Controllers/CustomerController.php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Religion filter
        if ($request->filled('religion')) {
            $query->where('religion', $request->religion);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('tel', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Latest first
        $query->orderBy('created_at', 'desc');

        // Pagination
        $customers = $query->paginate(10)->withQueryString();

        $religions = [
            'tamil' => 'Hindu',
            'muslim' => 'Islam',
            'christian' => 'Christian',
            'buddhist' => 'Buddhist'
        ];

        return view('customers.index', compact('customers', 'religions'));
    }



    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
    'name' => 'required',
    'address' => 'nullable|string',
    'city' => 'nullable|string',
    'tel' => 'required|digits:10|unique:customers,tel',
    'email' => 'nullable|string',
    'religion' => 'required|string',
]);


        $data = $request->all();

        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $data[$key] = '';
            }
        }

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.create', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'tel' => 'required|digits:10|string|unique:customers,tel,' . $customer->id,
            'email' => 'nullable|string',
            'religion' => 'nullable|string',
        ]);

        $data = $request->all();

        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $data[$key] = '';
            }
        }

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
