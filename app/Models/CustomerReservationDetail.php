<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReservationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'product_id',
        'quantity',
        'unit_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2'
    ];

    // Calculated attributes
    public function getLineTotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getProductNameAttribute()
    {
        return $this->product ? $this->product->name : 'Product Deleted';
    }

    public function getWeightAttribute()
    {
        return $this->product ? $this->product->weight : 0;
    }

    public function getMakingChargesAttribute()
    {
        return $this->product ? $this->product->making_charges : 0;
    }

    public function getWastageWeightAttribute()
    {
        return $this->product ? $this->product->wastage_weight : 0;
    }

    public function getStoneWeightAttribute()
    {
        return $this->product ? $this->product->stone_weight : 0;
    }

    public function getGoldRateAttribute()
    {
        return $this->product ? $this->product->gold_rate : 0;
    }

    public function reservation()
    {
        return $this->belongsTo(CustomerReservation::class, 'reservation_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}