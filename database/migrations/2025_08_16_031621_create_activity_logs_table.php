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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Admin yang melakukan aksi
            $table->foreignId('target_user_id')->nullable()->constrained('users')->onDelete('set null'); // User yang diubah statusnya
            $table->string('action'); // e.g., 'activated_account', 'deactivated_account'
            $table->boolean('old_status')->nullable();
            $table->boolean('new_status')->nullable();
            $table->text('description')->nullable(); // Deskripsi tambahan jika diperlukan
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
