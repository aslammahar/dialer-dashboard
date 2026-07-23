<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToAvatarQALeadsTable extends Migration {
    public function up() {
        Schema::table('avatar_q_a_leads', function (Blueprint $table) {
            $table->string('date_time')->nullable(); // Replace 'date_time' with the actual column name you want.
        });
    }

    public function down() {
        Schema::table('avatar_q_a_leads', function (Blueprint $table) {
            $table->dropColumn('date_time');
        });
    }
}

