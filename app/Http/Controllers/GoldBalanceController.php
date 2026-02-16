<?php

namespace App\Http\Controllers;

use App\Models\GoldBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoldBalanceController extends Controller
{
    public function index()
    {
        $goldBalances = GoldBalance::orderBy('created_at', 'asc')->get();
        return view('gold-balance.index', compact('goldBalances'));
    }

    public function create()
    {
        $lastBalance = GoldBalance::orderBy('created_at', 'desc')->first();
        $currentBalance = $lastBalance ? $lastBalance->gold_balance : 0;
        return view('gold-balance.create',compact('currentBalance'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'description' => 'required|string|max:255',
            'transaction_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Get the current balance (last entry's balance or 0)
            $lastBalance = GoldBalance::orderBy('created_at', 'desc')->first();
            $currentBalance = $lastBalance ? $lastBalance->gold_balance : 0;
            $transaction_type = $request->transaction_type;
            $amount = $request->amount;

            if($transaction_type == 'gold_in'){
                // Calculate new balance
                $newBalance = $currentBalance + $amount;
            }
            else{
                // Calculate new balance
                $newBalance = $currentBalance - $amount;
            }
           

            // Ensure we don't go negative
            if ($newBalance < 0) {
                throw new \Exception('Insufficient gold balance. Available: ' . number_format($currentBalance, 3));
            }


           
            if($transaction_type == 'gold_in'){
                 // Create new entry
                GoldBalance::create([
                    'description' => $request->description,
                    'gold_in' => $request->amount,
                    'gold_out' => 0,
                    'gold_balance' => $newBalance,
                ]);
            }
            else{
                  // Create new entry
                GoldBalance::create([
                    'description' => $request->description,
                    'gold_in' => 0,
                    'gold_out' => $request->amount,
                    'gold_balance' => $newBalance,
                ]);
            }
           
        });

        return redirect()->route('gold_balance.index')->with('success', 'Gold balance entry created successfully.');
    }

    public function edit(GoldBalance $goldBalance)
    {
        $goldIn = $goldBalance->gold_in;
        $amount = $goldBalance->amount; 
        if($goldIn > 0){
            $transactionType = "in";
            $amount = $goldBalance->gold_in;
        }else{
            $transactionType = "out";
            $amount = $goldBalance->gold_out;

        }

        $lastBalance = GoldBalance::orderBy('created_at', 'desc')->first();
        $currentBalance = $lastBalance ? $lastBalance->gold_balance : 0;
        return view('gold-balance.edit', compact('currentBalance','goldBalance','transactionType','amount'));
    }

    public function update(Request $request, GoldBalance $goldBalance)
    {
        // dd($request->all());
        $request->validate([
            'description' => 'required|string|max:255',
            'transaction_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $goldBalance) {
            $transaction_type = $request->transaction_type;

             if($transaction_type == 'in'){
                 // Update the entry
            $goldBalance->update([
                'description' => $request->description,
                'gold_in' =>$request->amount,
                'gold_out' => 0,
            ]);
            }
            else{
                 // Update the entry
            $goldBalance->update([
                'description' => $request->description,
                'gold_in' => 0,
                'gold_out' => $request->amount,
            ]);
            }
            // Recalculate all balances from this point forward
            $this->recalculateBalances();
        });

        return redirect()->route('gold_balance.index')->with('success', 'Gold balance entry updated successfully.');
    }

    public function destroy(GoldBalance $goldBalance)
    {
        DB::transaction(function () use ($goldBalance) {
            $goldBalance->delete();
            $this->recalculateBalances();
        });

        return redirect()->route('gold_balance.index')->with('success', 'Gold balance entry deleted successfully.');
    }


    // NEW REPORT METHODS

    /**
     * Show the daily report form
     */
    public function dailyReportForm()
    {
        return view('gold-balance.daily-report-form');
    }

    /**
     * Generate daily gold balance report
     */
    public function dailyReport(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
        ]);

        $reportDate = Carbon::parse($request->report_date);
        $reportData = $this->generateDailyReportData($reportDate);

        return view('gold-balance.daily-report', compact('reportData', 'reportDate'));
    }

    /**
     * Download daily report as PDF
     */
    // public function downloadDailyReport(Request $request)
    // {
    //     $request->validate([
    //         'report_date' => 'required|date',
    //     ]);

    //     $reportDate = Carbon::parse($request->report_date);
    //     $reportData = $this->generateDailyReportData($reportDate);

    //     $pdf = Pdf::loadView('gold-balance.daily-report-pdf', compact('reportData', 'reportDate'))
    //               ->setPaper('a4', 'portrait')
    //               ->setOptions([
    //                   'isHtml5ParserEnabled' => true,
    //                   'isRemoteEnabled' => true,
    //               ]);

    //     $filename = 'gold-balance-report-' . $reportDate->format('Y-m-d') . '.pdf';
        
    //     return $pdf->download($filename);
    // }

    /**
     * Generate report data for a specific date
     */
    private function generateDailyReportData(Carbon $date)
    {
        // Get transactions for the specific date
        $transactions = GoldBalance::whereDate('created_at', $date)
            ->orderBy('created_at', 'asc')
            ->get();

        // Get opening balance (balance at the end of previous day)
        $previousDayLastTransaction = GoldBalance::whereDate('created_at', '<', $date)
            ->orderBy('created_at', 'desc')
            ->first();

        $openingBalance = $previousDayLastTransaction ? $previousDayLastTransaction->gold_balance : 0;

        // Calculate totals
        $totalGoldIn = $transactions->sum('gold_in');
        $totalGoldOut = $transactions->sum('gold_out');
        $closingBalance = $transactions->count() > 0 ? $transactions->last()->gold_balance : $openingBalance;

        // Get first and last transaction times
        $firstTransaction = $transactions->first();
        $lastTransaction = $transactions->last();

        return [
            'transactions' => $transactions,
            'opening_balance' => $openingBalance,
            'total_gold_in' => $totalGoldIn,
            'total_gold_out' => $totalGoldOut,
            'closing_balance' => $closingBalance,
            'transaction_count' => $transactions->count(),
            'net_change' => $closingBalance - $openingBalance,
            'first_transaction_time' => $firstTransaction ? $firstTransaction->created_at : null,
            'last_transaction_time' => $lastTransaction ? $lastTransaction->created_at : null,
        ];
    }








    private function recalculateBalances()
    {
        $entries = GoldBalance::orderBy('created_at', 'asc')->get();
        $runningBalance = 0;

        foreach ($entries as $entry) {
            $goldIn = $entry->gold_in ?? 0;
            $goldOut = $entry->gold_out ?? 0;
            $runningBalance = $runningBalance + $goldIn - $goldOut;
            
            $entry->update(['gold_balance' => $runningBalance]);
        }
    }

    public function getCurrentBalance()
    {
        $lastEntry = GoldBalance::orderBy('created_at', 'desc')->first();
        return $lastEntry ? $lastEntry->gold_balance : 0;
    }
}