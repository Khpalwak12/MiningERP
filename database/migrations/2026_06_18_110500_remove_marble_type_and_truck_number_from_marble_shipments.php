<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marble_shipments', function (Blueprint $table) {
            if (Schema::hasColumn('marble_shipments', 'marble_type_id')) {
                try {
                    $table->dropForeign(['marble_type_id']);
                } catch (\Throwable) {
                    // Ignore if constraint name differs or missing.
                }
                $table->dropColumn('marble_type_id');
            }

            if (Schema::hasColumn('marble_shipments', 'truck_number')) {
                $table->dropColumn('truck_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('marble_shipments', function (Blueprint $table) {
            if (! Schema::hasColumn('marble_shipments', 'truck_number')) {
                $table->string('truck_number')->nullable()->after('shipment_date');
            }

            if (! Schema::hasColumn('marble_shipments', 'marble_type_id')) {
                $table->foreignId('marble_type_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            }
        });
    }
};

