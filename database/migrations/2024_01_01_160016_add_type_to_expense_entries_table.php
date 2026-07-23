<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToExpenseEntriesTable extends Migration
{
    public function up()
    {
        Schema::table('monthly_expenses', function (Blueprint $table) {
            $table->string('type')->default('debit'); // or nullable if preferred
        });
    }

    public function down()
    {
        Schema::table('monthly_expenses', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
