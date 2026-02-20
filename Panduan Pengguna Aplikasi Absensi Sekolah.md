# Panduan Pengguna Aplikasi Absensi Sekolah

Dokumen ini menjelaskan alur kerja aplikasi absensi sekolah yang berbasis QR Code untuk peran Admin, Guru, Pegawai, dan Siswa.

## 1. Alur Kerja & Peran Pengguna

Aplikasi ini membagi tanggung jawab pengguna secara spesifik:

*   **Administrator (Admin)**: 
    *   Mengelola **Data Master** (User, Kelas, Mata Pelajaran, Jadwal).
    *   Bertanggung jawab melakukan **Absensi kepada Pegawai** (Guru, TU, dan Staf Lainnya).
    *   Mengelola pengaturan aplikasi dan log aktivitas.
*   **Guru**: 
    *   Bertanggung jawab melakukan **Absensi kepada Siswa** di kelas pengampu sesuai jadwal.
    *   Berstatus sebagai pegawai yang **diabsen oleh Admin**.
*   **TU (Tata Usaha) & Lainnya (Other)**: 
    *   Berstatus sebagai **Pegawai**.
    *   Hanya menjadi objek absensi yang **diabsen oleh Admin**.
*   **Siswa**: 
    *   Menjadi objek absensi yang **diabsen oleh Guru**.
    *   Dapat melihat riwayat kehadiran pribadi.

---

## 2. Panduan untuk Admin

### Manajemen Data Master
Admin menginput data melalui menu:
1.  **Manajemen User**: Tambah Admin, Guru, Siswa, dan Staf.
2.  **Manajemen Kelas**: Tambah dan atur anggota kelas.
3.  **Manajemen Mapel**: Tambah mata pelajaran.
4.  **Manajemen Jadwal**: Atur jadwal mengajar guru.

### Distribusi QR Code
Untuk mulai melakukan absensi, Admin harus membagikan kode QR:
1.  Buka menu **Pengaturan & Utilitas** -> **Generate QR Codes**.
2.  Filter berdasarkan kategori (misal: Semua Siswa Kelas 7A).
3.  Klik **Unduh Semua Terfilter (ZIP SVG)**.
4.  Bagikan file SVG tersebut kepada pengguna (bisa dicetak atau disimpan di HP).

### Mengabsen Pegawai
Admin mencatat kehadiran Guru/Staf melalui:
*   Menu **Scan Absensi** (jika ada scanner terpusat).
*   Menu **Rekap Absensi Pegawai** untuk pencatatan manual.

---

## 3. Panduan untuk Guru

### Mengabsen Siswa
1.  Buka menu **Jadwal Mengajar**.
2.  Pilih jadwal yang sedang berstatus **"Berlangsung"**.
3.  Klik tombol **Mulai Absensi QR**.
4.  Arahkan kamera ke QR Code siswa. Kehadiran akan tercatat otomatis.
5.  Jika siswa tidak membawa kode, klik nama siswa di daftar jadwal untuk mengisi status secara manual.

---

## 4. Panduan untuk Siswa & Pegawai (Staf)

### Cara Melakukan Absensi
1.  Siapkan file **QR Code SVG** yang telah diberikan oleh Admin.
2.  Tunjukkan kode tersebut kepada petugas absensi:
    *   Siswa menunjukkan kode kepada **Guru**.
    *   Guru dan Staf menunjukkan kode kepada **Admin**.
3.  Pastikan muncul konfirmasi "Berhasil" pada perangkat petugas.

### Memantau Kehadiran
*   **Siswa**: Login dan buka menu **Laporan Absensi** untuk melihat detail kehadiran per mapel.
*   **Pegawai**: Melihat ringkasan kehadiran melalui dashboard masing-masing.

---

## 5. Tips & Pemecahan Masalah

*   **Gambar QR Pecah**: Gunakan file format **SVG** saat mencetak agar kualitas tetap tajam dan mudah terbaca kamera.
*   **Kamera Tidak Aktif**: Pastikan browser Anda memiliki izin untuk mengakses kamera (klik ikon gembok di alamat bar browser).
*   **Loading Lama**: Jika setelah mengunduh file ZIP indikator loading di atas layar tetap berjalan, cukup tekan **F5 atau Refresh**. File Anda sudah aman terunduh.
