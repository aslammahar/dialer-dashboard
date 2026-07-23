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
        Schema::create('queue_sale_comments', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->foreignId('queue_sale_id')->constrained()->onDelete('cascade');
            // Assuming you have a 'users' table setup
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            // Content and threading
            $table->text('content');
            // Parent ID allows comments to be replies to other comments
            $table->foreignId('parent_id')->nullable()->constrained('queue_sale_comments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_sale_comments');
    }
};