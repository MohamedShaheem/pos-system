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
        Schema::create('customer_gold_advance_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_gold_advance_id')->constrained('customer_gold_advances')->onDelete('cascade');
            $table->decimal('gold_amount', 10, 2);
            $table->foreignId('pos_order_id')->nullable()->constrained();
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
        Schema::dropIfExists('customer_gold_advance_uses');
    }
};
