<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gold_balances', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->decimal('gold_in', 10, 3)->default(0);     
            $table->decimal('gold_out', 10, 3)->default(0);
            $table->decimal('gold_balance', 10, 3)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gold_balances');
    }
};
