<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chit_details', function (Blueprint $table) {
            // Drop the old foreign key
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            
            // Add new column with foreign key
            $table->unsignedBigInteger('chit_customer_id')->after('chit_id');
            $table->foreign('chit_customer_id')->references('id')->on('chit_customers')->onDelete('cascade');

            // Add note fields for each month
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

    public function down()
    {
        Schema::table('chit_details', function (Blueprint $table) {
            // Drop the note fields
            $table->dropColumn([
                'month_1_note', 'month_2_note', 'month_3_note', 'month_4_note',
                'month_5_note', 'month_6_note', 'month_7_note', 'month_8_note',
                'month_9_note', 'month_10_note', 'month_11_note', 'month_12_note'
            ]);

            // Drop the new foreign key
            $table->dropForeign(['chit_customer_id']);
            $table->dropColumn('chit_customer_id');
            
            // Add back the old column with foreign key
            $table->unsignedBigInteger('customer_id')->after('chit_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
}; 