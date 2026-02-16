<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGoldAdvanceUse extends Model
{
    use HasFactory;
   
    protected $fillable = [
        'customer_gold_advance_id', 
        'gold_amount',
        'gold_rate',
        'pos_order_id',
        'amount'
    ];

    public function customerGoldAdvance()
    {
        return $this->belongsTo(CustomerGoldAdvance::class);
    }

    public function posOrder()
    {
        return $this->belongsTo(POSOrder::class, 'pos_order_id');
    }


    public function customer()
    {
        return $this->hasOneThrough(Customer::class, CustomerGoldAdvance::class, 'id', 'id', 'customer_gold_advance_id', 'customer_id');
    }

}
