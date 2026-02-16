<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'qty',
        'weight',
        'wastage_weight',
        'stone_weight',
        'gold_rate_id',
        'making_charges',
        'amount',
        'status',
        'product_category_id',
        'sub_category_id',
        'created_by',
        'is_approved',
        'product_no',
        'supplier_id',
        'type',
        'product_type',
    ];

    protected $casts = [
        'weight' => 'decimal:3',
        'wastage_weight' => 'decimal:3',
        'stone_weight' => 'decimal:3',
        'making_charges' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($product) {
    //         if (!$product->product_no) {
    //             $lastProduct = self::orderBy('product_no', 'desc')->first();
    //             $product->product_no = $lastProduct ? $lastProduct->product_no + 1 : 5001;
    //         }
    //     });
    // }

        protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Only generate if user did not provide a product_no
            if (empty($product->product_no)) {
                $product->product_no = \App\Models\ProductNumberSequence::getNextProductNumber();
            }
        });
    }



    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function goldRate()
    {
        return $this->belongsTo(GoldRate::class, 'gold_rate_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function productWeightAdjusts()
    {
        return $this->hasMany(ProductWeightAdjust::class);
    }

}
