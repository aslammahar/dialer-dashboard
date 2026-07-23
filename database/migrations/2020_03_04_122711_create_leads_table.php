<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'leads', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('subject');
            $table->integer('user_id');            
            $table->string('name');
            $table->string('email')->unique();
            $table->string('state');
            $table->string('city');
            $table->string('zip_code');
            $table->string('address');
            $table->string('age');
            $table->string('spouse_age');
            $table->string('beneficiary');
            $table->string('plan');
            $table->string('smoker');
            $table->string('color_hobby');
            $table->string('licensed_agent_name');
            $table->string('call_back_time');            
            $table->integer('pipeline_id');
            $table->integer('stage_id');
            $table->string('sources')->nullable();
            $table->string('products')->nullable();
            $table->text('notes')->nullable();
            $table->string('priorities')->nullable();
            $table->string('labels')->nullable();
            $table->string('groups')->nullable();
            $table->integer('order')->default(0);
            $table->integer('created_by');
            $table->integer('is_active')->default(1);
            $table->integer('is_converted')->default(0);
            $table->date('date')->nullable();
            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
