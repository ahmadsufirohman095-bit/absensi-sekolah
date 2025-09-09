# Laporan Dokumentasi Menu Halaman Aplikasi Absensi Sekolah

Dokumen ini menjelaskan setiap menu halaman yang tersedia dalam aplikasi absensi sekolah, beserta fitur, fungsi, hak akses pengguna (Admin, Guru, Siswa), dan operasi CRUD (Create, Read, Update, Delete) yang dapat dilakukan.

---

## 1. Dashboard

*   **Penjelasan:** Halaman utama yang menampilkan ringkasan informasi penting dan statistik sesuai dengan peran pengguna.
*   **Fitur & Fungsi:**
    *   **Dashboard Admin:** Menampilkan grafik dan statistik absensi keseluruhan, jumlah pengguna, kelas, dan mata pelajaran. Memberikan gambaran umum kinerja sistem.
    *   **Dashboard Guru:** Menampilkan ringkasan jadwal mengajar, statistik absensi di kelas yang diajar, dan notifikasi terkait.
    *   **Dashboard Siswa:** Menampilkan ringkasan absensi pribadi, jadwal pelajaran, dan notifikasi.
*   **Hak Akses:**
    *   **Admin:** Penuh
    *   **Guru:** Penuh (sesuai data yang relevan)
    *   **Siswa:** Penuh (sesuai data yang relevan)
*   **CRUD:** Hanya operasi **Read** (melihat data).

---

## 2. Profil Pengguna

*   **Penjelasan:** Halaman untuk melihat dan mengelola informasi profil pribadi pengguna yang sedang login.
*   **Fitur & Fungsi:**
    *   **Lihat Profil:** Menampilkan nama, email, peran, dan detail profil spesifik.
    *   **Edit Profil:** Memperbarui informasi pribadi.
    *   **Ubah Kata Sandi:** Mengubah kata sandi akun.
    *   **Hapus Akun:** Menghapus akun sendiri.
*   **Hak Akses:**
    *   **Admin:** Penuh (untuk profilnya sendiri)
    *   **Guru:** Penuh (untuk profilnya sendiri)
    *   **Siswa:** Penuh (untuk profilnya sendiri)
*   **CRUD:**
    *   **Read:** Melihat detail profil.
    *   **Update:** Memperbarui informasi profil dan kata sandi.
    *   **Delete:** Menghapus akun sendiri.

---

## 3. Notifikasi

*   **Penjelasan:** Halaman untuk melihat daftar notifikasi yang diterima pengguna.
*   **Fitur & Fungsi:**
    *   **Daftar Notifikasi:** Menampilkan semua notifikasi.
    *   **Tandai Sebagai Dibaca:** Menandai notifikasi sebagai sudah dibaca.
*   **Hak Akses:**
    *   **Admin:** Penuh
    *   **Guru:** Penuh
    *   **Siswa:** Penuh
*   **CRUD:**
    *   **Read:** Melihat notifikasi.
    *   **Update:** Mengubah status notifikasi (sudah dibaca).

---

## 4. Scan Absensi

*   **Penjelasan:** Halaman yang digunakan oleh Admin atau Guru untuk melakukan absensi siswa menggunakan pemindai QR Code.
*   **Fitur & Fungsi:**
    *   **Pemindai QR Code:** Memindai QR Code siswa untuk mencatat kehadiran.
    *   **Daftar Siswa Terpindai:** Menampilkan daftar siswa yang telah berhasil diabsen.
*   **Hak Akses:**
    *   **Admin:** Penuh
    *   **Guru:** Penuh
    *   **Siswa:** Tidak memiliki akses.
*   **CRUD:**
    *   **Create:** Mencatat data absensi baru melalui pemindaian.
    *   **Read:** Melihat daftar siswa yang sudah dipindai.

---

## 5. Rekap Absensi

*   **Penjelasan:** Halaman untuk mengelola dan melihat rekapitulasi data absensi siswa.
*   **Fitur & Fungsi:**
    *   **CRUD Absensi:** Menambah, mengedit, dan menghapus data absensi secara manual.
    *   **Filter Data:** Menyaring data berdasarkan tanggal, kelas, siswa, dll.
    *   **Aksi Massal:** Menghapus beberapa data absensi sekaligus.
    *   **Ekspor Data:** Mengekspor data rekap absensi ke format Excel.
*   **Hak Akses:**
    *   **Admin:** Penuh (dapat mengelola semua data absensi).
    *   **Guru:** Penuh (dapat mengelola data absensi untuk jadwal yang diampunya, sesuai logika aplikasi).
    *   **Siswa:** Tidak memiliki akses.
*   **CRUD:**
    *   **Create:** Menambah absensi manual.
    *   **Read:** Melihat daftar dan ringkasan absensi.
    *   **Update:** Mengedit detail absensi.
    *   **Delete:** Menghapus absensi individual atau massal.

---

## 6. Pengaturan Aplikasi

*   **Penjelasan:** Halaman untuk mengelola berbagai pengaturan global aplikasi.
*   **Fitur & Fungsi:**
    *   **Konfigurasi Umum:** Mengatur nama sekolah, logo, dll.
    *   **Manajemen Aset Kartu:** Mengunggah dan menghapus gambar untuk kartu identitas.
    *   **Konfigurasi Cetak Kartu:** Mengatur tata letak cetak kartu identitas siswa.
*   **Hak Akses:**
    *   **Admin:** Penuh
    *   **Guru:** Tidak memiliki akses
    *   **Siswa:** Tidak memiliki akses
*   **CRUD:**
    *   **Read:** Melihat pengaturan.
    *   **Update:** Memperbarui pengaturan.
    *   **Create/Delete:** Mengelola aset gambar kartu.

---

## 7. Manajemen Pengguna (Users)

*   **Penjelasan:** Halaman untuk mengelola semua akun pengguna (Admin, Guru, Siswa).
*   **Fitur & Fungsi:**
    *   **CRUD Pengguna:** Menambah, mengedit, dan menghapus pengguna.
    *   **Aksi Massal:** Menghapus atau mengubah status beberapa akun sekaligus.
    *   **Aktifkan/Nonaktifkan Akun:** Mengubah status aktif akun.
    *   **Impor/Ekspor Pengguna:** Menambah atau mengunduh data pengguna secara massal via Excel.
    *   **Cetak Kartu Siswa:** Mencetak kartu identitas untuk siswa.
*   **Hak Akses:**
    *   **Admin:** Penuh
    *   **Guru:** Tidak memiliki akses
    *   **Siswa:** Tidak memiliki akses
*   **CRUD:**
    *   **Create:** Menambah pengguna baru.
    *   **Read:** Melihat daftar dan detail pengguna.
    *   **Update:** Mengedit pengguna dan mengubah status.
    *   **Delete:** Menghapus pengguna.

---

## 8. Manajemen Kelas

*   **Penjelasan:** Halaman untuk mengelola data kelas di sekolah.
*   **Fitur & Fungsi:**
    *   **CRUD Kelas:** Menambah, mengedit, dan menghapus kelas.
    *   **Kelola Anggota Kelas:** Menambah atau mengeluarkan siswa dari kelas.
    *   **Cetak Kartu Kelas:** Mencetak kartu identitas untuk semua siswa dalam satu kelas.
    *   **Cetak Jadwal Kelas:** Mencetak jadwal pelajaran untuk kelas tertentu.
*   **Hak Akses:**
    *   **Admin:** Penuh
    *   **Guru:** Tidak memiliki akses
    *   **Siswa:** Tidak memiliki akses
*   **CRUD:**
    *   **Create:** Menambah kelas baru.
    *   **Read:** Melihat daftar dan detail kelas.
    *   **Update:** Mengedit kelas dan anggota di dalamnya.
    *   **Delete:** Menghapus kelas.

---

## 9. Manajemen Mata Pelajaran

*   **Penjelasan:** Halaman untuk mengelola data mata pelajaran.
*   **Fitur & Fungsi:**
    *   **CRUD Mata Pelajaran:** Menambah, mengedit, dan menghapus mata pelajaran.
*   **Hak Akses:**
    *   **Admin:** Penuh
    *   **Guru:** Tidak memiliki akses
    *   **Siswa:** Tidak memiliki akses
*   **CRUD:**
    *   **Create:** Menambah mata pelajaran baru.
    *   **Read:** Melihat daftar mata pelajaran.
    *   **Update:** Mengedit mata pelajaran.
    *   **Delete:** Menghapus mata pelajaran.

---

## 10. Manajemen Jadwal Absensi

*   **Penjelasan:** Halaman untuk mengelola jadwal pelajaran yang menjadi dasar absensi. **Menu ini khusus untuk Admin.**
*   **Fitur & Fungsi:**
    *   **CRUD Jadwal:** Menambah, mengedit, dan menghapus jadwal.
    *   **Aksi Massal:** Menghapus beberapa jadwal sekaligus.
    *   **Impor/Ekspor Jadwal:** Menambah atau mengunduh data jadwal secara massal via Excel.
    *   **Isi Lembar Absensi:** Membuat dan mengisi lembar absensi manual berdasarkan jadwal yang dipilih.
*   **Hak Akses:**
    *   **Admin:** Penuh
    *   **Guru:** Tidak memiliki akses
    *   **Siswa:** Tidak memiliki akses
*   **CRUD:**
    *   **Create:** Menambah jadwal baru.
    *   **Read:** Melihat daftar jadwal.
    *   **Update:** Mengedit jadwal.
    *   **Delete:** Menghapus jadwal.

---

## 11. Jadwal Mengajar Guru

*   **Penjelasan:** Halaman khusus untuk Guru melihat jadwal mengajarnya.
*   **Fitur & Fungsi:**
    *   **Lihat Jadwal Pribadi:** Menampilkan jadwal mengajar guru yang sedang login.
    *   **Lihat Semua Jadwal:** Menampilkan seluruh jadwal pelajaran di sekolah.
    *   **Ekspor Jadwal:** Mengekspor jadwal mengajar ke format Excel.
*   **Hak Akses:**
    *   **Admin:** Tidak memiliki akses (mengelola via "Manajemen Jadwal Absensi").
    *   **Guru:** Penuh
    *   **Siswa:** Tidak memiliki akses.
*   **CRUD:** Hanya operasi **Read** dan **Export**.

---

## 12. Laporan Absensi Siswa

*   **Penjelasan:** Halaman khusus untuk Siswa melihat laporan absensi pribadinya.
*   **Fitur & Fungsi:**
    *   **Lihat Laporan Pribadi:** Menampilkan semua riwayat absensi.
    *   **Filter Tanggal:** Menyaring laporan berdasarkan rentang tanggal.
    *   **Ekspor Laporan:** Mengekspor laporan absensi ke format Excel.
*   **Hak Akses:**
    *   **Admin:** Tidak memiliki akses (melihat via "Rekap Absensi").
    *   **Guru:** Tidak memiliki akses (melihat via "Rekap Absensi").
    *   **Siswa:** Penuh
*   **CRUD:** Hanya operasi **Read** dan **Export**.

---

## 13. Halaman Reset Password

*   **Penjelasan:** Halaman ini memungkinkan pengguna yang lupa kata sandi untuk mengatur ulang kata sandi mereka melalui email.
*   **Fitur & Fungsi:**
    *   **Permintaan Reset Kata Sandi:** Pengguna memasukkan alamat email terdaftar untuk menerima tautan reset.
    *   **Formulir Reset Kata Sandi:** Setelah mengklik tautan dari email, pengguna dapat memasukkan kata sandi baru.
*   **Hak Akses:**
    *   **Admin:** Penuh
    *   **Guru:** Penuh
    *   **Siswa:** Penuh
    *   **Pengguna Belum Login:** Penuh
*   **CRUD:**
    *   **Update:** Memperbarui kata sandi pengguna.
