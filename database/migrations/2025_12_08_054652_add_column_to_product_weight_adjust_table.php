<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_weight_adjusts', function (Blueprint $table) {
            $table->string('processed_by')->nullable()->after('weight');
        });
    }

    public function down()
    {
        Schema::table('product_weight_adjusts', function (Blueprint $table) {
            $table->dropColumn('processed_by');
        });
    }
};
