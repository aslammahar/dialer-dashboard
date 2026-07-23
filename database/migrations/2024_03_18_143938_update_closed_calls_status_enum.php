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
        // Use raw SQL to modify the column type
        DB::statement("ALTER TABLE closed_calls MODIFY status ENUM('pending',
         'approved',
         'DNF',
         'Cancelled',
         'NSF',
         'DNC',
         'Underwriting',
         'Need to Reach',
        'rejected',
        'funded',
        'charged_backed'
        
             ) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            // Revert the changes if needed
            // You can't directly change the enum in a down migration
            // If you need to revert, you'd likely need to drop and recreate the column
            // This down() method is just a placeholder and you might need to adjust it based on your requirements
        });
    }
};
