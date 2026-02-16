<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAdvanceRefund extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_advance_id',
        'amount'
    ];

    public function customerAdvance()
    {
        return $this->belongsTo(CustomerAdvance::class, 'customer_advance_id');
    }
}
