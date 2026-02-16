<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGoldExchange extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'gold_rate_id',
        'gold_weight',
        'gold_purchased_amount',
        'pos_order_id',
    ];

    protected $casts = [
        'gold_weight' => 'float',
        'gold_purchased_amount' => 'float',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function goldRate()
    {
        return $this->belongsTo(GoldRate::class);
    }

    public function posOrder()
    {
        return $this->belongsTo(POSOrder::class, 'pos_order_id');
    }
}
