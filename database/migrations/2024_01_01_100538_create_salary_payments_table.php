<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_salary_id')->constrained('monthly_salaries')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_bank_detail_id')->constrained()->onDelete('cascade');
            $table->string('bank_name');
            $table->string('account_title');
            $table->string('account_number');
            $table->decimal('payment_amount', 15, 2);
            $table->enum('payment_status', ['pending', 'sent', 'declined'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_payments');
    }
};