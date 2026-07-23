<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('closer_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_closer_id')
                  ->constrained('sales_closers')
                  ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->enum('status', [
                'present',
                'absent',
                'leave',
                'half_day'
            ])->default('present');

            $table->foreignId('marked_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            $table->unique(['sales_closer_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closer_attendance');
    }
};