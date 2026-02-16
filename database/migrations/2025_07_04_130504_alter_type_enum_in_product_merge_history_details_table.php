<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class AlterTypeEnumInProductMergeHistoryDetailsTable extends Migration
{
    public function up()
    {
        // Convert ENUM to VARCHAR(50)
        DB::statement("ALTER TABLE `product_merge_history_details` 
            MODIFY `type` VARCHAR(50) NOT NULL");
    }

    public function down()
    {
        // Roll back to original ENUM definition if needed
        DB::statement("ALTER TABLE `product_merge_history_details` 
            MODIFY `type` ENUM('source', 'merged', 'leftover') NOT NULL");
    }
}

