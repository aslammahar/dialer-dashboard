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
    Schema::create('sales_teams', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // WFH Closing, Winning Edge, Sales Slayers, Jsons Team, Retention
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_teams');
    }
};
