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
        Schema::create('stock_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_audit_id')->constrained()->onDelete('cascade');
            $table->string('product_no')->index();
            $table->timestamp('scanned_at');
            $table->foreignId('scanned_by')->constrained('users');
            $table->timestamps();
            
            // Prevent duplicate scans in same audit
            $table->unique(['stock_audit_id', 'product_no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_audit_items');
    }
};
