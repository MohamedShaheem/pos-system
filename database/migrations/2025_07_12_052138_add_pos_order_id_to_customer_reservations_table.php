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
    Schema::table('customer_reservations', function (Blueprint $table) {
        $table->unsignedBigInteger('pos_order_id')->nullable()->after('status');
        $table->foreign('pos_order_id')->references('id')->on('pos_orders');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
public function down()
{
    Schema::table('customer_reservations', function (Blueprint $table) {
        $table->dropForeign(['pos_order_id']);
        $table->dropColumn('pos_order_id');
    });
}
};
