<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop the foreign key if it exists
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $doctrineTable = $sm->listTableDetails('customer_reservations');

        if ($doctrineTable->hasForeignKey('customer_reservations_product_id_foreign')) {
            Schema::table('customer_reservations', function (Blueprint $table) {
                $table->dropForeign('customer_reservations_product_id_foreign');
            });
        }

        // Drop product_id column and add product_details
        Schema::table('customer_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('customer_reservations', 'product_id')) {
                $table->dropColumn('product_id');
            }

            if (!Schema::hasColumn('customer_reservations', 'product_details')) {
                $table->json('product_details')->after('customer_id');
            }
        });

        // Add pos_order_id if it doesn't exist
        if (!Schema::hasColumn('customer_reservations', 'pos_order_id')) {
            Schema::table('customer_reservations', function (Blueprint $table) {
                $table->foreignId('pos_order_id')->nullable()->constrained('pos_orders');
            });
        }
    }

    public function down()
    {
        // Drop pos_order_id
        if (Schema::hasColumn('customer_reservations', 'pos_order_id')) {
            Schema::table('customer_reservations', function (Blueprint $table) {
                $table->dropForeign(['pos_order_id']);
                $table->dropColumn('pos_order_id');
            });
        }

        // Drop product_details and add back product_id
        Schema::table('customer_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('customer_reservations', 'product_details')) {
                $table->dropColumn('product_details');
            }

            $table->foreignId('product_id')->after('customer_id')->constrained();
        });
    }
};

