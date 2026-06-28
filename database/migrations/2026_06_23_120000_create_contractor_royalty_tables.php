<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_year_id')->constrained('financial_years');
            $table->date('production_date');
            $table->decimal('quantity_ton', 12, 3);
            $table->decimal('rate_per_ton', 12, 2);
            $table->decimal('total_royalty', 14, 2);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contractor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_year_id')->constrained('financial_years');
            $table->date('payment_date');
            $table->decimal('amount', 14, 2);
            $table->string('receipt_number')->nullable();
            $table->string('received_by');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_payments');
        Schema::dropIfExists('contractor_productions');
    }
};
