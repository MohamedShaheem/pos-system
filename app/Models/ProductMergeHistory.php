<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMergeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'merged_at',
        'merged_by',
        'merge_type'
    ];

    protected $casts = [
        'merged_at' => 'datetime'
    ];

    public function details()
    {
        return $this->hasMany(ProductMergeHistoryDetail::class);
    }

    public function mergedBy()
    {
        return $this->belongsTo(User::class, 'merged_by');
    }
} 