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
        Schema::create('siswa_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nis'); // Menambahkan kembali kolom NIS
            $table->unique(['nis', 'deleted_at']);
            $table->string('nama_lengkap')->nullable(); // Tambahkan kolom nama_lengkap
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null')->index('siswa_profiles_kelas_id_foreign');
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('telepon_ayah')->nullable();
            $table->string('telepon_ibu')->nullable();
            $table->timestamps();
            $table->string('tempat_lahir')->nullable();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->string('foto')->nullable();
            $table->softDeletes(); // Tambahkan kolom deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_profiles', function (Blueprint $table) {
            $table->dropUnique(['nis', 'deleted_at']);
            $table->string('nis')->unique()->change();
            $table->dropSoftDeletes(); // Hapus kolom deleted_at
        });
        Schema::dropIfExists('siswa_profiles');
    }
};
