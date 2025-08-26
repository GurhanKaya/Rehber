<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Performance için gerekli index'leri ekler
     */
    public function up(): void
    {
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('role'); // Role bazlı sorgular için
            $table->index('email'); // Email arama için
            $table->index(['name', 'surname']); // İsim arama için
            $table->index('created_at'); // Tarih bazlı sıralama için
        });

        // Tasks table indexes
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('status'); // Durum bazlı filtreleme için
            $table->index('type'); // Tip bazlı filtreleme için
            $table->index('assigned_user_id'); // Atanan kullanıcı için
            $table->index('created_at'); // Tarih bazlı sıralama için
            $table->index('deadline'); // Son tarih için
            $table->index(['status', 'type']); // Çoklu filtreleme için
            $table->index(['assigned_user_id', 'status']); // Kullanıcı + durum için
        });

        // Appointments table indexes
        Schema::table('appointments', function (Blueprint $table) {
            $table->index('user_id'); // Kullanıcı bazlı sorgular için
            $table->index('appointment_slot_id'); // Slot bazlı sorgular için
            $table->index('status'); // Durum bazlı filtreleme için
            $table->index('appointment_date'); // Tarih bazlı sorgular için
            $table->index(['user_id', 'appointment_date']); // Kullanıcı + tarih için
            $table->index(['status', 'appointment_date']); // Durum + tarih için
        });

        // Appointment slots table indexes
        Schema::table('appointment_slots', function (Blueprint $table) {
            $table->index('user_id'); // Personel bazlı sorgular için
            $table->index('day_of_week'); // Gün bazlı sorgular için
            $table->index(['user_id', 'day_of_week']); // Personel + gün için
        });

        // Task files table indexes
        Schema::table('task_files', function (Blueprint $table) {
            $table->index('task_id'); // Görev bazlı sorgular için
            $table->index('user_id'); // Kullanıcı bazlı sorgular için
            $table->index('created_at'); // Tarih bazlı sıralama için
        });

        // Task comments table indexes
        Schema::table('task_comments', function (Blueprint $table) {
            $table->index('task_id'); // Görev bazlı sorgular için
            $table->index('user_id'); // Kullanıcı bazlı sorgular için
            $table->index('created_at'); // Tarih bazlı sıralama için
        });

        // Task logs table indexes
        Schema::table('task_logs', function (Blueprint $table) {
            $table->index('task_id'); // Görev bazlı sorgular için
            $table->index('user_id'); // Kullanıcı bazlı sorgular için
            $table->index('created_at'); // Tarih bazlı sıralama için
            $table->index('action'); // Aksiyon bazlı filtreleme için
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['email']);
            $table->dropIndex(['name', 'surname']);
            $table->dropIndex(['created_at']);
        });

        // Tasks table indexes
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['type']);
            $table->dropIndex(['assigned_user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['deadline']);
            $table->dropIndex(['status', 'type']);
            $table->dropIndex(['assigned_user_id', 'status']);
        });

        // Appointments table indexes
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['appointment_slot_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['appointment_date']);
            $table->dropIndex(['user_id', 'appointment_date']);
            $table->dropIndex(['status', 'appointment_date']);
        });

        // Appointment slots table indexes
        Schema::table('appointment_slots', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['day_of_week']);
            $table->dropIndex(['user_id', 'day_of_week']);
        });

        // Task files table indexes
        Schema::table('task_files', function (Blueprint $table) {
            $table->dropIndex(['task_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        // Task comments table indexes
        Schema::table('task_comments', function (Blueprint $table) {
            $table->dropIndex(['task_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        // Task logs table indexes
        Schema::table('task_logs', function (Blueprint $table) {
            $table->dropIndex(['task_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['action']);
        });
    }
};
