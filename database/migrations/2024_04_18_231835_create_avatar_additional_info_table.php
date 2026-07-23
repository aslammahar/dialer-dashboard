<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvatarAdditionalInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avatar_additional_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->string('gender');
            $table->string('campaign');
            $table->string('closer');
            $table->string('group');
            $table->string('channel_group');
            $table->dateTime('SQLdate');
            $table->bigInteger('epoch');
            $table->string('server_ip');
            $table->string('SIPexten');
            $table->unsignedBigInteger('session_id');
            $table->string('dialed_label');
            $table->integer('script_width');
            $table->integer('script_height');
            $table->string('fullname');
            $table->unsignedBigInteger('agent_log_id');
            $table->string('user_group');
            $table->string('inOUT');
            $table->string('session_name');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('avatar_additional_info');
    }
}
