<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_bank_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('bank_name');
            $table->string('account_title');
            $table->string('account_number');
            $table->string('cnic_number');
            $table->integer('priority')->default(1);
            $table->enum('status', ['unverified', 'verified', 'rejected'])->default('unverified');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'priority']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_bank_details');
    }
};