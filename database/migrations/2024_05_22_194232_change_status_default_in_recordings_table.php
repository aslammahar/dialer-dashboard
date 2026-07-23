<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('recordings', function (Blueprint $table) {
            // Change the default value of the 'status' column to 'rec missing'
            $table->string('status')->default('rec missing')->change();
        });

        // Update existing records with null status to 'rec missing'
        DB::table('recordings')
            ->whereNull('status')
            ->update(['status' => 'rec missing']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This method is intentionally left empty because we do not want to rollback the changes
    }
};
