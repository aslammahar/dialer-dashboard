<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServerIpAndRecordingFilenameToRecordingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recordings', function (Blueprint $table) {
            $table->string('server_ip', 45)->nullable()->after('id'); // Assuming IPv6 compatibility with max 45 characters
            $table->string('recording_filename')->nullable()->after('server_ip');
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
            $table->dropColumn(['server_ip', 'recording_filename']);
        });
    }
}
