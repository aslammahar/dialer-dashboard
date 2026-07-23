<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilledByToMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->unsignedBigInteger('filled_by')->nullable()->after('smile'); // Adding filled_by field
            $table->foreign('filled_by')->references('id')->on('users')->onDelete('set null'); // Foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->dropForeign(['filled_by']);
            $table->dropColumn('filled_by');
        });
    }
}
