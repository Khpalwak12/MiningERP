<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marble_shipments', function (Blueprint $table) {
            if (! Schema::hasColumn('marble_shipments', 'stone_type_id')) {
                $table->foreignId('stone_type_id')->nullable()->after('mine_type_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('marble_shipments', function (Blueprint $table) {
            if (Schema::hasColumn('marble_shipments', 'stone_type_id')) {
                $table->dropForeign(['stone_type_id']);
                $table->dropColumn('stone_type_id');
            }
        });
    }
};
