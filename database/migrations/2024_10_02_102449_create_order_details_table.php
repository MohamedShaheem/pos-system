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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('pos_order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('qty');
            $table->decimal('weight', 8, 2);
            $table->decimal('amount', 10, 2);
            $table->decimal('making_charges', 10, 2)->nullable();
            $table->timestamps();
    
            // Foreign key constraints
            $table->foreign('pos_order_id')->references('id')->on('pos_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
};
