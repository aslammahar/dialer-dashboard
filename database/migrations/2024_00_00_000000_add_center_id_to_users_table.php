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
        $table->unsignedBigInteger('center_id')->nullable()->after('id');
        // Foreign key mat lagao abhi
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('center_id');
    });
}
};
