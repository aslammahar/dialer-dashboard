
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('closed_call_id')->constrained('closed_calls')->onDelete('cascade');
            $table->foreignId('validator_id')->nullable()->constrained('validators')->onDelete('set null');
            $table->string('customer_full_name')->nullable(); // Changed to nullable
            $table->string('state')->nullable();
            $table->string('carrier')->nullable();
            $table->unsignedBigInteger('clients_id')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_sales');
    }
};
