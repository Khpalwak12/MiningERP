<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mine_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_year_id')->constrained('financial_years');
            $table->string('name');
            $table->string('related_to')->nullable();
            $table->decimal('quantity', 12, 3)->default(1);
            $table->string('unit')->default('piece');
            $table->enum('status', ['usable', 'unusable'])->default('usable');
            $table->date('registration_date');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['registration_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mine_assets');
    }
};
