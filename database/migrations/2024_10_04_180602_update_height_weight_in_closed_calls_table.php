<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHeightWeightInClosedCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            // Rename the 'height_weight' column to 'height'
            $table->renameColumn('height_weight', 'height');

            // Add a new 'weight' column
            $table->decimal('weight', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            // Revert column name back to 'height_weight'
            $table->renameColumn('height', 'height_weight');

            // Drop the 'weight' column
            $table->dropColumn('weight');
        });
    }
}
