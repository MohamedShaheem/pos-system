<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('chits', function (Blueprint $table) {
            $table->id();            
            $table->string('name');
            $table->string('month_from')->nullable();
            $table->string('month_to')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('amount_per_month', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('serial_no', 7)->unique()->nullable();
            $table->timestamps();
        });

        // Generate unique serial numbers for existing records
        $chits = DB::table('chits')->get();
        foreach ($chits as $chit) {
            $serialNo = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
            DB::table('chits')->where('id', $chit->id)->update(['serial_no' => $serialNo]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chits');
    }
};
