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
        Schema::table('users', function (Blueprint $table) {
            $table->string('dialer_id', 191)->unique()->nullable();
            $table->string('pseudo_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_dialer_id_unique');
            $table->dropColumn('dialer_id');
            $table->dropColumn('pseudo_name');
        });
    }
};
