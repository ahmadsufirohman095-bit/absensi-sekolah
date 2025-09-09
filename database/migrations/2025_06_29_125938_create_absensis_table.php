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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->index('absensis_user_id_foreign');
            $table->date('tanggal_absensi')->index();
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->string('attendance_type')->default('manual'); // Tipe absensi: manual, qr_code, dll.
            $table->foreignId('jadwal_absensi_id')->nullable()->constrained('jadwal_absensis')->onDelete('set null')->index('absensis_jadwal_absensi_id_foreign'); // Tambahkan ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
