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
        Schema::table('users', function (Blueprint $table) {
            $table->string('dialer_password')->default('hello123');
            $table->string('campaign')->nullable();
            $table->string('usertype')->nullable();
            $table->string('dialer_no')->nullable();
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('dialer_password');
            $table->dropColumn('campaign');
            $table->dropColumn('usertype');
            $table->dropColumn('dialer_no');
            $table->dropColumn('status');
        });
    }
};
