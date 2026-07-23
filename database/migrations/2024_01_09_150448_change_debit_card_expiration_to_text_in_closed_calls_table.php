<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDebitCardExpirationToTextInClosedCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->text('debit_card_direct_express_expiration')->change();
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
            $table->string('debit_card_direct_express_expiration')->change();
        });
    }
}
