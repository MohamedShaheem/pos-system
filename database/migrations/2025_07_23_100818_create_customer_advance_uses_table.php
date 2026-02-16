<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_advance_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_advance_id')->constrained('customer_advances')->onDelete('cascade');
            // $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->foreignId('pos_order_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_advance_uses');
    }
};
