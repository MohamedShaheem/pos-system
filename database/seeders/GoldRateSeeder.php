<?php

namespace Database\Seeders;

use App\Models\GoldRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GoldRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GoldRate::create([
            'name' => '22K',
            'rate' => 460.00,
        ]);
    }
}
