<?php
// database/migrations/xxxx_xx_xx_create_number_lists_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('number_lists', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('data_vendor');
            $table->string('file_name');
            $table->integer('list_id')->unique();
            $table->integer('total_numbers');
            $table->integer('blocks_dubs_from_same_file')->default(0);
            $table->integer('dialer_scrubbing')->default(0);
            $table->integer('dnc_clean_numbers')->default(0);
            $table->integer('clean')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('number_lists');
    }
};