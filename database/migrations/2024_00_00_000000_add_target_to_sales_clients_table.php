<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // add_target_to_sales_clients_table
public function up(): void
{
    Schema::table('sales_clients', function (Blueprint $table) {
        $table->unsignedInteger('target')->default(0)->after('name');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_clients', function (Blueprint $table) {
            //
        });
    }
};
