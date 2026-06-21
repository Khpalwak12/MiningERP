<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marble_shipments', function (Blueprint $table) {
            $table->decimal('quantity_ton', 12, 3)->nullable()->change();
            $table->decimal('price_per_ton', 14, 2)->nullable()->change();
            $table->decimal('total_amount', 16, 2)->nullable()->change();
            $table->string('status', 30)->default('completed')->after('total_amount');
            $table->index('status');
        });

        DB::table('marble_shipments')->update(['status' => 'completed']);
    }

    public function down(): void
    {
        Schema::table('marble_shipments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
            $table->decimal('quantity_ton', 12, 3)->nullable(false)->change();
            $table->decimal('price_per_ton', 14, 2)->nullable(false)->change();
            $table->decimal('total_amount', 16, 2)->nullable(false)->change();
        });
    }
};
