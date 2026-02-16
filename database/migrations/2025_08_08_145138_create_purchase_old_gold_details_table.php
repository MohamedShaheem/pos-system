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
        Schema::create('purchase_old_gold_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_old_gold_id')->nullable()->constrained('purchase_old_golds')->nullOnDelete();
            $table->foreignId('gold_rate_id')->nullable()->constrained('gold_rates')->nullOnDelete();
            $table->decimal('gold_gram', 10, 3)->default(0);
            $table->decimal('gold_purchased_amount', 10, 2);
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
        Schema::dropIfExists('purchase_old_gold_details');
    }
};
