<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::rename('pending_product_merges', 'product_merge_pendings');
    }

    public function down(): void
    {
        Schema::rename('pending_product_merges', 'product_merge_pendings');
    }
};

