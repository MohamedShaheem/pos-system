<?php

// Updated CustomerAdvanceUse Model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAdvanceUse extends Model
{
    use HasFactory;
   
    protected $fillable = [
        'customer_advance_id', // Changed from customer_id to customer_advance_id
        'amount',
        'pos_order_id'
    ];

    public function customerAdvance()
    {
        return $this->belongsTo(CustomerAdvance::class);
    }

    public function posOrder()
    {
        return $this->belongsTo(POSOrder::class, 'pos_order_id');
    }


    // Get customer through customerAdvance relationship
    public function customer()
    {
        return $this->hasOneThrough(Customer::class, CustomerAdvance::class, 'id', 'id', 'customer_advance_id', 'customer_id');
    }

    protected $casts = [
        'amount' => 'float'
    ];
}