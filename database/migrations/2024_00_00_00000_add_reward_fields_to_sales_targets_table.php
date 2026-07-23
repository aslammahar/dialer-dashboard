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
    Schema::table('sales_targets', function (Blueprint $table) {
        $table->string('reward_headline')->default('the whole team earns a trip')->after('raw_target');
        $table->string('milestone_3_label')->default('Team Trip')->after('milestone_2_amount');
    });
}

public function down(): void
{
    Schema::table('sales_targets', function (Blueprint $table) {
        $table->dropColumn(['reward_headline', 'milestone_3_label']);
    });
}

    
};
