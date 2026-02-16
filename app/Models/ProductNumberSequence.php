<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductNumberSequence extends Model
{
    protected $table = 'product_number_sequences';

    protected $fillable = ['last_number'];

    /**
     * Get the next product number and increment the sequence safely.
     */
    public static function getNextProductNumber()
    {
        return DB::transaction(function () {
            // Lock the row to prevent race conditions
            $sequence = self::lockForUpdate()->first();

            $nextNumber = $sequence->last_number + 1;

            $sequence->update(['last_number' => $nextNumber]);

            return $nextNumber;
        });
    }

    /**
     * Optional: reset the sequence to a specific number
     */
    // public static function resetSequence(int $number)
    // {
    //     $sequence = self::first();
    //     $sequence->update(['last_number' => $number]);
    // }
}
