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
        Schema::create('monitorings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // Foreign key for employees
            $table->time('monitor_from');
            $table->time('monitor_to');
            $table->date('monitor_date');
            $table->text('call_rapport_building')->nullable();
            $table->text('qualifying_part')->nullable();
            $table->text('agents_efforts')->nullable();
            $table->text('rebuttals')->nullable();
            $table->text('overall_call_details')->nullable();
            $table->text('vocabulary')->nullable();
            $table->text('customer_response')->nullable();
            $table->text('suggestions')->nullable();
            $table->enum('score', ['Good', 'Avg', 'Bad', 'Worst']);
            $table->json('notify')->nullable(); // Remove ->after()

            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitorings');
    }
};
