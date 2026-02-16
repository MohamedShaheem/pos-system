<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChitDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'chit_id',
        'chit_customer_id',
        'month_1',
        'month_1_note',
        'month_2',
        'month_2_note',
        'month_3',
        'month_3_note',
        'month_4',
        'month_4_note',
        'month_5',
        'month_5_note',
        'month_6',
        'month_6_note',
        'month_7',
        'month_7_note',
        'month_8',
        'month_8_note',
        'month_9',
        'month_9_note',
        'month_10',
        'month_10_note',
        'month_11',
        'month_11_note',
        'month_12',
        'month_12_note',
        'total_paid',
        'paid_amount',
        'is_chit_paid',
        'payment_month'
    ];

    public function chit()
    {
        return $this->belongsTo(Chit::class);
    }

    public function chitCustomer()
    {
        return $this->belongsTo(ChitCustomer::class);
    }
}
