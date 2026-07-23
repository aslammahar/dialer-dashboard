<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_sales_entries', function (Blueprint $table) {
            $table->string('leads_id')->nullable()->after('sales_carrier_id');
        });
    }

    public function down(): void
    {
        Schema::table('daily_sales_entries', function (Blueprint $table) {
            $table->dropColumn('leads_id');
        });
    }
};