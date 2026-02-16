<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMergePending extends Model
{
    protected $fillable = [
        'source_products_data',
        'merge_details',
        'merged_product_data',
        'leftover_product_data',
        'created_by',
        'approved_by',
        'approved_at',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'source_products_data' => 'array',
        'merge_details' => 'array',
        'merged_product_data' => 'array',
        'leftover_product_data' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}