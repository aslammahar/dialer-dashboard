<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            
            // User who uploaded the file
            $table->unsignedBigInteger('user_id');
            
            // Polymorphic relationship fields
            $table->string('attachable_type'); // e.g., 'App\Models\UserDetail'
            $table->unsignedBigInteger('attachable_id'); // e.g., 1 (UserDetail ID)
            
            // File information
            $table->string('file_name'); // Original file name
            $table->string('file_path'); // Storage path
            $table->string('file_type'); // File extension
            $table->string('mime_type')->nullable(); // MIME type
            $table->integer('file_size')->nullable(); // File size in bytes
            
            // Category for different types of attachments
            $table->string('category'); // e.g., 'cnic_front', 'cnic_back', 'profile_picture'
            
            // Timestamps
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['attachable_type', 'attachable_id']); // Polymorphic index
            $table->index(['user_id', 'category']); // User-category queries
            $table->index('category'); // Category-based queries
            $table->index('created_at'); // Time-based queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};