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
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->string('agent_status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->dropColumn('agent_status');
        });
    }
};
