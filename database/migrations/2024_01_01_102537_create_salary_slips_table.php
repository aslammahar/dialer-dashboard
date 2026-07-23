<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_salary_id')->constrained()->onDelete('cascade');
            $table->string('slip_number')->unique();
            $table->string('file_path')->nullable();
            $table->enum('status', ['generated', 'sent', 'downloaded'])->default('generated');
            $table->timestamp('generated_at');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_slips');
    }
};