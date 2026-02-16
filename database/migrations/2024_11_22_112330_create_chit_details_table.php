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
        Schema::create('chit_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chit_id');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('month_1', 10, 2)->default(0);
            $table->decimal('month_2', 10, 2)->default(0);
            $table->decimal('month_3', 10, 2)->default(0);
            $table->decimal('month_4', 10, 2)->default(0);
            $table->decimal('month_5', 10, 2)->default(0);
            $table->decimal('month_6', 10, 2)->default(0);
            $table->decimal('month_7', 10, 2)->default(0);
            $table->decimal('month_8', 10, 2)->default(0);
            $table->decimal('month_9', 10, 2)->default(0);
            $table->decimal('month_10', 10, 2)->default(0);
            $table->decimal('month_11', 10, 2)->default(0);
            $table->decimal('month_12', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->boolean('is_chit_paid')->default(false);
            $table->string('payment_month')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('chit_id')->references('id')->on('chits')->onDelete('cascade');
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
        Schema::dropIfExists('chit_details');
    }
};
