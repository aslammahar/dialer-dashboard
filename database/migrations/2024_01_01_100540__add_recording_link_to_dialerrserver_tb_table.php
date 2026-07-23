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
        Schema::table('dialers_servers', function (Blueprint $table) {
            $table->string('recording_link')->nullable()->after('server_ip');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dialers_servers', function (Blueprint $table) {
            //
            $table->dropColumn('recording_link');
        });
    }
};
