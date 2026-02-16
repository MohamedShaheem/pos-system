<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChitCustomer extends Model
{
    protected $fillable = [
        'customer_no',
        'name',
        'address',
        'city',
        'tel'
    ];

    public function chitDetails()
    {
        return $this->hasMany(ChitDetail::class);
    }
} 