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
    Schema::create('sales_targets', function (Blueprint $table) {
        $table->id();
        $table->date('month')->unique(); // first day of the target month
        $table->decimal('spd_target', 4, 2)->default(2.0);          // e.g. SPD of 2 per day
        $table->decimal('monthly_spd_target', 4, 2)->default(2.5);  // e.g. SPD of 2.5 this month
        $table->unsignedInteger('raw_target')->default(40);         // raw approved-count target
        $table->string('milestone_1_label')->default('Movie Night for Closers');
        $table->string('milestone_2_label')->default('Cash Bonus');
        $table->string('milestone_2_amount')->nullable();            // e.g. "100k"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};
