<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_audits', function (Blueprint $table) {
            $table->enum('audit_type', ['category', 'all'])->default('category')->after('product_category_id');
            
            // Make category nullable for 'all' audits
            $table->unsignedBigInteger('product_category_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('stock_audits', function (Blueprint $table) {
            $table->dropColumn('audit_type');
            // Note: reverting nullable might require handling existing data
        });
    }
};