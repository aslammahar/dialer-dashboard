<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_form_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('our_campaign_id'); // This line is crucial!
            $table->string('label');
            $table->string('name');
            $table->string('field_role');
            $table->string('type');
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->integer('order')->default(0);

            $table->timestamps();

            $table->foreign('our_campaign_id')->references('id')->on('our_campaigns')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};