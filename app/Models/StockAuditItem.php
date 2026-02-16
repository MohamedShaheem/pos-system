<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAuditItem extends Model
{
    protected $fillable = [
        'stock_audit_id',
        'product_no',
        'scanned_at',
        'scanned_by'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function audit()
    {
        return $this->belongsTo(StockAudit::class, 'stock_audit_id');
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function product()
    {
        return Product::where('product_no', $this->product_no)->first();
    }
}