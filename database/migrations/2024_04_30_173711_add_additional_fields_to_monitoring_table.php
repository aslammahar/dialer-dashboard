<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->text('greeting')->nullable()->after('monitor_date');
            $table->text('energy')->nullable()->after('greeting');
            $table->text('qa')->nullable()->after('energy');
            $table->enum('focus', ['Excellent', 'Satisfied', 'Not Satisfied'])->after('qa');
            $table->enum('positivity', ['Excellent', 'Satisfied', 'Not Satisfied'])->after('focus');
            $table->enum('confidence', ['Excellent', 'Satisfied', 'Not Satisfied'])->after('positivity');
            $table->enum('motivation', ['Excellent', 'Satisfied', 'Not Satisfied'])->after('confidence');
            $table->enum('energy_level', ['Excellent', 'Satisfied', 'Not Satisfied'])->after('motivation');
            $table->enum('smile', ['Excellent', 'Satisfied', 'Not Satisfied'])->after('energy_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->dropColumn([
                'greeting',
                'energy',
                'qa',
                'focus',
                'positivity',
                'confidence',
                'motivation',
                'energy_level',
                'smile'
            ]);
        });
    }
}
