<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAdvance extends Model
{
    use HasFactory;
   
    protected $fillable = [
        'customer_id',
        'order_no',
        'note',
        'advance_balance'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function details()
    {
        return $this->hasMany(CustomerAdvanceDetail::class);
    }

    public function refunds()
    {
        return $this->hasMany(CustomerAdvanceRefund::class);
    }

    public function advanceUse()
    {
        return $this->hasMany(CustomerAdvanceUse::class);
    }

    public function getAdvanceBalanceAttribute($value)
    {
        return $value;
    }

    public function getTotalDepositAttribute()
    {
        return $this->details()->sum('amount');
    }

    public function getTotalUsedAttribute()
    {
        return $this->advanceUse()->sum('amount');
    }

    public function getTotalRefundedAttribute()
    {
        return $this->refunds()->sum('amount');
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->total_deposit - $this->total_used - $this->total_refunded;
    }

    public function getCurrentBalanceAttribute()
    {
        // This is the actual available balance considering deposits, usage, and refunds
        $totalDeposit = $this->details()->sum('amount');
        $totalUsed = $this->advanceUse()->sum('amount');
        $totalRefunded = $this->refunds()->sum('amount');
        
        return $totalDeposit - $totalUsed - $totalRefunded;
    }

    // Scope for advances with order_no
    public function scopeWithOrderNo($query)
    {
        return $query->whereNotNull('order_no');
    }

    // Scope for advances without order_no
    public function scopeWithoutOrderNo($query)
    {
        return $query->whereNull('order_no');
    }

    // Scope for specific order_no
    public function scopeByOrderNo($query, $orderNo)
    {
        return $query->where('order_no', $orderNo);
    }

    // Scope for advances with positive balance
    // public function scopeWithPositiveBalance($query)
    // {
    //     return $query->whereHas('details', function($q) {
    //         $q->select(\DB::raw('SUM(amount) as total_deposits'));
    //     })->where(function($query) {
    //         $query->whereRaw('
    //             (SELECT COALESCE(SUM(amount), 0) FROM customer_advance_details WHERE customer_advance_id = customer_advances.id) 
    //             - 
    //             (SELECT COALESCE(SUM(amount), 0) FROM customer_advance_uses WHERE customer_advance_id = customer_advances.id)
    //             - 
    //             (SELECT COALESCE(SUM(amount), 0) FROM customer_advance_refunds WHERE customer_advance_id = customer_advances.id)
    //             > 0
    //         ');
    //     });
    // }
}