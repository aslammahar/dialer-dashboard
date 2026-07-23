<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('monthly_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accountant_id')->constrained('accounting_entries')->onDelete('cascade');
            $table->date('month_year');
            $table->enum('expense_category', ['internet', 'electricity', 'rent', 'other','water','phone','maintenance','supplies']);
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_expenses');
    }
}