<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToClosedCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->timestamp('timestamp')->nullable();
            $table->string('list_id_1')->nullable();
            $table->string('list_id_2')->nullable();
            $table->string('dialername')->nullable();
            $table->string('dialeragentname')->nullable();
            $table->string('agentname')->nullable();
            $table->string('teamname')->nullable();
            $table->string('lead_id')->nullable();
            $table->string('juniorcloser2')->nullable();
            $table->string('closername')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->dropColumn(['timestamp', 'list_id_1', 'list_id_2', 'dialername', 'dialeragentname', 'agentname', 'teamname', 'lead_id', 'juniorcloser2', 'closername']);
        });
    }
}
