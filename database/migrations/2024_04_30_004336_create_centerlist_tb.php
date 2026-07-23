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
        Schema::create('centerlist_tb', function (Blueprint $table) {
            $table->id();
            $table->string('centerName', 150)->nullable();
            $table->string('centerCode', 150)->nullable();
            $table->string('employee_id', 150)->nullable();
            $table->string('created_by', 300)->nullable();
            $table->timestamps();
        });
    }

    // id	
    //  Ascending 1	
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centerlist_tb');
    }
};
