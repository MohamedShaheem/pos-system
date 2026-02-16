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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('wastage_weight', 8, 3)->nullable()->after('weight'); // weight in grams and milligrams
            $table->decimal('stone_weight', 8, 3)->nullable()->after('wastage_weight'); // weight in grams and milligrams
            $table->unsignedBigInteger('gold_rate_id')->nullable()->after('stone_weight'); // Reference to gold_rates table
            $table->foreign('gold_rate_id')->references('id')->on('gold_rates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['gold_rate_id']);
            $table->dropColumn(['wastage_weight', 'stone_weight', 'gold_rate_id']);
        });
    }
}; 