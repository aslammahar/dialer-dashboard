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
   Schema::create('department_support_tickets', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('department_support_id'); // from DepartmentSupport table
    $table->unsignedBigInteger('user_id'); // jis ne issue submit kia
    $table->string('subject');
    $table->text('description')->nullable();
    $table->string('status')->default('Pending'); // Pending, Solved, Declined
     $table->text('assigned_to')->nullable();
        $table->text('response')->nullable();
    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_support_tickets');
    }
};
