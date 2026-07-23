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
        Schema::table('carrier-search', function (Blueprint $table) {
            $table->json('carriers')->nullable(); // Add this line to create the carriers column
        });
    }
    
    public function down()
    {
        Schema::table('carrier-search', function (Blueprint $table) {
            $table->dropColumn('carriers'); // Remove this line on rollback
        });
    }
    
};
