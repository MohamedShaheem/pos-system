<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POSOrder extends Model
{
    use HasFactory;
    
    protected $table = 'pos_orders';
    // in this section advance mean cash payment
    protected $fillable = [
        'invoice_no', 'customer_id', 'total', 'advance', 'cash', 'chq', 'card', 'bank_transfer', 'balance', 
        'inclusive_tax', 'status', 'discount', 'processed_by', 'is_credit_invoice'
    ];

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($order) {
    //         $order->invoice_no = (1000 + self::count());
    //     });
    // }
    public function advanceUses()
    {
        return $this->hasMany(CustomerAdvanceUse::class, 'pos_order_id');
    }

    public function goldAdvanceUses()
    {
        return $this->hasMany(CustomerGoldAdvanceUse::class, 'pos_order_id');
    }

    public function customerGoldExchanges()
    {
        return $this->hasMany(CustomerGoldExchange::class, 'pos_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processedByUser()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }


    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'pos_order_id'); 
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'pos_order_id');
    }

    public function getTotalPaidAttribute()
    {
        return $this->advance + $this->payments()->sum('amount');
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->total - $this->total_paid;
    }

    // Boot method to auto-generate invoice number
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (!$order->invoice_no) {
                $order->invoice_no = 'INV-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
