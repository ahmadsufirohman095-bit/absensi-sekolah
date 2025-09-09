## 1. Diagram Use Case

```plantuml
@startuml
left to right direction

actor Admin
actor Guru
actor Siswa

rectangle "Aplikasi Absensi Sekolah" {
  ' Use cases utama yang diakses aktor
  usecase "(Login)" as UC_Login
  usecase "(Melakukan Scan Absensi)" as UC_Scan
  usecase "(Mengelola Rekap Absensi)" as UC_Rekap
  usecase "(Melihat Laporan Absensi)" as UC_Laporan
  usecase "(Mengelola Pengguna)" as UC_Users
  usecase "(Mengelola Jadwal)" as UC_Jadwal

  ' Use cases fungsional (untuk relasi)
  usecase "(Lupa Kata Sandi)" as UC_Forgot
  usecase "(Export Data ke Excel)" as UC_Export
  usecase "(Memvalidasi Jadwal Aktif)" as UC_ValidateSchedule
  usecase "(Otentikasi & Otorisasi)" as UC_Auth
}

' Relasi Aktor ke Use Case
Admin -- UC_Users
Admin -- UC_Jadwal
Guru -- UC_Scan
Guru -- UC_Rekap
Siswa -- UC_Laporan
(Admin, Guru, Siswa) -- UC_Login

' Relasi <<extend>> (fungsionalitas opsional)
' Panah dari use case opsi ke use case dasar
UC_Forgot .> UC_Login : <<extend>>
UC_Export .> UC_Rekap : <<extend>>
UC_Export .> UC_Laporan : <<extend>>

' Relasi <<include>> (fungsionalitas wajib)
' Panah dari use case dasar ke use case yang dibutuhkan
UC_Scan ..> UC_ValidateSchedule : <<include>>
UC_Users ..> UC_Auth : <<include>>
UC_Jadwal ..> UC_Auth : <<include>>
UC_Rekap ..> UC_Auth : <<include>>
UC_Laporan ..> UC_Auth : <<include>>

@enduml
```

---

## 2. ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    USERS ||--o{ ADMIN_PROFILES : "has"
    USERS ||--o{ GURU_PROFILES : "has"
    USERS ||--o{ SISWA_PROFILES : "has"

    KELAS ||--o{ SISWA_PROFILES : "contains"
    KELAS ||--o{ JADWAL_ABSENSI : "has"
    KELAS ||--o{ KELAS_MATA_PELAJARAN : "has"

    MATA_PELAJARAN ||--o{ GURU_MATA_PELAJARAN : "has"
    MATA_PELAJARAN ||--o{ KELAS_MATA_PELAJARAN : "has"
    MATA_PELAJARAN ||--o{ JADWAL_ABSENSI : "has"

    GURU_PROFILES ||--o{ GURU_MATA_PELAJARAN : "teaches"
    GURU_PROFILES ||--o{ JADWAL_ABSENSI : "creates"

    JADWAL_ABSENSI ||--o{ ABSENSI : "generates"

    SISWA_PROFILES ||--o{ ABSENSI : "attends"

    USERS {
        int id PK
        string name
        string email
        string password
        string role
        datetime email_verified_at
        datetime created_at
        datetime updated_at
    }

    ADMIN_PROFILES {
        int id PK
        int user_id FK
        string full_name
        string phone_number
        string address
        datetime created_at
        datetime updated_at
    }

    GURU_PROFILES {
        int id PK
        int user_id FK
        string nip
        string full_name
        string phone_number
        string address
        datetime created_at
        datetime updated_at
    }

    SISWA_PROFILES {
        int id PK
        int user_id FK
        int kelas_id FK
        string nis
        string full_name
        string gender
        date date_of_birth
        string address
        datetime created_at
        datetime updated_at
    }

    KELAS {
        int id PK
        string name
        int wali_kelas_id FK
        datetime created_at
        datetime updated_at
    }

    MATA_PELAJARAN {
        int id PK
        string name
        datetime created_at
        datetime updated_at
    }

    GURU_MATA_PELAJARAN {
        int guru_profile_id PK,FK
        int mata_pelajaran_id PK,FK
        datetime created_at
        datetime updated_at
    }

    KELAS_MATA_PELAJARAN {
        int kelas_id PK,FK
        int mata_pelajaran_id PK,FK
        datetime created_at
        datetime updated_at
    }

    JADWAL_ABSENSI {
        int id PK
        int kelas_id FK
        int mata_pelajaran_id FK
        int guru_profile_id FK
        string hari
        time jam_mulai
        time jam_selesai
        string kode_qr
        datetime created_at
        datetime updated_at
    }

    ABSENSI {
        int id PK
        int jadwal_absensi_id FK
        int siswa_profile_id FK
        date tanggal
        string status "Hadir, Izin, Sakit, Alpha"
        time waktu_masuk
        time waktu_keluar
        string keterangan
        datetime created_at
        datetime updated_at
    }

    ACTIVITY_LOG {
        int id PK
        int user_id FK
        string activity_type
        string description
        datetime created_at
        datetime updated_at
    }

    NOTIFICATIONS {
        string id PK
        string type
        string notifiable_type
        int notifiable_id
        text data
        datetime read_at
        datetime created_at
        datetime updated_at
    }

    SETTINGS {
        int id PK
        string key
        string value
        datetime created_at
        datetime updated_at
    }

    PRINT_CARD_CONFIGS {
        int id PK
        string config_name
        text config_data
        datetime created_at
        datetime updated_at
    }
```


## 3. Activity Diagram untuk Proses Login

```plantuml
@startuml
start
:Pengguna membuka halaman Login;
:Memasukkan ID Pengguna (NIS / NIP) dan Kata Sandi;
:Klik Tombol Login;
:Sistem memvalidasi kredensial;
if (Kredensial Valid?) then (Ya)
  :Sistem memeriksa peran pengguna;
  if (Peran Pengguna? == Admin) then (Admin)
    :Arahkan ke Dashboard Admin;
  elseif (Peran Pengguna? == Guru) then (Guru)
    :Arahkan ke Dashboard Guru;
  else (Siswa)
    :Arahkan ke Dashboard Siswa;
  endif
else (Tidak)
  :Tampilkan pesan error: Kredensial tidak valid;
  -> Memasukkan ID Pengguna (NIS / NIP) dan Kata Sandi;
endif
stop
@enduml
```

---

## 4. Activity Diagram untuk Proses Absensi QR Code

```plantuml
@startuml
start
:Guru membuka halaman Scan Absensi;
if (Memilih Jadwal Absensi (jika ada pilihan)?) then (Ya)
  :Sistem mengaktifkan pemindai QR Code;
  repeat
    :Guru memindai QR Code Siswa;
    if (QR Code Valid dan Siswa Terdaftar?) then (Ya)
      :Sistem mencatat absensi: Hadir, Waktu Masuk Otomatis;
      :Tampilkan daftar siswa yang sudah dipindai;
    else (Tidak)
      :Tampilkan pesan error: QR Code tidak valid/Siswa tidak terdaftar;
    endif
  repeat while (Ada lagi siswa yang akan dipindai?) is (Ya)
else (Tidak)
  :pemindai QR Code tidak akan aktif;
endif
stop
@enduml
```

---

## 5. Activity Diagram untuk Proses Tambah Absensi Manual

```plantuml
@startuml
start
:Guru membuka halaman Rekap Absensi;
:Klik tombol Tambah Absensi Manual;
:Form Tambah Absensi Manual ditampilkan;
:Memilih Siswa, Jadwal, Tanggal, Status, Jam, Keterangan (opsional);
:Klik tombol Simpan;
:Sistem memvalidasi input;
if (Input Valid?) then (Ya)
  :Sistem menyimpan data absensi manual;
  :Tampilkan pesan sukses;
  :Kembali ke halaman Rekap Absensi;
else (Tidak)
  :Tampilkan pesan error: Input tidak valid;
  -> Form Tambah Absensi Manual ditampilkan;
endif
stop
@enduml
```

---

## 6. Activity Diagram untuk Proses Tambah User

```plantuml
@startuml
start
:Admin membuka halaman Manajemen Pengguna;
:Klik tombol Tambah Pengguna Baru;
:Form Tambah Pengguna ditampilkan;
:Memasukkan Nama, Email, Kata Sandi, Peran, dan Detail Profil;
:Klik tombol Simpan;
:Sistem memvalidasi input;
if (Input Valid?) then (Ya)
  :Sistem menyimpan data pengguna baru;
  :Tampilkan pesan sukses;
  :Kembali ke halaman Manajemen Pengguna;
else (Tidak)
  :Tampilkan pesan error: Input tidak valid;
  -> Form Tambah Pengguna ditampilkan;
endif
stop
@enduml
```

---

## 7. Activity Diagram untuk Proses Tambah Kelas

```plantuml
@startuml
start
:Admin membuka halaman Manajemen Kelas;
:Klik tombol Tambah Kelas Baru;
:Form Tambah Kelas ditampilkan;
:Memasukkan Nama Kelas dan Memilih Wali Kelas (opsional);
:Klik tombol Simpan;
:Sistem memvalidasi input;
if (Input Valid?) then (Ya)
  :Sistem menyimpan data kelas baru;
  :Tampilkan pesan sukses;
  :Kembali ke halaman Manajemen Kelas;
else (Tidak)
  :Tampilkan pesan error: Input tidak valid;
  -> Form Tambah Kelas ditampilkan;
endif
stop
@enduml
```

---

## 8. Activity Diagram untuk Proses Tambah Mata Pelajaran

```plantuml
@startuml
start
:Admin membuka halaman Manajemen Mata Pelajaran;
:Klik tombol Tambah Mata Pelajaran Baru;
:Form Tambah Mata Pelajaran ditampilkan;
:Memasukkan Nama Mata Pelajaran;
:Klik tombol Simpan;
:Sistem memvalidasi input;
if (Input Valid?) then (Ya)
  :Sistem menyimpan data mata pelajaran baru;
  :Tampilkan pesan sukses;
  :Kembali ke halaman Manajemen Mata Pelajaran;
else (Tidak)
  :Tampilkan pesan error: Input tidak valid;
  -> Form Tambah Mata Pelajaran ditampilkan;
endif
stop
@enduml
```

---

## 9. Activity Diagram untuk Proses Cetak Kartu Absensi

```plantuml
@startuml
start
:Admin membuka halaman Manajemen Pengguna atau Manajemen Kelas;
if (Pilih opsi Cetak Kartu Siswa (individual) atau Cetak Kartu Kelas (massal)?) then (Individual)
  :Pilih Siswa yang akan dicetak kartunya;
else (Massal)
  :Pilih Kelas yang akan dicetak kartunya;
endif
:Sistem mengambil data siswa dan konfigurasi cetak kartu;
:Sistem membuat pratinjau kartu absensi;
if (Admin menyetujui pratinjau?) then (Ya)
  :Sistem menghasilkan file cetak (misal: PDF);
  :Admin mengunduh/mencetak kartu;
else (Tidak)
  :Kembali ke pratinjau atau opsi pemilihan;
  -> Sistem membuat pratinjau kartu absensi;
endif
stop
@enduml
```

---

## 10. Activity Diagram untuk Proses Reset Password

```plantuml
@startuml
start
:Pengguna membuka halaman Login;
:Klik "Lupa Kata Sandi";
:Pengguna memasukkan alamat email;
:Klik tombol "Kirim Tautan Reset Kata Sandi";
:Sistem memvalidasi alamat email;
if (Email terdaftar?) then (Ya)
  :Sistem mengirimkan tautan reset kata sandi ke email pengguna;
  :Pengguna membuka email dan mengklik tautan reset;
  :Halaman Reset Kata Sandi ditampilkan;
  :Pengguna memasukkan Kata Sandi Baru dan Konfirmasi Kata Sandi;
  :Klik tombol "Reset Kata Sandi";
  :Sistem memvalidasi kata sandi baru;
  if (Kata Sandi Valid dan Cocok?) then (Ya)
    :Sistem memperbarui kata sandi pengguna;
    :Tampilkan pesan sukses: Kata sandi berhasil direset;
    :Arahkan ke halaman Login;
  else (Tidak)
    :Tampilkan pesan error: Kata sandi tidak valid atau tidak cocok;
    -> Halaman Reset Kata Sandi ditampilkan;
  endif
else (Tidak)
  :Tampilkan pesan error: Email tidak terdaftar;
  -> Pengguna memasukkan alamat email;
endif
stop
@enduml
```