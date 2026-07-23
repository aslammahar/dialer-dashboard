<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaleadsvoiceTable extends Migration
{
        public function up()
    {
        Schema::create('qaleadsvoice', function (Blueprint $table) {
            $table->id();
            $table->string('user_email', 191); // Set data type to VARCHAR(191)
            $table->string('phone_number')->nullable();
            $table->string('state')->nullable();
            $table->string('licenced_agent_name')->nullable();
            $table->string('status')->nullable();
            $table->text('comments')->nullable();
            $table->text('recordings')->nullable();
            $table->string('qa_person')->nullable();
            $table->timestamps();

            $table->foreign('user_email')->references('email')->on('users');
        });
    }



    public function down()
    {
        Schema::dropIfExists('qaleadsvoice');
    }
}
