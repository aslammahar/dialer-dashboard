<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salary_department_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_department_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('assigned_date')->default(now());
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['salary_department_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_department_users');
    }
};