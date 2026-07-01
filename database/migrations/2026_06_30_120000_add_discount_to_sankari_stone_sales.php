<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sankari_stone_sales', function (Blueprint $table) {
            $table->decimal('discount', 14, 2)->default(0)->after('price_per_truck');
        });

        DB::table('sankari_stone_sales')->orderBy('id')->lazyById()->each(function (object $sale) {
            $subtotal = round((float) $sale->truck_count * (float) $sale->price_per_truck, 2);
            $cashReceived = (float) $sale->cash_received;
            $discount = 0.0;
            $totalAmount = (float) $sale->total_amount;

            if ($cashReceived > 0 && $cashReceived < $totalAmount) {
                $discount = round($totalAmount - $cashReceived, 2);
                $totalAmount = $cashReceived;
            }

            DB::table('sankari_stone_sales')->where('id', $sale->id)->update([
                'discount' => $discount,
                'total_amount' => $totalAmount > 0 ? $totalAmount : max(0, round($subtotal - $discount, 2)),
            ]);
        });

        Schema::table('sankari_stone_sales', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'cash_received']);
        });
    }

    public function down(): void
    {
        Schema::table('sankari_stone_sales', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'credit'])->default('cash')->after('total_amount');
            $table->decimal('cash_received', 16, 2)->default(0)->after('payment_type');
        });

        DB::table('sankari_stone_sales')->orderBy('id')->lazyById()->each(function (object $sale) {
            DB::table('sankari_stone_sales')->where('id', $sale->id)->update([
                'payment_type' => 'cash',
                'cash_received' => (float) $sale->total_amount,
            ]);
        });

        Schema::table('sankari_stone_sales', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
