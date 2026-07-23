<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tax_slabs', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_salary', 12, 2);
            $table->decimal('max_salary', 12, 2)->nullable(); // null means unlimited
            $table->decimal('tax_percentage', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tax_slabs');
    }
};