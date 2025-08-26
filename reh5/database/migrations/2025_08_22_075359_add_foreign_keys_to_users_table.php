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
        Schema::table('users', function (Blueprint $table) {
            // Department ve Title tablolarından sonra foreign key constraint'leri ekle
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('title_id')->references('id')->on('titles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Foreign key constraint'leri kaldır
            $table->dropForeign(['department_id']);
            $table->dropForeign(['title_id']);
        });
    }
};
