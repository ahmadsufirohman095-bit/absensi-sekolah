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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username');
            $table->unique(['username', 'deleted_at']);
            $table->string('identifier')->nullable()->index();
            $table->unique(['identifier', 'deleted_at']);
            $table->string('email'); // Email address
            $table->unique(['email', 'deleted_at']);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('siswa');
            $table->string('custom_role')->nullable();
            $table->rememberToken();
            $table->boolean('is_active')->default(true); // User account status
            $table->timestamp('last_login_at')->nullable(); // Last login timestamp
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index(); // Timestamp of last activity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username', 'deleted_at']);
            $table->dropUnique(['identifier', 'deleted_at']);
            $table->dropUnique(['email', 'deleted_at']);

            $table->string('username')->unique()->change();
            $table->string('identifier')->nullable()->unique()->index()->change();
            $table->string('email')->unique()->change();
        });

        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
