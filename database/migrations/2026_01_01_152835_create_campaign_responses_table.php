<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->json('submission_data');
            $table->unsignedBigInteger('refer_to')->nullable(); // Allow null values
            $table->foreign('refer_to')->references('id')->on('users')->onDelete('set null'); // Foreign key to users table
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_responses');
    }
};