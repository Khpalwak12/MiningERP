<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_payments', 'receipt_number')) {
                $table->string('receipt_number')->nullable()->unique()->after('amount');
            }
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_payments', 'received_by')) {
                $table->string('received_by')->default('')->after('receipt_number');
            }
        });

        if (Schema::hasColumn('customer_payments', 'received_by')) {
            DB::table('customer_payments')
                ->where(function ($query) {
                    $query->whereNull('received_by')->orWhere('received_by', '');
                })
                ->update(['received_by' => '—']);
        }

        Schema::table('customer_payments', function (Blueprint $table) {
            if (Schema::hasColumn('customer_payments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_payments', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'other'])->default('cash')->after('amount');
            }

            if (Schema::hasColumn('customer_payments', 'receipt_number')) {
                $table->dropUnique(['receipt_number']);
                $table->dropColumn('receipt_number');
            }

            if (Schema::hasColumn('customer_payments', 'received_by')) {
                $table->dropColumn('received_by');
            }
        });
    }
};
