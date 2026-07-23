<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('avatar_leads', function (Blueprint $table) {
            $table->text('recording')->nullable();
            $table->string('Isgreetings')->nullable();
            $table->string('Ispitch_call_about')->nullable();
            $table->string('Isage')->nullable();
            $table->string('Issmoker')->nullable();
            $table->string('Ishealth1')->nullable();
            $table->string('Isbeneficiary')->nullable();
            $table->string('Isaccount')->nullable();
            $table->string('Isplan')->nullable();
            $table->string('Istransfer_details')->nullable();
            $table->string('Isxfer_consent')->nullable();
            $table->string('rebuttals')->nullable();
            $table->text('Qacomments')->nullable();
            $table->string('QAstatus')->nullable()->default('pending');
            $table->unsignedBigInteger('QapersonId')->nullable();
            $table->integer('use_of_rebuttals')->nullable();
            $table->integer('no_of_refusals')->nullable();
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avatar_leads', function (Blueprint $table) {
            $table->dropColumn('recording');
            $table->dropColumn('Isgreetings');
            $table->dropColumn('Ispitch_call_about');
            $table->dropColumn('Isage');
            $table->dropColumn('Issmoker');
            $table->dropColumn('Ishealth1');
            $table->dropColumn('Isbeneficiary');
            $table->dropColumn('Isaccount');
            $table->dropColumn('Isplan');
            $table->dropColumn('Istransfer_details');
            $table->dropColumn('Isxfer_consent');
            $table->dropColumn('rebuttals');
            $table->dropColumn('Qacomments');
            $table->dropColumn('QAstatus');
            $table->dropColumn('QapersonId');
            $table->dropColumn('use_of_rebuttals');
            $table->dropColumn('no_of_refusals');
            $table->dropColumn('testing');
            
        });
    }
};
