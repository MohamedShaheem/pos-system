<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'total_amount',
        'paid_amount',
        'status',
        'delivery_date',
        'pos_order_id'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'delivery_date' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function posOrder()
    {
        return $this->belongsTo(POSOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(CustomerReservationPayment::class, 'reservation_id');
    }

    // ADD THIS ALIAS FOR COMPATIBILITY
    public function details()
    {
        return $this->hasMany(CustomerReservationDetail::class, 'reservation_id');
    }

    public function reservationDetails()
    {
        return $this->hasMany(CustomerReservationDetail::class, 'reservation_id');
    }

    public function getProductsAttribute()
    {
        return $this->reservationDetails->map(function ($detail) {
            return $detail->product;
        })->filter();
    }

    public function getTotalQuantityAttribute()
    {
        return $this->reservationDetails->sum('quantity');
    }
}