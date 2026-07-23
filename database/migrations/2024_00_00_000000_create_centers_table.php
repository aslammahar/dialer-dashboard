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
    Schema::create('centers', function (Blueprint $table) {
        $table->id();
        $table->string('center_name');
        $table->text('description')->nullable();
        $table->unsignedBigInteger('created_by')->nullable();
        $table->timestamps(); // created_at & updated_at
    });
}

public function down()
{
    Schema::dropIfExists('centers');
}
};
