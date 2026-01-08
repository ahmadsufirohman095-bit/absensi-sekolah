<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('FAQ (Pertanyaan yang Sering Diajukan)') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-4 sm:p-6">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Daftar Pertanyaan yang Sering Diajukan</h3>

                <?php
                $userRole = Auth::user()->role ?? 'guest'; // Ambil peran pengguna, default 'guest' jika tidak login
                $faqs = [
                    [
                        'category_title' => 'Pengantar Aplikasi Absensi Sekolah',
                        'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                        'items' => [
                            [
                                'question' => 'Apa itu Aplikasi Absensi Sekolah?',
                                'answer' => 'Aplikasi Absensi Sekolah adalah sistem manajemen kehadiran berbasis web yang dirancang untuk memudahkan proses pencatatan absensi siswa dan pegawai di lingkungan sekolah. Aplikasi ini mendukung berbagai peran pengguna (Admin, Guru, Siswa, TU, Lainnya) dengan fitur-fitur yang disesuaikan untuk setiap peran.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Siapa saja pengguna aplikasi ini?',
                                'answer' => 'Aplikasi ini dirancang untuk beberapa jenis pengguna utama:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li><strong>Administrator (Admin):</strong> Bertanggung jawab atas konfigurasi sistem, manajemen pengguna, kelas, mata pelajaran, jadwal, serta rekapitulasi absensi siswa dan pegawai.</li>
                                        <li><strong>Guru:</strong> Bertanggung jawab untuk mengelola absensi siswa di kelas yang diajar, melihat jadwal mengajar, memantau kehadiran siswa, serta dapat mencetak kartu absensi pegawai untuk dirinya sendiri atau guru lain.</li>
                                        <li><strong>TU (Tata Usaha):</strong> Bertanggung jawab atas manajemen data kepegawaian, pengelolaan jadwal dan rekap absensi pegawai (karyawan), serta dapat mencetak kartu absensi pegawai.</li>
                                        <li><strong>Lainnya (Other):</strong> Pengguna dengan hak akses yang disesuaikan, biasanya terkait dengan manajemen pegawai dan absensi (karyawan), serta dapat mencetak kartu absensi pegawai.</li>
                                        <li><strong>Siswa:</strong> Bertanggung jawab untuk melakukan absensi menggunakan QR Code dan melihat riwayat kehadiran serta jadwal pelajaran mereka.</li>
                                    </ol>',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Apa saja fitur utama aplikasi ini?',
                                'answer' => 'Fitur-fitur utama Aplikasi Absensi Sekolah meliputi:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Absensi berbasis QR Code untuk siswa.</li>
                                        <li>Manajemen pengguna (Admin, Guru, Siswa, TU, Lainnya) dengan peran dan hak akses yang berbeda.</li>
                                        <li>Manajemen kelas, mata pelajaran, dan jadwal absensi siswa.</li>
                                        <li>Manajemen jadwal dan rekapitulasi absensi pegawai.</li>
                                        <li>Cetak kartu absensi kustom untuk siswa dan pegawai.</li>
                                        <li>Rekapitulasi dan pelaporan absensi yang komprehensif untuk siswa dan pegawai.</li>
                                        <li>Kustomisasi tampilan aplikasi (nama, halaman login, kartu absensi).</li>
                                        <li>Sistem notifikasi untuk informasi penting.</li>
                                        <li>Ekspor dan impor data untuk memudahkan pengelolaan.</li>
                                    </ol>',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                        ],
                    ],
                    [
                        'category_title' => 'Untuk Pengguna Umum (Login, Profil, Notifikasi)',
                        'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                        'items' => [
                            [
                                'question' => 'Bagaimana cara masuk (login) ke aplikasi?',
                                'answer' => 'Untuk masuk ke aplikasi:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Buka peramban web Anda dan kunjungi alamat aplikasi.</li>
                                        <li>Masukkan <strong>NIS/NIP/Username</strong> dan <strong>kata sandi</strong> Anda yang terdaftar.</li>
                                        <li>Klik tombol "Masuk".</li>
                                        <li>Jika berhasil, Anda akan diarahkan ke halaman Dashboard sesuai peran Anda.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Tips:</strong> Pastikan Anda menggunakan kredensial yang benar. Jika Anda mengalami kesulitan, coba reset password atau hubungi administrator.</p>',
                                'roles' => ['admin', 'guru', 'siswa'],
                            ],
                            [
                                'question' => 'Saya lupa password, bagaimana cara meresetnya?',
                                'answer' => 'Jika Anda lupa kata sandi:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Di halaman login, klik tautan <strong>"Lupa Password?"</strong>.</li>
                                        <li>Masukkan <strong>alamat email</strong> yang terdaftar di akun Anda.</li>
                                        <li>Klik tombol "Kirim Tautan Reset Password".</li>
                                        <li>Periksa kotak masuk email Anda (termasuk folder spam/junk) untuk email dari sistem.</li>
                                        <li>Klik tautan reset password di dalam email dan ikuti instruksi untuk membuat kata sandi baru.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Troubleshooting:</strong> Jika Anda tidak menerima email, pastikan email yang dimasukkan benar dan periksa kembali folder spam. Jika masalah berlanjut, hubungi administrator.</p>',
                                'roles' => ['admin', 'guru', 'siswa'],
                            ],
                            [
                                'question' => 'Bagaimana cara memperbarui informasi profil saya?',
                                'answer' => 'Untuk memperbarui informasi profil Anda:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Setelah masuk, klik pada <strong>ikon profil</strong> Anda (biasanya di pojok kanan atas) atau navigasikan ke menu <strong>"Profil"</strong>.</li>
                                        <li>Di halaman profil, Anda akan melihat opsi untuk mengedit informasi seperti nama, email, dan foto profil.</li>
                                        <li>Lakukan perubahan yang diperlukan.</li>
                                        <li>Klik tombol <strong>"Simpan"</strong> atau <strong>"Perbarui Profil"</strong> untuk menyimpan perubahan Anda.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Catatan:</strong> Beberapa informasi profil (misalnya, peran pengguna) mungkin hanya dapat diubah oleh administrator.</p>',
                                'roles' => ['admin', 'guru', 'siswa'],
                            ],
                            [
                                'question' => 'Bagaimana cara melihat dan mengelola notifikasi?',
                                'answer' => 'Untuk melihat dan mengelola notifikasi:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Klik <strong>ikon lonceng</strong> (notifikasi) yang biasanya terletak di pojok kanan atas aplikasi.</li>
                                        <li>Daftar notifikasi terbaru akan ditampilkan.</li>
                                        <li>Klik pada notifikasi tertentu untuk melihat detail lengkapnya.</li>
                                        <li>Anda dapat menandai notifikasi sebagai sudah dibaca secara individual atau menandai semua notifikasi sebagai sudah dibaca.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Notifikasi memberikan informasi penting seperti pengingat absensi, perubahan status akun, atau pengumuman dari administrator.</p>',
                                'roles' => ['admin', 'guru', 'siswa'],
                            ],
                        ],
                    ],
                    [
                        'category_title' => 'Untuk Siswa',
                        'roles' => ['siswa'],
                        'items' => [
                            [
                                'question' => 'Bagaimana cara melakukan absensi menggunakan QR Code?',
                                'answer' => 'Untuk melakukan absensi:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Pastikan Anda memiliki kartu absensi dengan QR Code yang valid.</li>
                                        <li>Buka aplikasi dan navigasikan ke halaman absensi (biasanya melalui menu "Absensi" atau "Scan QR").</li>
                                        <li>Pindai QR Code yang ditampilkan di layar atau pada kartu absensi Anda menggunakan kamera perangkat.</li>
                                        <li>Setelah berhasil memindai, Anda akan menerima konfirmasi kehadiran.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Tips:</strong> Pastikan pencahayaan cukup dan QR Code tidak rusak untuk pemindaian yang berhasil.</p>',
                                'roles' => ['siswa'],
                            ],
                            [
                                'question' => 'Bagaimana cara melihat riwayat kehadiran saya?',
                                'answer' => 'Untuk melihat riwayat kehadiran Anda:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Setelah masuk, navigasikan ke menu <strong>"Laporan Absensi"</strong> atau <strong>"Riwayat Kehadiran"</strong>.</li>
                                        <li>Anda akan melihat daftar absensi Anda berdasarkan tanggal, mata pelajaran, dan status kehadiran.</li>
                                        <li>Gunakan filter yang tersedia untuk menyaring riwayat berdasarkan periode waktu tertentu.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Memungkinkan siswa untuk memantau catatan kehadiran mereka sendiri.</p>',
                                'roles' => ['siswa'],
                            ],
                            [
                                'question' => 'Bagaimana cara melihat jadwal pelajaran saya?',
                                'answer' => 'Untuk melihat jadwal pelajaran Anda:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Setelah masuk, navigasikan ke menu <strong>"Jadwal Pelajaran"</strong>.</li>
                                        <li>Anda akan melihat jadwal pelajaran Anda untuk setiap hari dalam seminggu, termasuk mata pelajaran, guru, dan waktu.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Membantu siswa mengetahui jadwal pelajaran mereka dan mempersiapkan diri.</p>',
                                'roles' => ['siswa'],
                            ],
                        ],
                    ],
                    [
                        'category_title' => 'Untuk Guru',
                        'roles' => ['guru'],
                        'items' => [
                            [
                                'question' => 'Bagaimana cara melihat jadwal mengajar saya?',
                                'answer' => 'Untuk melihat jadwal mengajar Anda:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Setelah masuk, navigasikan ke menu <strong>"Jadwal Mengajar"</strong>.</li>
                                        <li>Anda akan melihat daftar kelas dan mata pelajaran yang Anda ampu, beserta waktu dan hari mengajar.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Membantu guru mengelola waktu dan mempersiapkan diri untuk setiap sesi mengajar.</p>',
                                'roles' => ['guru'],
                            ],
                            [
                                'question' => 'Bagaimana cara mencatat absensi siswa di kelas saya?',
                                'answer' => 'Untuk mencatat absensi siswa:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Setelah masuk, navigasikan ke menu <strong>"Jadwal Mengajar"</strong>.</li>
                                        <li>Pilih jadwal pelajaran yang sedang berlangsung atau yang ingin Anda catat absensinya.</li>
                                        <li>Di halaman absensi, Anda dapat melihat daftar siswa di kelas tersebut dan mengubah status kehadiran mereka (Hadir, Sakit, Izin, Alpha).</li>
                                        <li>Klik tombol <strong>"Simpan Absensi"</strong> setelah selesai.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Tips:</strong> Pastikan untuk mencatat absensi tepat waktu sesuai jadwal.</p>',
                                'roles' => ['guru'],
                            ],
                            [
                                'question' => 'Bagaimana cara melihat laporan absensi siswa per kelas?',
                                'answer' => 'Untuk melihat laporan absensi siswa di kelas yang Anda ampu:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Setelah masuk, navigasikan ke menu <strong>"Rekap Absensi"</strong> atau <strong>"Laporan Kelas"</strong>.</li>
                                        <li>Pilih kelas dan mata pelajaran yang ingin Anda lihat laporannya.</li>
                                        <li>Gunakan filter tanggal untuk melihat data dalam periode tertentu.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Memberikan guru gambaran tentang kehadiran siswa di kelas mereka.</p>',
                                'roles' => ['guru'],
                            ],
                        ],
                    ],
                    [
                        'category_title' => 'Untuk Administrator',
                        'roles' => ['admin'],
                        'items' => [
                            [
                                'question' => 'Bagaimana cara mengkustomisasi nama aplikasi?',
                                'answer' => 'Untuk mengubah nama aplikasi yang muncul di sidebar dan judul halaman (favicon):
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Pengaturan"</strong>.</li>
                                        <li>Pada bagian <strong>"Kustomisasi Nama Aplikasi"</strong>, masukkan nama aplikasi yang baru di kolom <strong>"Nama Aplikasi"</strong>.</li>
                                        <li>Klik tombol <strong>"Simpan Pengaturan"</strong> di bagian bawah halaman.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Memungkinkan penyesuaian identitas aplikasi sesuai dengan nama sekolah atau institusi.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengkustomisasi tampilan halaman login?',
                                'answer' => 'Anda dapat mengubah tampilan halaman login (judul, subjudul, logo, dan gambar latar) agar sesuai dengan identitas sekolah Anda:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Pengaturan"</strong>.</li>
                                        <li>Pada bagian <strong>"Kustomisasi Halaman Login"</strong>:
                                            <ol class="list-decimal list-inside ml-4 mt-1">
                                                <li><strong>Judul Halaman:</strong> Masukkan teks utama yang akan muncul di bawah logo.</li>
                                                <li><strong>Subjudul Halaman:</strong> Masukkan teks kecil di bawah judul utama.</li>
                                                <li><strong>Logo Sekolah:</strong> Unggah gambar logo sekolah Anda (format PNG, JPG, SVG, maks 5MB).</li>
                                                <li><strong>Gambar Latar:</strong> Unggah gambar latar belakang untuk halaman login (resolusi tinggi, maks 10MB).</li>
                                            </ol>
                                        </li>
                                        <li>Gunakan pratinjau di tengah halaman untuk melihat perubahan secara langsung.</li>
                                        <li>Klik tombol <strong>"Simpan Pengaturan"</strong> di bagian bawah halaman.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Tips:</strong> Gunakan gambar berkualitas tinggi untuk tampilan yang profesional. Perhatikan ukuran file agar tidak memperlambat waktu muat halaman.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengelola FAQ (Pertanyaan yang Sering Diajukan)?',
                                'answer' => 'Halaman FAQ ini berfungsi sebagai sumber informasi bagi pengguna. Untuk mengelola konten FAQ:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Pengaturan"</strong>.</li>
                                        <li>Pada bagian <strong>"Pertanyaan yang Sering Diajukan (FAQ)"</strong>, klik tombol <strong>"Lihat FAQ"</strong>.</li>
                                        <li>Saat ini, pengelolaan konten FAQ (Pertanyaan yang Sering Diajukan) dilakukan secara manual dengan mengedit langsung file sumber aplikasi, yaitu `resources/views/pengaturan/faq.blade.php`. Setiap penambahan, perubahan, atau penghapusan pertanyaan dan jawaban memerlukan modifikasi kode secara langsung. Oleh karena itu, untuk pembaruan atau penambahan, disarankan untuk melibatkan pengembang atau seseorang yang memiliki pemahaman tentang struktur kode aplikasi.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Menyediakan panduan dan jawaban atas pertanyaan umum untuk mengurangi beban dukungan teknis.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara menambah pengguna baru (admin, guru, siswa, TU, Lainnya)?',
                                'answer' => 'Untuk menambah pengguna baru:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Manajemen Pengguna"</strong>.</li>
                                        <li>Klik tombol <strong>"Tambah Pengguna"</strong>.</li>
                                        <li>Isi formulir dengan detail pengguna baru (nama, email, password, peran, dll.).</li>
                                        <li>Pilih peran yang sesuai (Admin, Guru, Siswa, TU, atau Lainnya).</li>
                                        <li>Klik tombol <strong>"Simpan"</strong>.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Tips:</strong> Pastikan untuk memberikan password awal yang aman dan informasikan kepada pengguna baru.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengedit atau menghapus pengguna?',
                                'answer' => 'Untuk mengedit atau menghapus pengguna:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Manajemen Pengguna"</strong>.</li>
                                        <li>Cari pengguna yang ingin Anda edit atau hapus dari daftar.</li>
                                        <li>Untuk mengedit, klik <strong>ikon pensil</strong> (Edit) di samping nama pengguna, ubah informasi yang diperlukan, lalu simpan.</li>
                                        <li>Untuk menghapus, klik <strong>ikon tempat sampah</strong> (Hapus) di samping nama pengguna, lalu konfirmasi penghapusan.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Peringatan:</strong> Penghapusan pengguna bersifat permanen dan akan menghapus semua data terkait pengguna tersebut.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengimpor atau mengekspor data pengguna?',
                                'answer' => 'Untuk mengelola data pengguna dalam jumlah besar:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li><strong>Ekspor Data Pengguna:</strong>
                                            <ol class="list-decimal list-inside ml-4 mt-1">
                                                <li>Masuk sebagai admin.</li>
                                                <li>Navigasikan ke menu <strong>"Manajemen Pengguna"</strong>.
                                                <li>Klik tombol <strong>"Ekspor"</strong>.
                                                <li>Data pengguna akan diunduh dalam format Excel.
                                            </ol>
                                        </li>
                                        <li><strong>Impor Data Pengguna:</strong>
                                            <ol class="list-decimal list-inside ml-4 mt-1">
                                                <li>Masuk sebagai admin.
                                                <li>Navigasikan ke menu <strong>"Manajemen Pengguna"</strong>.
                                                <li>Klik tombol <strong>"Impor"</strong>.
                                                <li>Anda dapat mengunduh <strong>template impor</strong> untuk memastikan format data yang benar.
                                                <li>Unggah file Excel yang berisi data pengguna yang ingin diimpor.
                                                <li>Ikuti instruksi di layar untuk menyelesaikan proses impor.
                                            </ol>
                                        </li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Tips:</strong> Selalu gunakan template yang disediakan untuk impor data guna menghindari kesalahan format.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mencetak kartu absensi untuk siswa?',
                                'answer' => 'Untuk mencetak kartu absensi yang berisi QR Code untuk siswa:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Manajemen Pengguna"</strong>.</li>
                                        <li>Pilih siswa yang ingin dicetak kartunya (Anda bisa memilih satu per satu atau beberapa sekaligus).</li>
                                        <li>Klik tombol <strong>"Cetak Kartu Siswa"</strong>.</li>
                                        <li>Sistem akan menghasilkan file PDF yang berisi kartu absensi siap cetak.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Kartu ini digunakan siswa untuk melakukan absensi melalui pemindaian QR Code.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mencetak kartu absensi untuk pegawai (Guru, TU, Lainnya)?',
                                'answer' => 'Untuk mencetak kartu absensi yang berisi QR Code untuk Guru, TU, atau peran Lainnya:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Cetak Kartu Absensi Pegawai"</strong>.</li>
                                        <li>Pilih pegawai yang ingin dicetak kartunya (Anda bisa memilih satu per satu atau beberapa sekaligus).</li>
                                        <li>Klik tombol <strong>"Cetak Kartu Pegawai"</strong>.</li>
                                        <li>Sistem akan menghasilkan file PDF yang berisi kartu absensi siap cetak.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Kartu ini digunakan pegawai untuk melakukan absensi melalui pemindaian QR Code.</p>',
                                'roles' => ['admin', 'guru', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengaktifkan/menonaktifkan akun pengguna (toggle status)?',
                                'answer' => 'Untuk mengubah status aktif/nonaktif akun pengguna:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Manajemen Pengguna"</strong>.</li>
                                        <li>Cari pengguna yang ingin Anda ubah statusnya.</li>
                                        <li>Klik <strong>ikon status</strong> (biasanya berupa tombol toggle atau ikon centang/silang) di samping nama pengguna.</li>
                                        <li>Konfirmasi perubahan status jika diminta.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Mengaktifkan atau menonaktifkan akun pengguna tanpa menghapusnya, berguna untuk pengguna yang cuti atau sudah tidak aktif sementara.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara melakukan aksi massal pada pengguna (bulk toggle status, bulk destroy)?',
                                'answer' => 'Untuk melakukan aksi massal pada beberapa pengguna sekaligus:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Manajemen Pengguna"</strong>.</li>
                                        <li>Centang kotak di samping nama pengguna yang ingin Anda pilih. Anda juga bisa memilih semua pengguna dengan mencentang kotak di header tabel.</li>
                                        <li>Setelah memilih, akan muncul opsi aksi massal (misalnya, <strong>"Aktifkan Massal"</strong>, <strong>"Nonaktifkan Massal"</strong>, atau <strong>"Hapus Massal"</strong>).</li>
                                        <li>Pilih aksi yang diinginkan dan konfirmasi.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Peringatan:</strong> Aksi massal bersifat permanen untuk penghapusan. Pastikan Anda memilih pengguna yang benar.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara menambah, mengedit, atau menghapus kelas?',
                                'answer' => 'Untuk mengelola data kelas:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Kelas"</strong>.</li>
                                        <li><strong>Menambah Kelas:</strong> Klik tombol <strong>"Tambah Kelas"</strong>, isi detail kelas (nama, tingkat), lalu simpan.</li>
                                        <li><strong>Mengedit Kelas:</strong> Klik <strong>ikon pensil</strong> (Edit) di samping nama kelas, ubah informasi, lalu simpan.</li>
                                        <li><strong>Menghapus Kelas:</strong> Klik <strong>ikon tempat sampah</strong> (Hapus) di samping nama kelas, lalu konfirmasi penghapusan. Perlu diingat, kelas tidak dapat dihapus jika masih ada siswa yang terdaftar di dalamnya.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Tips:</strong> Pastikan penamaan kelas konsisten untuk memudahkan pengelolaan.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mencetak kartu absensi untuk siswa per kelas?',
                                'answer' => 'Untuk mencetak kartu absensi semua siswa dalam satu kelas:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Kelas"</strong>.</li>
                                        <li>Pilih kelas yang ingin Anda cetak kartu absensinya.</li>
                                        <li>Klik tombol <strong>"Cetak Kartu Absensi"</strong> (biasanya di halaman detail kelas atau di daftar kelas).</li>
                                        <li>Sistem akan menghasilkan file PDF yang berisi kartu absensi untuk semua siswa di kelas tersebut.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Memudahkan distribusi kartu absensi secara kolektif untuk satu kelas.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mencetak jadwal kelas?',
                                'answer' => 'Untuk mencetak jadwal pelajaran untuk suatu kelas:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Kelas"</strong>.</li>
                                        <li>Pilih kelas yang ingin Anda ekspor jadwalnya.</li>
                                        <li>Klik tombol <strong>"Ekspor Jadwal"</strong> (biasanya di halaman detail kelas).</li>
                                        <li>Sistem akan menghasilkan file Excel yang berisi jadwal pelajaran lengkap untuk kelas tersebut.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Menyediakan salinan fisik jadwal pelajaran untuk ditempel di kelas atau dibagikan kepada siswa.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara menghapus siswa dari kelas?',
                                'answer' => 'Untuk menghapus siswa dari suatu kelas (tanpa menghapus akun siswa):
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Kelas"</strong>.</li>
                                        <li>Pilih kelas tempat siswa berada.</li>
                                        <li>Di halaman detail kelas, cari daftar siswa.</li>
                                        <li>Klik <strong>ikon hapus</strong> (biasanya silang atau tempat sampah) di samping nama siswa yang ingin dihapus dari kelas.</li>
                                        <li>Konfirmasi penghapusan.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Catatan:</strong> Tindakan ini hanya menghapus siswa dari kelas tersebut, bukan dari sistem pengguna secara keseluruhan.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara menambah, mengedit, atau menghapus mata pelajaran?',
                                'answer' => 'Untuk mengelola data mata pelajaran:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Mata Pelajaran"</strong>.</li>
                                        <li><strong>Menambah Mata Pelajaran:</strong> Klik tombol <strong>"Tambah Mata Pelajaran"</strong>, isi detail (nama mata pelajaran), lalu simpan.</li>
                                        <li><strong>Mengedit Mata Pelajaran:</strong> Klik <strong>ikon pensil</strong> (Edit) di samping nama mata pelajaran, ubah informasi, lalu simpan.</li>
                                        <li><strong>Menghapus Mata Pelajaran:</strong> Klik <strong>ikon tempat sampah</strong> (Hapus) di samping nama mata pelajaran, lalu konfirmasi penghapusan.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Tips:</strong> Pastikan setiap mata pelajaran memiliki guru pengampu yang sesuai dalam pengaturan jadwal.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara membuat, mengedit, atau menghapus jadwal absensi?',
                                'answer' => 'Untuk mengelola jadwal absensi (kapan dan di mana absensi dilakukan):
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Kelola Jadwal"</strong>.</li>
                                        <li><strong>Membuat Jadwal:</strong> Klik tombol <strong>"Tambah Jadwal"</strong>, isi detail (kelas, mata pelajaran, guru, waktu, hari), lalu simpan.</li>
                                        <li><strong>Mengedit Jadwal:</strong> Klik <strong>ikon pensil</strong> (Edit) di samping jadwal, ubah informasi, lalu simpan.</li>
                                        <li><strong>Menghapus Jadwal:</strong> Klik <strong>ikon tempat sampah</strong> (Hapus) di samping jadwal, lalu konfirmasi penghapusan.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Jadwal ini menentukan kapan siswa dapat melakukan absensi untuk mata pelajaran tertentu.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengimpor atau mengekspor Kelola Jadwal?',
                                'answer' => 'Untuk mengelola Kelola Jadwal dalam jumlah besar:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li><strong>Ekspor Jadwal:</strong>
                                            <ol class="list-decimal list-inside ml-4 mt-1">
                                                <li>Masuk sebagai admin.</li>
                                                <li>Navigasikan ke menu <strong>"Kelola Jadwal"</strong>.
                                                <li>Klik tombol <strong>"Ekspor"</strong>.
                                                <li>Data jadwal akan diunduh dalam format Excel.
                                            </ol>
                                        </li>
                                        <li><strong>Impor Jadwal:</strong>
                                            <ol class="list-decimal list-inside ml-4 mt-1">
                                                <li>Masuk sebagai admin.
                                                <li>Navigasikan ke menu <strong>"Kelola Jadwal"</strong>.
                                                <li>Klik tombol <strong>"Impor"</strong>.
                                                <li>Anda dapat mengunduh <strong>template impor</strong> untuk memastikan format data yang benar.
                                                <li>Unggah file Excel yang berisi data jadwal yang ingin diimpor.
                                                <li>Ikuti instruksi di layar untuk menyelesaikan proses impor.
                                            </ol>
                                        </li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Tips:</strong> Selalu gunakan template yang disediakan untuk impor data guna menghindari kesalahan format.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengunduh template impor Kelola Jadwal?',
                                'answer' => 'Untuk mendapatkan format yang benar saat mengimpor Kelola Jadwal:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Kelola Jadwal"</strong>.
                                        <li>Klik tombol <strong>"Impor"</strong>.
                                        <li>Cari dan klik tautan <strong>"Unduh Template Impor"</strong>.
                                        <li>File template Excel akan diunduh ke perangkat Anda.
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Memastikan data yang Anda siapkan untuk impor sesuai dengan struktur yang diharapkan sistem.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara melihat dan mengunduh laporan rekap absensi?',
                                'answer' => 'Untuk melihat ringkasan dan mengunduh laporan absensi seluruh siswa:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Rekap Absensi"</strong>.</li>
                                        <li>Gunakan filter yang tersedia (tanggal, kelas, mata pelajaran, status kehadiran) untuk menyaring data.</li>
                                        <li>Klik tombol <strong>"Filter"</strong> untuk melihat data di layar.
                                        <li>Untuk mengunduh laporan, klik tombol <strong>"Ekspor Excel"</strong>.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Memberikan gambaran umum kehadiran siswa di seluruh sekolah untuk analisis dan pelaporan.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengelola data absensi (CRUD, bulk destroy)?',
                                'answer' => 'Admin memiliki kontrol penuh atas data absensi:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li><strong>Melihat/Mengedit:</strong> Di menu <strong>"Rekap Absensi"</strong>, Anda dapat melihat detail absensi dan mengedit status kehadiran siswa jika ada kesalahan.</li>
                                        <li><strong>Menghapus:</strong> Anda dapat menghapus catatan absensi individual.</li>
                                        <li><strong>Hapus Massal (Bulk Destroy):</strong> Pilih beberapa catatan absensi yang ingin dihapus (centang kotak di sampingnya), lalu klik tombol <strong>"Hapus Massal"</strong>.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Peringatan:</strong> Pengelolaan data absensi harus dilakukan dengan hati-hati untuk menjaga integritas data.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengkustomisasi tampilan kartu absensi?',
                                'answer' => 'Anda dapat menyesuaikan desain dan informasi yang ditampilkan pada kartu absensi siswa:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Pengaturan"</strong>.</li>
                                        <li>Pada bagian <strong>"Kustomisasi Kartu Absensi"</strong>, klik tombol <strong>"Atur Tampilan Kartu"</strong>.</li>
                                        <li>Di halaman ini, Anda dapat mengatur elemen-elemen seperti logo, teks, warna, dan informasi siswa yang akan muncul di kartu.</li>
                                        <li>Simpan perubahan Anda setelah selesai.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Memungkinkan sekolah untuk mencetak kartu absensi yang sesuai dengan branding dan kebutuhan informasi mereka.</p>',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengelola rekap absensi pegawai?',
                                'answer' => 'Untuk melihat ringkasan dan mengunduh laporan absensi seluruh pegawai:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Rekap Absensi Pegawai"</strong>.</li>
                                        <li>Gunakan filter yang tersedia (tanggal, peran, nama pegawai, status kehadiran) untuk menyaring data.</li>
                                        <li>Klik tombol <strong>"Filter"</strong> untuk melihat data di layar.</li>
                                        <li>Untuk mengunduh laporan, klik tombol <strong>"Ekspor Excel"</strong>.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Memberikan gambaran umum kehadiran pegawai di seluruh sekolah untuk analisis dan pelaporan.</p>',
                                'roles' => ['admin', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengelola jadwal absensi pegawai?',
                                'answer' => 'Untuk mengelola jadwal absensi pegawai (kapan dan di mana absensi dilakukan):
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li>Masuk sebagai admin.</li>
                                        <li>Navigasikan ke menu <strong>"Kelola Jadwal Absensi Pegawai"</strong>.</li>
                                        <li><strong>Membuat Jadwal:</strong> Klik tombol <strong>"Tambah Jadwal"</strong>, isi detail (pegawai, waktu, hari), lalu simpan.</li>
                                        <li><strong>Mengedit Jadwal:</strong> Klik <strong>ikon pensil</strong> (Edit) di samping jadwal, ubah informasi, lalu simpan.</li>
                                        <li><strong>Menghapus Jadwal:</strong> Klik <strong>ikon tempat sampah</strong> (Hapus) di samping jadwal, lalu konfirmasi penghapusan.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Fungsi:</strong> Jadwal ini menentukan kapan pegawai dapat melakukan absensi.</p>',
                                'roles' => ['admin', 'tu', 'other'],
                            ],
                        ],
                    ],
                    [
                        'category_title' => 'Pemecahan Masalah (Troubleshooting)',
                        'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                        'items' => [
                            [
                                'question' => 'Kode QR tidak terbaca saat absensi.',
                                'answer' => 'Jika mengalami kesulitan dalam memindai kode QR:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li><strong>Periksa Pencahayaan:</strong> Pastikan area sekitar kode QR memiliki pencahayaan yang cukup dan tidak ada bayangan atau pantulan yang mengganggu.</li>
                                        <li><strong>Bersihkan Lensa Kamera:</strong> Pastikan lensa kamera perangkat bersih dari kotoran atau sidik jari.</li>
                                        <li><strong>Jarak Pemindaian:</strong> Coba sesuaikan jarak antara perangkat dan kode QR. Terkadang terlalu dekat atau terlalu jauh dapat menyebabkan kegagalan pemindaian.</li>
                                        <li><strong>Kualitas QR Code:</strong> Pastikan kode QR yang ditampilkan atau dicetak tidak rusak, buram, atau terlipat.</li>
                                        <li><strong>Koneksi Internet:</strong> Pastikan perangkat memiliki koneksi internet yang stabil.</li>
                                        <li><strong>Laporkan ke Guru/Admin:</strong> Jika masalah berlanjut, laporkan kepada guru atau admin untuk absensi manual.</li>
                                    </ol>',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Data absensi tidak sinkron atau tidak muncul di laporan.',
                                'answer' => 'Jika Anda mendapati data absensi tidak sinkron atau tidak muncul di laporan:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li><strong>Periksa Koneksi Internet:</strong> Pastikan perangkat yang digunakan untuk absensi dan perangkat yang melihat laporan memiliki koneksi internet yang stabil.</li>
                                        <li><strong>Refresh Halaman:</strong> Coba muat ulang halaman laporan absensi.</li>
                                        <li><strong>Periksa Jadwal:</strong> Pastikan jadwal absensi sudah diatur dengan benar dan waktu absensi masih dalam periode yang aktif.</li>
                                        <li><strong>Hubungi Dukungan:</strong> Jika masalah berlanjut, kumpulkan informasi detail (waktu kejadian, nama siswa/guru, jadwal) dan hubungi tim dukungan teknis.</li>
                                    </ol>',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Tidak bisa login ke aplikasi.',
                                'answer' => 'Jika Anda tidak bisa masuk ke aplikasi:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li><strong>Periksa Kredensial:</strong> Pastikan Anda memasukkan ID Pengguna (NIS/NIP/Username) dan kata sandi dengan benar (perhatikan huruf besar/kecil).</li>
                                        <li><strong>Reset Password:</strong> Gunakan fitur "Lupa Password?" jika Anda yakin lupa kata sandi Anda (lihat FAQ terkait).</li>
                                        <li><strong>Status Akun:</strong> Pastikan akun Anda tidak dinonaktifkan oleh administrator.</li>
                                        <li><strong>Koneksi Internet:</strong> Pastikan perangkat Anda terhubung ke internet.</li>
                                        <li><strong>Hubungi Admin:</strong> Jika semua langkah di atas tidak berhasil, hubungi administrator sekolah Anda untuk memeriksa status akun atau bantuan lebih lanjut.</li>
                                    </ol>',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                        ],
                    ],
                    [
                        'category_title' => 'Definisi Istilah Teknis',
                        'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                        'items' => [
                            [
                                'question' => 'QR Code',
                                'answer' => '<strong>QR Code (Quick Response Code)</strong> adalah jenis kode batang dua dimensi yang dapat dibaca oleh perangkat seluler. Dalam aplikasi ini, QR Code digunakan sebagai metode cepat dan efisien untuk mencatat kehadiran.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Dashboard',
                                'answer' => '<strong>Dashboard</strong> adalah halaman utama yang menampilkan ringkasan informasi penting dan metrik kinerja aplikasi. Setiap peran pengguna (Admin, Guru, Siswa, TU, Lainnya) memiliki tampilan dashboard yang disesuaikan dengan kebutuhan mereka.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Rekap Absensi',
                                'answer' => '<strong>Rekap Absensi</strong> adalah fitur yang memungkinkan pengguna (terutama Admin, Guru, TU, dan Lainnya) untuk melihat, mengelola, dan mengunduh ringkasan data kehadiran siswa atau pegawai dalam periode waktu tertentu.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Toggle Status',
                                'answer' => '<strong>Toggle Status</strong> adalah fungsi yang memungkinkan administrator untuk dengan cepat mengubah status aktif atau nonaktif suatu akun pengguna atau fitur tertentu dengan satu klik.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Bulk Action (Aksi Massal)',
                                'answer' => '<strong>Bulk Action (Aksi Massal)</strong> adalah kemampuan untuk melakukan operasi (seperti menghapus, mengaktifkan, atau menonaktifkan) pada beberapa item (misalnya, pengguna, jadwal, catatan absensi) secara bersamaan, menghemat waktu dan usaha.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                        ],
                    ],
                    [
                        'category_title' => 'Informasi Penting Lainnya',
                        'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                        'items' => [
                            [
                                'question' => 'Apa saja persyaratan sistem minimum untuk menggunakan aplikasi ini?',
                                'answer' => 'Untuk memastikan pengalaman terbaik dalam menggunakan aplikasi, berikut adalah persyaratan sistem minimum yang direkomendasikan:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li><strong>Perangkat Keras:</strong>
                                            <ol class="list-decimal list-inside ml-4 mt-1">
                                                <li>Untuk siswa/guru/pegawai (scan QR): Smartphone atau tablet dengan kamera belakang yang berfungsi baik.</li>
                                                <li>Untuk admin/guru/TU/lainnya (manajemen): Komputer atau laptop dengan spesifikasi standar.</li>
                                            </ol>
                                        </li>
                                        <li><strong>Peramban Web yang Didukung:</strong>
                                            <ol class="list-decimal list-inside ml-4 mt-1">
                                                <li>Google Chrome (versi terbaru)</li>
                                                <li>Mozilla Firefox (versi terbaru)</li>
                                                <li>Microsoft Edge (versi terbaru)</li>
                                                <li>Safari (versi terbaru)</li>
                                            </ol>
                                        </li>
                                        <li><strong>Koneksi Internet:</strong> Koneksi internet stabil (minimal 4G atau Wi-Fi) untuk akses penuh fitur dan sinkronisasi data real-time.</li>
                                    </ol>',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Bagaimana data saya dikumpulkan, disimpan, digunakan, dan dilindungi?',
                                'answer' => 'Kami berkomitmen penuh untuk melindungi privasi dan keamanan data Anda. Berikut adalah ringkasan kebijakan kami:
                                    <ol class="list-decimal list-inside ml-4 mt-2">
                                        <li><strong>Pengumpulan Data:</strong> Kami mengumpulkan data kehadiran (siswa dan pegawai), informasi profil dasar pengguna (nama, email, peran, kelas, mata pelajaran, NIP, jabatan), dan log aktivitas sistem untuk tujuan operasional dan peningkatan layanan.</li>
                                        <li><strong>Penyimpanan Data:</strong> Semua data disimpan di server yang aman dengan implementasi enkripsi data saat transit dan saat istirahat, serta kontrol akses yang ketat.</li>
                                        <li><strong>Penggunaan Data:</strong> Data digunakan semata-mata untuk tujuan absensi, manajemen pengguna, pelaporan, analisis kinerja, dan peningkatan kualitas layanan aplikasi. Data tidak akan digunakan untuk tujuan pemasaran atau dibagikan kepada pihak ketiga tanpa persetujuan eksplisit Anda, kecuali diwajibkan oleh hukum.</li>
                                        <li><strong>Perlindungan Data:</strong> Kami menerapkan langkah-langkah keamanan fisik, teknis, dan administratif yang komprehensif untuk melindungi data dari akses tidak sah, pengungkapan, perubahan, atau penghancuran. Ini termasuk firewall, deteksi intrusi, dan audit keamanan rutin.</li>
                                    </ol>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Untuk informasi lebih lanjut, silakan lihat Kebijakan Privasi lengkap kami.</p>',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                        ],
                    ],
                ];
                ?>

                <div x-data="{ activeCategory: null, filter: '' }" x-init="
                    const categories = document.querySelectorAll('[data-category-title]');
                    categories.forEach((category, index) => {
                        category.setAttribute('id', 'category-' + index);
                    });

                    $watch('filter', value => {
                        const lowerCaseFilter = value.toLowerCase();
                        document.querySelectorAll('.faq-category').forEach(categoryElement => {
                            let categoryHasVisibleItems = false;
                            const categoryTitleElement = categoryElement.querySelector('[data-category-title] h4');
                            const categoryTitleText = categoryTitleElement ? categoryTitleElement.textContent.toLowerCase() : '';

                            const categoryTitleMatches = categoryTitleText.includes(lowerCaseFilter);

                            categoryElement.querySelectorAll('.faq-item').forEach(itemElement => {
                                const itemText = itemElement.textContent.toLowerCase();
                                const itemMatches = itemText.includes(lowerCaseFilter);

                                if (itemMatches) {
                                    itemElement.style.display = '';
                                    categoryHasVisibleItems = true;
                                } else {
                                    itemElement.style.display = 'none';
                                }
                            });

                            if (value === '') {
                                categoryElement.style.display = ''; // Show category if filter is empty
                                const categoryAlpine = categoryElement.__alpine_data__;
                                if (categoryAlpine) {
                                    categoryAlpine.open = false; // Collapse all categories
                                }
                                categoryElement.querySelectorAll('.faq-item').forEach(itemElement => {
                                    itemElement.style.display = ''; // Show all items
                                });
                            } else if (categoryTitleMatches || categoryHasVisibleItems) {
                                categoryElement.style.display = ''; // Show category
                                const categoryAlpine = categoryElement.__alpine_data__;
                                if (categoryAlpine) {
                                    categoryAlpine.open = true; // Expand category
                                }
                            } else {
                                categoryElement.style.display = 'none'; // Hide category
                                const categoryAlpine = categoryElement.__alpine_data__;
                                if (categoryAlpine) {
                                    categoryAlpine.open = false; // Collapse category
                                }
                            }
                        });
                    });
                ">
                {{-- Search Bar --}}
                <div class="mb-8">
                    <div class="relative">
                        <input type="text" id="faq-search" placeholder="Cari pertanyaan..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400"
                            x-model="filter">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <?php $categoryIndex = 0; ?>
                @foreach ($faqs as $category)
                    @php
                        $showCategory = false;
                        foreach ($category['roles'] as $role) {
                            if ($role === $userRole) {
                                $showCategory = true;
                                break;
                            }
                        }
                    @endphp

                    @if ($showCategory)
                        <?php $categoryIndex++; ?>
                        <div class="mb-4 faq-category" x-data="{ open: false }">
                            <div class="border-b border-gray-200 dark:border-gray-700">
                                <button @click="open = !open" class="flex justify-between items-center w-full py-4 text-left font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition duration-150 ease-in-out" data-category-title>
                                    <h4 class="text-lg sm:text-xl">{{ $categoryIndex }}. {{ $category['category_title'] }}</h4>
                                    <svg :class="{ 'rotate-180': open, 'rotate-0': !open }" class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                            <div x-show="open" x-collapse class="pb-4 space-y-4 transition-all duration-300 ease-in-out">
                                <?php $itemIndex = 0; ?>
                                @foreach ($category['items'] as $item)
                                    @php
                                        $showItem = false;
                                        foreach ($item['roles'] as $role) {
                                            if ($role === $userRole) {
                                                $showItem = true;
                                                break;
                                            }
                                        }
                                    @endphp

                                    @if ($showItem)
                                        <?php $itemIndex++; ?>
                                        <div class="faq-item border-b border-gray-100 dark:border-gray-700 last:border-b-0" x-data="{ itemOpen: false }">
                                            <button @click="itemOpen = !itemOpen" class="flex justify-between items-center w-full py-3 text-left text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition duration-150 ease-in-out">
                                                <h5 class="text-base sm:text-lg font-medium">{{ $categoryIndex }}.{{ $itemIndex }}. {{ $item['question'] }}</h5>
                                                <svg :class="{ 'rotate-180': itemOpen, 'rotate-0': !itemOpen }" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                            <div x-show="itemOpen" x-collapse class="pl-6 pt-2 pb-3 text-gray-700 dark:text-gray-300 transition-all duration-300 ease-in-out prose dark:prose-invert max-w-none">
                                                {!! $item['answer'] !!}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
