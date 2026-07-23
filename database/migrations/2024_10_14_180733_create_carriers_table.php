<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('carrier-search', function (Blueprint $table) {
        $table->id();
        $table->json('licensed_agency'); // Store as JSON
        $table->json('state'); // Store as JSON
        $table->string('licensed_agent_name');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('carrier-search');
}

};
