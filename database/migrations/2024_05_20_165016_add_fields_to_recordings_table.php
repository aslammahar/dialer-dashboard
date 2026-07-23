<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('recordings', function (Blueprint $table) {
            $table->string('dialer_name')->nullable();
            $table->string('dialer_id')->nullable();
            $table->time('audio_duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recordings', function (Blueprint $table) {
            $table->dropColumn('dialer_name');
            $table->dropColumn('dialer_id');
            $table->dropColumn('audio_duration');
        });
    }
};
