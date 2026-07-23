<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'center_id')) {
                $table->unsignedBigInteger('center_id')->nullable()->after('created_by')->index();
            }
        });
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'center_id')) {
                $table->dropIndex(['center_id']);
                $table->dropColumn('center_id');
            }
        });
    }
};

