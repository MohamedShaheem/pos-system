<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMergeHistoryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_merge_history_id',
        'product_id',
        'type', // 'source' or 'target'
        'product_data' //json value
    ];

    protected $casts = [
        'product_data' => 'array'
    ];

    public function history()
    {
        return $this->belongsTo(ProductMergeHistory::class, 'product_merge_history_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
} 