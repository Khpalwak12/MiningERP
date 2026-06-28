<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_salary_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_year_id')->constrained('financial_years');
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('payroll_payment_id')->nullable()->unique()->constrained('payroll_payments')->nullOnDelete();
            $table->date('charge_date');
            $table->string('period_month', 7)->nullable();
            $table->decimal('amount', 14, 2);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['charge_date', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_salary_charges');
    }
};
