<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

     protected $fillable = [
        'supplier_name',
        'short_code',
        'address',
        'city',
        'contact_no',
        'email',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
