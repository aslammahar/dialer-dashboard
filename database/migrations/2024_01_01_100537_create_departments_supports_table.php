<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('department_supports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('role_id');
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_supports');
    }
};
