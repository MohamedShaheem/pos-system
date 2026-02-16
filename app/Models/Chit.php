<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'month_from',
        'month_to',
        'total_amount',
        'amount_per_month',
        'paid_amount',
        'serial_no'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($chit) {
            $chit->serial_no = strtoupper(substr(md5(uniqid(rand(), true)), 0, 7));
        });
    }

    public function chitDetails()
    {
        return $this->hasMany(ChitDetail::class);
    }
}
