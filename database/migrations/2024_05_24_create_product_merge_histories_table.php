<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_merge_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamp('merged_at');
            $table->foreignId('merged_by')->constrained('users');
            $table->enum('merge_type', ['1-2', '2-2', '2-1']);
            $table->timestamps();
        });

        Schema::create('product_merge_history_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_merge_history_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->enum('type', ['source', 'merged', 'leftover']);
            $table->json('product_data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_merge_history_details');
        Schema::dropIfExists('product_merge_histories');
    }
}; 