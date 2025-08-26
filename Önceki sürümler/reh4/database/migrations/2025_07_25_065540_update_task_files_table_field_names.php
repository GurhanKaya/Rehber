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
        Schema::table('task_files', function (Blueprint $table) {
            // Rename columns to match the component usage
            $table->renameColumn('path', 'file_path');
            $table->renameColumn('name', 'file_name');
            $table->renameColumn('size', 'file_size');
            $table->renameColumn('uploaded_by', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_files', function (Blueprint $table) {
            // Revert column names back to original
            $table->renameColumn('file_path', 'path');
            $table->renameColumn('file_name', 'name');
            $table->renameColumn('file_size', 'size');
            $table->renameColumn('user_id', 'uploaded_by');
        });
    }
};
