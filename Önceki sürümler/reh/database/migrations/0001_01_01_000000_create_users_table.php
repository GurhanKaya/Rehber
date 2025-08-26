<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // USERS TABLOSU
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // Ad
            $table->string('surname')->nullable();       // Soyad
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('personel');     // user veya admin
            $table->string('title')->nullable();         // Ünvan
            $table->string('phone')->nullable();         // Telefon
            $table->string('photo')->nullable();         // Profil fotoğrafı
            $table->string('department')->nullable();    // Departman
            $table->rememberToken();
            $table->timestamps();
        });

        // PASSWORD RESET TOKENS TABLOSU
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // SESSIONS TABLOSU
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};

