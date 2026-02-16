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
        Schema::table('customer_gold_advance_uses', function (Blueprint $table) {
            $table->decimal('gold_rate', 10, 2)->default(0)->after('gold_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_gold_advance_uses', function (Blueprint $table) {
            $table->dropColumn('gold_rate');
        });
    }
};
