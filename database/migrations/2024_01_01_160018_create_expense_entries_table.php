<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('expense_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_type_id'); // Foreign key to expense types
            $table->date('date'); // Date of the entry
            $table->string('description'); // Description of the entry
            $table->enum('type', ['credit', 'debit']); // Type of entry (credit or debit)
            $table->decimal('amount', 10, 2); // Amount of the entry
            $table->text('remarks')->nullable(); // Optional remarks
            $table->timestamps(); // Created at and updated at timestamps

            // Foreign key constraint
            $table->foreign('expense_type_id')->references('id')->on('accounting_entries')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('expense_entries');
    }
}