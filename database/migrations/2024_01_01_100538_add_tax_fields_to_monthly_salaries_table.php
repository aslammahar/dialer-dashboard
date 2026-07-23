<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('monthly_salaries', function (Blueprint $table) {
            $table->decimal('tax_amount', 12, 2)->default(0)->after('total_deductions');
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('tax_amount');
            $table->foreignId('tax_slab_id')->nullable()->after('tax_percentage')->constrained('tax_slabs')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('monthly_salaries', function (Blueprint $table) {
            $table->dropForeign(['tax_slab_id']);
            $table->dropColumn(['tax_amount', 'tax_percentage', 'tax_slab_id']);
        });
    }
};