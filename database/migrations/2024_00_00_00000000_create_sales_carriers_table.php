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
    Schema::create('sales_carriers', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // CICA STANDARD, Aetna(CVS), AFLAC, Securico Life, CICA GI, GTL (Guaranteed), AmAm, AIG
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_carriers');
    }
};
