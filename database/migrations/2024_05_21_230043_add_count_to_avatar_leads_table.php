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
        Schema::table('avatar_leads', function (Blueprint $table) {
            $table->string('count')->nullable()->default(null);
            $table->date('Qadate')->nullable()->default(null); // Adding the Qadate column
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
            $table->dropColumn('count');
            $table->dropColumn('Qadate'); // Dropping the Qadate column
        });
    }
};
