<?php
// database/migrations/create_vendor_lists_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vendor_lists', function (Blueprint $table) {
            $table->id();
            $table->string('list_id')->unique();
            $table->integer('sales')->default(0);
            $table->string('dialer_name')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('total_numbers')->default(0);
            $table->integer('dnc')->default(0);
            $table->integer('duplicate')->default(0);
            $table->integer('clean')->default(0);
            $table->decimal('sales_conversion', 8, 4)->default(0);
            $table->integer('xfers')->default(0);
            $table->decimal('xfers_sales_conversion', 8, 4)->default(0);
            $table->decimal('xfers_clean_conversion', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_lists');
    }
};