<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machinery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_year_id')->constrained('financial_years');
            $table->date('purchase_date');
            $table->string('item_name');
            $table->enum('currency', ['AFN', 'USD'])->default('AFN');
            $table->decimal('amount', 14, 2);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['purchase_date', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machinery_items');
    }
};
