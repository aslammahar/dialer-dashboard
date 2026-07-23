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
        Schema::table('tax_slabs', function (Blueprint $table) {
            $table->decimal('fixed_amount', 15, 2)->default(0)->after('max_salary')->comment('Fixed tax amount (e.g., Rs. 6000)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_slabs', function (Blueprint $table) {
            $table->dropColumn('fixed_amount');
        });
    }
};