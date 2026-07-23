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
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->enum('signature_type', ['email', 'otp', 'voice'])->nullable()->after('recording_status');
            $table->string('call_id')->nullable()->after('signature_type');
            $table->enum('dialer_name_new', ['dialer1', 'dialer2', 'dialer3', 'dialer4', 'dialer5'])->nullable()->after('call_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->dropColumn(['signature_type', 'call_id', 'dialer_name_new']);
        });
    }
};