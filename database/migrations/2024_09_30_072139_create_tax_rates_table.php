<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 2); // rate in percentage (e.g., 18.00 for 18%)
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('tax_rates');
    }
};
