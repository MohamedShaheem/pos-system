<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGoldAdvanceDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_gold_advance_id',
        'gold_amount',
        'note'
    ];

    public function customerGoldAdvance()
    {
         return $this->belongsTo(CustomerGoldAdvance::class, 'customer_gold_advance_id');
    }

}
