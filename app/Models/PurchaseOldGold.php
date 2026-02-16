<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOldGold extends Model
{
    use HasFactory;

    protected $table = 'purchase_old_golds';
    
    protected $fillable = ['customer_id', 'invoice_no', 'status'];

    protected static function booted()
    {
        static::creating(function ($purchaseOldGold) {
            if (!$purchaseOldGold->invoice_no) {
                $maxInvoiceNo = PurchaseOldGold::max('invoice_no') ?? 999;
                $purchaseOldGold->invoice_no = $maxInvoiceNo + 1;
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseOldGoldDetail::class);
    }
}
