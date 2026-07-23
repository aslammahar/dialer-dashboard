<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifier_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('closed_call_id');
            $table->unsignedBigInteger('verifier_id');
            $table->unsignedBigInteger('assigned_by');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->unique('closed_call_id'); // one verifier per call at a time

            $table->foreign('closed_call_id')->references('id')->on('closed_calls')->onDelete('cascade');
            $table->foreign('verifier_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifier_assignments');
    }
};
