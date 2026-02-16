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
        Schema::create('stock_audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_reference')->unique(); // e.g., AUD-2025-001
            $table->foreignId('product_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->integer('expected_count')->default(0); // Products in system
            $table->integer('scanned_count')->default(0); // Products scanned
            $table->text('notes')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_audits');
    }
};
