<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWeightAdjust extends Model
{
    use HasFactory;

    protected $table = 'product_weight_adjusts';

    protected $fillable = [
        'product_id',
        'note',
        'adjust_type',
        'weight'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
