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
        Schema::create('task_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->string('file_path');                    // path yerine file_path
            $table->string('file_name');                    // name yerine file_name
            $table->unsignedBigInteger('file_size')->nullable(); // size yerine file_size
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();  // uploaded_by yerine user_id
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            
            // Performance index'leri
            $table->index('task_id'); // Görev bazlı sorgular için
            $table->index('user_id'); // Kullanıcı bazlı sorgular için
            $table->index('created_at'); // Tarih bazlı sıralama için
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_files');
    }
}; 