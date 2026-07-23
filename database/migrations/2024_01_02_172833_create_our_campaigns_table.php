<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('our_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('our_project_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('our_project_id')->references('id')->on('our_projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('our_campaigns');
    }
};