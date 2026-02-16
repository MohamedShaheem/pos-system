<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GoldRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate',
        'type',
        'name',  
        'rate_per_pawn',  
        'percentage',
    ];

    // Check if rate is outdated (not updated today)
    public function isOutdated()
    {
        return $this->updated_at->lt(Carbon::today());
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function customerExchangeGolds()
    {
        return $this->hasMany(CustomerGoldExchange::class);
    }
}