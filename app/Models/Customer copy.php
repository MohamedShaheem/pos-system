<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;
   
    protected $guarded = [];

    public function posOrders()
    {
        return $this->hasMany(POSOrder::class);
    }

    public function advances()
    {
        return $this->hasMany(CustomerAdvance::class);
    }

    public function exchangeGolds()
    {
        return $this->hasMany(CustomerGoldExchange::class);
    }

    public function goldAdvances()
    {
        return $this->hasMany(CustomerGoldAdvance::class);
    }

    public function purchaseOldGold()
    {
        return $this->hasMany(PurchaseOldGold::class);
    }

    public function goldAdvanceUse()
    {
        return $this->hasManyThrough(CustomerGoldAdvanceUse::class, CustomerGoldAdvance::class);
    }

    public function reservations()
    {
        return $this->hasMany(CustomerReservation::class);
    }

    // Get all advance uses through customerAdvances
    public function advanceUses()
    {
        return $this->hasManyThrough(CustomerAdvanceUse::class, CustomerAdvance::class);
    }

    public function getTotalUsedAttribute()
    {
        return $this->advanceUses()->sum('amount');
    }

    public function getTotalOrdersAmountAttribute()
    {
        return $this->posOrders()->sum('total');
    }

    public function getTotalAdvancePaymentsAttribute()
    {
        return $this->posOrders()->sum('advance');
    }

    public function getTotalPaymentsAttribute()
    {
        $regularPayments = $this->posOrders()
            ->with('payments')
            ->get()
            ->sum(function ($order) {
                return $order->payments->sum('amount');
            });
       
        return $regularPayments + $this->total_advance_payments;
    }

    public function getTotalBalanceAttribute()
    {
        return $this->total_orders_amount - $this->total_payments;
    }

    public function getPaymentStatusAttribute()
    {
        if ($this->total_balance <= 0) {
            return 'paid';
        } elseif ($this->total_payments > 0) {
            return 'partial';
        }
        return 'unpaid';
    }

    public function getPaymentBreakdownAttribute()
    {
        return [
            'advance' => $this->total_advance_payments,
            'regular_payments' => $this->total_payments - $this->total_advance_payments,
            'total_paid' => $this->total_payments,
            'total_orders' => $this->total_orders_amount,
            'balance' => $this->total_balance
        ];
    }

    // Get total available advance balance
    public function getTotalAdvanceBalanceAttribute()
    {
        return $this->advances()->sum('advance_balance');
    }

    // Get advances with order_no
    public function getAdvancesWithOrderNoAttribute()
    {
        return $this->advances()->withOrderNo()->get();
    }

    // Get advances without order_no
    public function getAdvancesWithoutOrderNoAttribute()
    {
        return $this->advances()->withoutOrderNo()->get();
    }
}