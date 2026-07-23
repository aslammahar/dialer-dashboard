<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id()->comment('Primary Key');
            $table->dateTime('create_time')->nullable()->comment('Create Time');
            $table->string('name')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedInteger('uid')->default(0);
            $table->tinyInteger('state')->default(0);
            $table->time('attendance_time')->default('00:44:41');
            $table->date('attendance_date')->default('2024-06-22');
            $table->tinyInteger('status')->default(1);
            $table->unsignedTinyInteger('type')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
