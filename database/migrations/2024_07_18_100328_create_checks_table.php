<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecksTable extends Migration
{
    public function up()
    {
        // Drop the table if it exists
        Schema::dropIfExists('checks');

        // Create the new checks table
        Schema::create('checks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('employee_id'); // Use unsignedBigInteger for referencing bigIncrements
            $table->dateTime('attendance_time');
            $table->dateTime('leave_time')->nullable();
            
            // Define foreign key constraint
            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('checks', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });

        Schema::dropIfExists('checks');
    }
}
