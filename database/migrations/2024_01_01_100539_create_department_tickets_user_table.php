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
    Schema::create('department_support_ticket_user', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dept_suprt_tckt_id')->constrained('department_support_tickets')->onDelete('cascade');
        $table->foreignId(column: 'user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('department_support_ticket_user');
}
};
