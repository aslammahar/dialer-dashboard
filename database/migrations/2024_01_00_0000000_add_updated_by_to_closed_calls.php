<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedByToClosedCalls extends Migration
{
    public function up()
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); // replace with an actual column name or remove ->after()
        });
    }

    public function down()
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('updated_by');
        });
    }
}