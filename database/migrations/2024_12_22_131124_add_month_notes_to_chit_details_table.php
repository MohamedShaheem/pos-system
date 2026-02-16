<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chit_details', function (Blueprint $table) {
            $table->string('month_1_note')->nullable()->after('month_1');
            $table->string('month_2_note')->nullable()->after('month_2');
            $table->string('month_3_note')->nullable()->after('month_3');
            $table->string('month_4_note')->nullable()->after('month_4');
            $table->string('month_5_note')->nullable()->after('month_5');
            $table->string('month_6_note')->nullable()->after('month_6');
            $table->string('month_7_note')->nullable()->after('month_7');
            $table->string('month_8_note')->nullable()->after('month_8');
            $table->string('month_9_note')->nullable()->after('month_9');
            $table->string('month_10_note')->nullable()->after('month_10');
            $table->string('month_11_note')->nullable()->after('month_11');
            $table->string('month_12_note')->nullable()->after('month_12');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chit_details', function (Blueprint $table) {
            $table->dropColumn([
                'month_1_note', 'month_2_note', 'month_3_note', 'month_4_note',
                'month_5_note', 'month_6_note', 'month_7_note', 'month_8_note',
                'month_9_note', 'month_10_note', 'month_11_note', 'month_12_note'
            ]);
        });
    }
};
