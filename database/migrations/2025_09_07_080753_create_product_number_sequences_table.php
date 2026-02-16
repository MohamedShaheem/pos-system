<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->integer('last_number')->default(5000); // starting auto number
            $table->timestamps();
        });

        // Insert the initial row
        DB::table('product_number_sequences')->insert([
            'last_number' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('product_number_sequences');
    }
};
