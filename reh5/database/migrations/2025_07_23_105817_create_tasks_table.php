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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['public', 'private', 'cooperative']);
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->enum('status', ['bekliyor', 'devam ediyor', 'tamamlandı', 'iptal'])->default('bekliyor');
            $table->dateTime('deadline')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('assigned_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            
            // Performance index'leri
            $table->index('status'); // Durum bazlı filtreleme için
            $table->index('type'); // Tip bazlı filtreleme için
            $table->index('assigned_user_id'); // Atanan kullanıcı için
            $table->index('created_at'); // Tarih bazlı sıralama için
            $table->index('deadline'); // Son tarih için
            $table->index(['status', 'type']); // Çoklu filtreleme için
            $table->index(['assigned_user_id', 'status']); // Kullanıcı + durum için
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
