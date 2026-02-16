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
        Schema::table('gold_rates', function (Blueprint $table) {
            $table->string('type')->nullable()->after('id');
            $table->decimal('rate_per_pawn', 10, 2)->nullable()->after('name');
            // $table->decimal('percentage', 5, 2)->nullable()->after('rate');  /// i have changed the data type on the db to text
            $table->string('percentage', 5, 2)->nullable()->after('rate');  /// i have changed the data type on the db to text
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gold_rates', function (Blueprint $table) {
            $table->dropColumn(['rate_per_pawn', 'percentage']);
        });
    }
};
