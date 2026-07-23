<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
      
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_parent')->default(0)->after('type');
            $table->unsignedBigInteger('client_id')->nullable()->after('is_parent');
            
            $table->foreign('client_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['is_parent', 'client_id']);
        });
    }
};
