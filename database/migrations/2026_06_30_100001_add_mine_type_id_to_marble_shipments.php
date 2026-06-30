<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marble_shipments', function (Blueprint $table) {
            if (! Schema::hasColumn('marble_shipments', 'mine_type_id')) {
                $table->foreignId('mine_type_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('marble_shipments', function (Blueprint $table) {
            if (Schema::hasColumn('marble_shipments', 'mine_type_id')) {
                $table->dropForeign(['mine_type_id']);
                $table->dropColumn('mine_type_id');
            }
        });
    }
};
