<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOldGoldDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_old_gold_id',
        'gold_rate_id',
        'gold_gram',
        'gold_purchased_amount',
    ];

    
    public function goldRate()
    {
        return $this->belongsTo(GoldRate::class);
    }

    public function purchaseOldGold()
    {
        return $this->belongsTo(PurchaseOldGold::class);
    }


}
