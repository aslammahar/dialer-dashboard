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
        Schema::create('hik_vision_attendances', function (Blueprint $table) {
            $table->id();
            Schema::create('hik_vision_attendance', function (Blueprint $table) {
                $table->id();
                $table->string('employee_no')->nullable();       // employeeNoString from device
                $table->string('employee_name')->nullable();     // Name from device
                $table->string('status')->nullable();            // checkIn / checkOut
                $table->dateTime('event_time')->nullable();      // dateTime from device
                $table->json('raw_event')->nullable();           // full JSON for debugging
                $table->timestamps();
            });
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hik_vision_attendances');
    }
};
