<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name','short_code','sort_order'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

     public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'product_category_id');
    }
}
