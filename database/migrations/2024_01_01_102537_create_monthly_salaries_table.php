<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('monthly_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('salary_department_id')->constrained()->onDelete('cascade');
            $table->foreignId('salary_structure_id')->constrained()->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('basic_salary', 10, 2);
            $table->integer('working_days');
            $table->integer('present_days');
            $table->integer('absent_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->decimal('punctuality', 10, 2)->default(0);
            $table->decimal('total_allowances', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['user_id', 'month', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_salaries');
    }
};