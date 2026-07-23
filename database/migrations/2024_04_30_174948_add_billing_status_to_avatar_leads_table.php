<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingStatusToAvatarLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('avatar_leads', function (Blueprint $table) {
            $table->string('billing_status')->nullable()->after('xferSubmission');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('avatar_leads', function (Blueprint $table) {
            $table->dropColumn('billing_status');
        });
    }
}
