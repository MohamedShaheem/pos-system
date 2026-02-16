<?php

namespace App\Http\Controllers;

use App\Models\Chit;
use App\Models\ChitDetail;
use App\Models\ChitCustomer;
use Illuminate\Http\Request;

class ChitController extends Controller
{
    public function index()
    {
        $chits = Chit::with('chitDetails.chitCustomer')->get();
        return view('chits.index', compact('chits'));
    }

    public function create()
    {
        return view('chits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'month_from' => 'nullable|string',
            'month_to' => 'nullable|string',
            'total_amount' => 'nullable|numeric',
            'amount_per_month' => 'required|numeric',
        ]);

        Chit::create($request->all());
        return redirect()->route('chits.index')->with('success', 'Chit created successfully.');
    }

    public function edit(Chit $chit)
    {
        return view('chits.create', compact('chit'));
    }

    public function update(Request $request, Chit $chit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'month_from' => 'nullable|string',
            'month_to' => 'nullable|string',
            'total_amount' => 'nullable|numeric',
            'amount_per_month' => 'required|numeric',
        ]);

        $chit->update($request->all());
        return redirect()->route('chits.index')->with('success', 'Chit updated successfully.');
    }

    public function destroy(Chit $chit)
    {
        $chit->delete();
        return redirect()->route('chits.index')->with('success', 'Chit deleted successfully.');
    }

    public function show(Chit $chit)
    {
        $chitCustomers = ChitCustomer::whereDoesntHave('chitDetails', function ($query) use ($chit) {
            $query->where('chit_id', $chit->id);
        })->get(); 

        $chitDetails = $chit->chitDetails; // Get chit details

        return view('chits.show', compact('chit', 'chitCustomers', 'chitDetails'));
    }

    public function removeCustomer(Chit $chit, $chitCustomerId)
    {
        $chitDetail = ChitDetail::where('chit_id', $chit->id)->where('chit_customer_id', $chitCustomerId)->first();
        if ($chitDetail) {
            $chitDetail->delete();
        }
        return redirect()->route('chits.show', $chit)->with('success', 'Customer removed successfully.');
    }

    public function updateChitDetail(Request $request)
    {
        $request->validate([
            'chit_customer_id' => 'required|exists:chit_customers,id',
            'total_paid' => 'required|numeric',
            'month_amount' => 'required|numeric',
            'month_number' => 'required|numeric|between:1,12',
            'month_note' => 'nullable|string|max:255',
            'chit_id' => 'required|exists:chits,id',
        ]);

        // Find the ChitDetail for the given customer and chit
        $chitDetail = ChitDetail::where('chit_customer_id', $request->chit_customer_id)
            ->where('chit_id', $request->chit_id)
            ->first();

        if ($chitDetail) {
            // Update the specific month's amount and note
            $monthField = 'month_' . $request->month_number;
            $monthNoteField = 'month_' . $request->month_number . '_note';
            
            $chitDetail->$monthField = $request->month_amount;
            if ($request->has('month_note')) {
                $chitDetail->$monthNoteField = $request->month_note;
            }
            
            // Recalculate total paid
            $totalPaid = 0;
            for ($i = 1; $i <= 12; $i++) {
                $monthField = 'month_' . $i;
                $totalPaid += $chitDetail->$monthField;
            }
            
            $chitDetail->total_paid = $totalPaid;
            $chitDetail->save();

            // Update the chit's total paid amount
            $chit = Chit::find($request->chit_id);
            $chitTotalPaid = ChitDetail::where('chit_id', $request->chit_id)->sum('total_paid');
            $chit->paid_amount = $chitTotalPaid;
            $chit->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Chit detail not found.']);
    }
    
    public function updateCustomers(Request $request, Chit $chit)
    {
        $request->validate([
            'chit_customer_id' => 'required|exists:chit_customers,id',
        ]);

        // Create a new ChitDetail for the selected customer
        ChitDetail::create([
            'chit_id' => $chit->id,
            'chit_customer_id' => $request->chit_customer_id,
            'total_paid' => 0,
            // Initialize months and their notes
            'month_1' => false,
            'month_1_note' => '',
            'month_2' => false,
            'month_2_note' => '',
            'month_3' => false,
            'month_3_note' => '',
            'month_4' => false,
            'month_4_note' => '',
            'month_5' => false,
            'month_5_note' => '',
            'month_6' => false,
            'month_6_note' => '',
            'month_7' => false,
            'month_7_note' => '',
            'month_8' => false,
            'month_8_note' => '',
            'month_9' => false,
            'month_9_note' => '',
            'month_10' => false,
            'month_10_note' => '',
            'month_11' => false,
            'month_11_note' => '',
            'month_12' => false,
            'month_12_note' => '',
        ]);

        return redirect()->route('chits.show', $chit)->with('success', 'Customer enrolled successfully.');
    }

    public function updateChitPaidStatus(Request $request)
    {
        $request->validate([
            'chit_customer_id' => 'required|exists:chit_customers,id',
            'chit_id' => 'required|exists:chits,id',
            'paid_amount' => 'required|numeric|min:0',
            'is_chit_paid' => 'boolean',
        ]);

        $chitDetail = ChitDetail::where('chit_customer_id', $request->chit_customer_id)
            ->where('chit_id', $request->chit_id)
            ->first();

        if ($chitDetail) {
            $chitDetail->paid_amount = $request->paid_amount;  // Save to ChitDetail
            $chitDetail->is_chit_paid = $request->is_chit_paid;
            $chitDetail->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Chit detail not found.']);
    }
}
