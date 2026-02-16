<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAdvanceRefundsTable extends Migration
{
    public function up()
    {
        Schema::create('customer_advance_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_advance_id');
            $table->decimal('amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_advance_id')->references('id')->on('customer_advances')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_advance_refunds');
    }
}

