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
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->decimal('total', 10, 2);
            $table->decimal('advance', 10, 2)->nullable();
            $table->decimal('balance', 10, 2);
            $table->decimal('inclusive_tax', 10, 2);
            $table->enum('status', ['complete', 'pending', 'draft', 'hold'])->default('pending');
            $table->timestamps();
    
            // Foreign key constraint
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_o_s_orders');
    }
};
