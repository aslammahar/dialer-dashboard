<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_change_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('closed_call_id');
            $table->string('policy_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->string('source_file'); // 'lapse_report' or 'monthly_advance'
            $table->string('upload_batch')->nullable(); // UUID batch ID per upload session
            $table->unsignedBigInteger('changed_by');
            $table->timestamp('changed_at');
            $table->string('paid_to_date')->nullable(); // from lapse report
            $table->string('description')->nullable();  // from monthly advance
            $table->timestamps();

            $table->foreign('closed_call_id')->references('id')->on('closed_calls')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['closed_call_id', 'changed_at']);
            $table->index('upload_batch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_change_logs');
    }
};