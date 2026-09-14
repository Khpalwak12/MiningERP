<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('absence_date');
            $table->unsignedInteger('days')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'absence_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_absences');
    }
};
