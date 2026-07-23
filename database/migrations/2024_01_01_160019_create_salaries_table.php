<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('agent_name');
            $table->string('designation');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('account_title');
            $table->decimal('salary', 10, 2);
            $table->string('salary_month');
            $table->string('bank_code');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}