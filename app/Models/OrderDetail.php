<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_order_id', 'product_id', 'qty', 'discount', 'amount', 'weight', 'making_charges', 'wastage_weight', 'stone_weight', 'gold_rate'
    ];

    public function posOrder() {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
