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
        Schema::create('dialerlist_tb', function (Blueprint $table) {
            $table->id();
            $table->string('dialer_ip', 150)->nullable();
            $table->string('dialer_weblink', 150)->nullable();
            $table->string('dialer_access', 300)->nullable();
            $table->string('dialer_no', 40)->nullable();
            $table->string('dialer_team', 40)->nullable();
            $table->text('recording_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dialerlist_tb');
    }
};
