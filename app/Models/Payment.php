<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_order_id',
        'amount',
        'payment_method',
        'reference_no',
        'notes',
        'is_credit_payment'
    ];

    public function posOrder()
    {
        return $this->belongsTo(POSOrder::class, 'pos_order_id');
    }
} 