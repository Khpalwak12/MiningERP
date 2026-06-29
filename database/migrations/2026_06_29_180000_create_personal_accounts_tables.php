<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('personal_contacts')) {
            Schema::create('personal_contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('contact_type')->default('person');
                $table->text('notes')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('personal_ledger_transactions')) {
            Schema::create('personal_ledger_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('financial_year_id')->constrained('financial_years');
                $table->foreignId('personal_contact_id')->constrained('personal_contacts')->cascadeOnDelete();
                $table->date('transaction_date');
                $table->enum('transaction_type', ['credit', 'payment']);
                $table->enum('currency', ['AFN', 'USD'])->default('AFN');
                $table->decimal('amount', 14, 2);
                $table->text('description')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['transaction_date', 'personal_contact_id'], 'pl_ledger_tx_date_contact_idx');
            });
        } elseif (! $this->indexExists('personal_ledger_transactions', 'pl_ledger_tx_date_contact_idx')) {
            Schema::table('personal_ledger_transactions', function (Blueprint $table) {
                $table->index(['transaction_date', 'personal_contact_id'], 'pl_ledger_tx_date_contact_idx');
            });
        }

        if (! Schema::hasTable('personal_home_expenses')) {
            Schema::create('personal_home_expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('financial_year_id')->constrained('financial_years');
                $table->date('expense_date');
                $table->string('item_name');
                $table->enum('currency', ['AFN', 'USD'])->default('AFN');
                $table->decimal('amount', 14, 2);
                $table->text('description')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
                $table->index('expense_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_home_expenses');
        Schema::dropIfExists('personal_ledger_transactions');
        Schema::dropIfExists('personal_contacts');
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return collect(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]))->isNotEmpty();
    }
};
