<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GoldBalance;

class GoldBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GoldBalance::create([
            'description'   => 'Opening Balance',
            'gold_in'       => 0.000,
            'gold_out'      => 0.000,
            'gold_balance'  => 0.000,
            'created_at'    => now(),
        ]);
    }
}
