<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGoldAdvance extends Model
{
    use HasFactory;
   
    protected $fillable = [
        'customer_id',
        'order_no',
        'note',
        'gold_rate_id',
        'gold_balance'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function goldRate()
    {
        return $this->belongsTo(GoldRate::class);
    }

    public function details()
    {
        return $this->hasMany(CustomerGoldAdvanceDetail::class);
    }

    // Fixed: This should return CustomerGoldAdvanceUse, not CustomerGoldAdvance
    public function goldAdvanceUse()
    {
        return $this->hasMany(CustomerGoldAdvanceUse::class);
    }

    public function getTotalDepositAttribute()
    {
        return $this->details()->sum('gold_amount'); // Changed from 'amount' to 'gold_amount'
    }

    public function getTotalUsedAttribute()
    {
        return $this->goldAdvanceUse()->sum('gold_amount');
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->total_deposit - $this->total_used;
    }
}