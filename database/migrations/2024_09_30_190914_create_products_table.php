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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('product_no')->unique();
            $table->string('desc')->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('weight', 8, 3); // weight in grams and milligrams
            $table->decimal('making_charges', 8, 2)->nullable();
            $table->decimal('cost_amount', 10, 2)->default(0);
            $table->decimal('amount', 10, 2)->nullable();
            $table->unsignedBigInteger('product_category_id');
            $table->foreign('product_category_id')->references('id')->on('product_categories');
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
        Schema::dropIfExists('products');
    }
};
