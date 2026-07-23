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
        Schema::table('number_lists', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()
                ->constrained('data_vendors') // assumes you have a `vendors` table
                ->onDelete('cascade');   // optional: deletes related number_lists when vendor is deleted
        });
    }

    public function down(): void
    {
        Schema::table('number_lists', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
        });
    }
};
