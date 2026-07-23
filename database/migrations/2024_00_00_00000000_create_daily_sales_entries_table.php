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
    Schema::create('daily_sales_entries', function (Blueprint $table) {
        $table->id();
        $table->date('entry_date');
        $table->foreignId('sales_closer_id')->constrained('sales_closers')->cascadeOnDelete();
        $table->foreignId('sales_team_id')->nullable()->constrained('sales_teams')->nullOnDelete();
        $table->foreignId('sales_client_id')->nullable()->constrained('sales_clients')->nullOnDelete();
        $table->foreignId('sales_carrier_id')->nullable()->constrained('sales_carriers')->nullOnDelete();
        $table->enum('status', ['approved', 'pending'])->default('approved');
        $table->enum('sale_type', ['level', 'gi'])->nullable(); // only relevant when approved
        $table->decimal('avg_pre', 8, 2)->nullable();
        $table->string('notes')->nullable();
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();

        $table->index(['entry_date', 'sales_closer_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sales_entries');
    }
};
