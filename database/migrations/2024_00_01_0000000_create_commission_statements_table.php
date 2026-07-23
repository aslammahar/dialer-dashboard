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
        Schema::create('commission_statements', function (Blueprint $table) {
            $table->id();
            $table->string('agent_name');
            $table->string('agent_no')->nullable();
            $table->integer('level')->nullable();
            $table->string('contract_code')->nullable();
            $table->string('policy_no')->nullable()->index();
            $table->string('insured_name')->nullable();
            $table->string('plan_name')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('process_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('check_date')->nullable();
            $table->string('check_no')->nullable();
            $table->decimal('annual_premium', 10, 2)->nullable();
            $table->decimal('monthly_premium', 10, 2)->nullable();
            $table->string('commission_rate')->nullable();
            $table->string('description')->nullable();
            $table->decimal('debit', 10, 2)->nullable();
            $table->decimal('commission_credit', 10, 2)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->string('parent_id')->nullable();
            $table->string('month');
            $table->integer('year');
            $table->integer('month_no');
            $table->string('file_name')->nullable();
            $table->timestamps();
            
            $table->index('agent_name');
            $table->index(['year', 'month_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_statements');
    }
};