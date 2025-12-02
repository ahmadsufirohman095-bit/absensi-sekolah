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
        Schema::create('absensi_pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jadwal_absensi_pegawai_id')->nullable()->constrained('jadwal_absensi_pegawais')->onDelete('set null');
            $table->date('tanggal_absensi');
            $table->enum('status', ['hadir', 'terlambat', 'sakit', 'izin', 'alpha']);
            $table->time('waktu_masuk')->nullable();
            $table->string('keterangan')->nullable();
            $table->enum('attendance_type', ['manual', 'qr_code'])->default('manual');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_pegawais');
    }
};
