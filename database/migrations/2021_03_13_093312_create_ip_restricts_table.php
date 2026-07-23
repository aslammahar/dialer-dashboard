<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpRestrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_restricts', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->integer('created_by');
            $table->timestamps(); // This will add 'created_at' and 'updated_at' columns
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ip_restricts');
    }
}
