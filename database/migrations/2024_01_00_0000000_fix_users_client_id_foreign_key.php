<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the wrong foreign key
            $table->dropForeign(['client_id']);
            
            // Add correct foreign key pointing to clients table
            $table->foreign('client_id')
                  ->references('id')
                  ->on('clients')  // ← Changed from 'users' to 'clients'
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            
            // Restore original (wrong) foreign key
            $table->foreign('client_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
};