<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. !!bu randevular tablosu!!
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // personel
            $table->unsignedBigInteger('appointment_slot_id'); // hangi slot üzerinden alındı
            $table->string('name'); // randevu alan kişi adı
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('date');
            $table->string('status')->default('bekliyor'); // bekliyor, onaylandı, iptal vb.
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('appointment_slot_id')->references('id')->on('appointment_slots')->onDelete('cascade');
            
            // Performance index'leri
            $table->index('user_id'); // Kullanıcı bazlı sorgular için
            $table->index('appointment_slot_id'); // Slot bazlı sorgular için
            $table->index('status'); // Durum bazlı filtreleme için
            $table->index('date'); // Tarih bazlı sorgular için
            $table->index(['user_id', 'date']); // Kullanıcı + tarih için
            $table->index(['status', 'date']); // Durum + tarih için
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
