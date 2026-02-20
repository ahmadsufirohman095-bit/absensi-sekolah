<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <?php echo e(__('Pusat Bantuan & FAQ')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-6 sm:py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                
                <div class="mb-10 text-center">
                    <h3 class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">Bagaimana kami bisa membantu Anda?</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400 text-lg">Panduan resmi penggunaan Sistem Absensi Sekolah sesuai dokumentasi terbaru.</p>
                </div>

                <?php
                $userRole = Auth::user()->role ?? 'guest';
                $faqs = [
                    // --- UMUM (SEMUA ROLE) ---
                    [
                        'category_title' => '🌐 Panduan Umum & Alur Kerja',
                        'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                        'items' => [
                            [
                                'question' => 'Bagaimana alur utama absensi di sekolah ini?',
                                'answer' => 'Sistem ini membagi tanggung jawab sebagai berikut:
                                    <ul class="list-disc list-inside ml-4 mt-2 space-y-1">
                                        <li><strong>Admin</strong> mengabsen seluruh <strong>Pegawai</strong> (Guru, TU, Staf).</li>
                                        <li><strong>Guru</strong> mengabsen seluruh <strong>Siswa</strong> di dalam kelas.</li>
                                        <li><strong>Siswa & Pegawai</strong> adalah objek absensi yang harus menunjukkan QR Code.</li>
                                    </ul>',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Format file apa yang terbaik untuk QR Code?',
                                'answer' => 'Sangat disarankan menggunakan format <strong>SVG</strong> yang disediakan sistem. Format SVG tidak akan pecah saat dicetak (print) dalam ukuran apapun, sehingga kamera scanner lebih mudah membacanya.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                        ],
                    ],

                    // --- KHUSUS ADMINISTRATOR ---
                    [
                        'category_title' => '🛠️ Panel Administrator',
                        'roles' => ['admin'],
                        'items' => [
                            [
                                'question' => 'Bagaimana cara mendistribusikan QR Code secara massal?',
                                'answer' => '1. Buka menu <strong>Pengaturan & Utilitas</strong> -> <strong>Generate QR Codes</strong>.<br>2. Filter kategori user.<br>3. Klik <strong>Unduh Semua Terfilter (ZIP SVG)</strong>.<br>4. Ekstrak file ZIP tersebut dan bagikan kode SVG ke masing-masing pengguna.',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Di menu mana saya mengabsen Guru dan Staf?',
                                'answer' => 'Anda dapat menggunakan menu <strong>Scan Absensi</strong> jika menggunakan scanner terpusat, atau melalui menu <strong>Rekap Absensi Pegawai</strong> untuk mencatat kehadiran secara manual.',
                                'roles' => ['admin'],
                            ],
                            [
                                'question' => 'Bagaimana cara mengelola anggota kelas?',
                                'answer' => 'Masuk ke menu <strong>Manajemen Kelas</strong>, pilih kelas yang diinginkan, lalu Anda dapat menambah atau mengeluarkan siswa dari daftar anggota kelas tersebut.',
                                'roles' => ['admin'],
                            ],
                        ],
                    ],

                    // --- KHUSUS GURU ---
                    [
                        'category_title' => '👨‍🏫 Panduan Khusus Guru',
                        'roles' => ['guru'],
                        'items' => [
                            [
                                'question' => 'Bagaimana status jadwal yang bisa mulai diabsen?',
                                'answer' => 'Hanya jadwal yang berstatus <strong>"Berlangsung"</strong> yang dapat melakukan absensi QR. Pastikan jam mengajar Anda sudah sesuai dengan waktu saat ini.',
                                'roles' => ['guru'],
                            ],
                            [
                                'question' => 'Langkah-langkah mengabsen siswa dengan cepat?',
                                'answer' => 'Buka <strong>Jadwal Mengajar</strong> -> Klik <strong>Mulai Absensi QR</strong> -> Arahkan kamera ke kode siswa. Pastikan muncul konfirmasi "Berhasil" di layar perangkat Anda.',
                                'roles' => ['guru'],
                            ],
                            [
                                'question' => 'Bagaimana jika siswa lupa membawa kode QR?',
                                'answer' => 'Klik nama siswa yang bersangkutan pada daftar di halaman jadwal mengajar, lalu pilih status kehadirannya secara manual (Hadir/Sakit/Izin/Alpha).',
                                'roles' => ['guru'],
                            ],
                        ],
                    ],

                    // --- KHUSUS SISWA ---
                    [
                        'category_title' => '🎓 Panduan Khusus Siswa',
                        'roles' => ['siswa'],
                        'items' => [
                            [
                                'question' => 'Bagaimana cara saya absen di kelas?',
                                'answer' => 'Siapkan QR Code Anda (bisa dari HP atau kartu cetak). Tunjukkan kode tersebut kepada <strong>Guru</strong> saat jam pelajaran dimulai.',
                                'roles' => ['siswa'],
                            ],
                            [
                                'question' => 'Dimana saya melihat laporan kehadiran per mata pelajaran?',
                                'answer' => 'Login ke akun Anda, lalu buka menu <strong>Laporan Absensi</strong> di sidebar. Anda dapat melihat detail statistik kehadiran untuk setiap mapel yang Anda ikuti.',
                                'roles' => ['siswa'],
                            ],
                        ],
                    ],

                    // --- KHUSUS TU / PEGAWAI ---
                    [
                        'category_title' => '📁 Panduan Pegawai (TU & Staf)',
                        'roles' => ['tu', 'other'],
                        'items' => [
                            [
                                'question' => 'Siapa yang bertanggung jawab mengabsen saya?',
                                'answer' => 'Sebagai pegawai (TU/Staf), kehadiran Anda dicatat oleh <strong>Administrator</strong> sekolah. Pastikan Anda melakukan scan pada petugas/Admin saat jam masuk dan pulang.',
                                'roles' => ['tu', 'other'],
                            ],
                            [
                                'question' => 'Dimana saya bisa melihat ringkasan kehadiran saya?',
                                'answer' => 'Anda dapat melihat ringkasan kehadiran harian Anda langsung pada halaman <strong>Dashboard</strong> utama setelah login.',
                                'roles' => ['tu', 'other'],
                            ],
                        ],
                    ],

                    // --- MASALAH TEKNIS ---
                    [
                        'category_title' => '💻 Tips & Pemecahan Masalah',
                        'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                        'items' => [
                            [
                                'question' => 'Indikator loading tidak hilang setelah unduh file ZIP/Excel.',
                                'answer' => 'Ini adalah perilaku browser yang umum. Jika file sudah terunduh, silakan tekan <strong>F5 atau Refresh</strong> halaman untuk menghilangkan indikator loading tersebut.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Bagaimana cara memberi izin kamera di browser?',
                                'answer' => 'Klik ikon <strong>Gembok (Lock)</strong> di sebelah kiri alamat web (URL bar), cari menu "Camera", lalu pilih <strong>"Allow"</strong> atau "Izinkan". Setelah itu refresh halaman.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                            [
                                'question' => 'Hasil scan QR Code sering gagal/lama.',
                                'answer' => 'Pastikan lensa kamera bersih, cahaya di ruangan cukup, dan gunakan file QR format <strong>SVG</strong> untuk hasil cetak yang tajam.',
                                'roles' => ['admin', 'guru', 'siswa', 'tu', 'other'],
                            ],
                        ],
                    ],
                ];
                ?>

                <div x-data="{ filter: '' }">
                    <div class="mb-8 max-w-2xl mx-auto">
                        <div class="relative">
                            <input type="text" placeholder="Cari solusi cepat (misal: 'kamera', 'svg', 'absen manual')..."
                                class="w-full pl-12 pr-4 py-4 border-2 border-indigo-100 dark:border-gray-700 rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-300 transition duration-200 text-lg"
                                x-model="filter">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-10">
                        <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(in_array($userRole, $category['roles'])): ?>
                                <div class="faq-category" x-show="filter === '' || $el.innerText.toLowerCase().includes(filter.toLowerCase())" x-transition>
                                    <h4 class="font-bold text-2xl text-indigo-700 dark:text-indigo-400 mb-6 border-b-2 border-indigo-50 dark:border-gray-700 pb-2 flex items-center">
                                        <span class="mr-3"><?php echo e($category['category_title']); ?></span>
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <?php $__currentLoopData = $category['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(in_array($userRole, $item['roles'])): ?>
                                                <div class="faq-item p-6 bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 rounded-3xl hover:bg-white dark:hover:bg-gray-700/50 hover:shadow-xl hover:border-indigo-300 transition-all duration-300">
                                                    <h5 class="font-bold text-gray-900 dark:text-gray-100 text-lg flex items-start">
                                                        <span class="text-indigo-500 mr-3 font-black text-xl">Q:</span>
                                                        <?php echo e($item['question']); ?>

                                                    </h5>
                                                    <div class="mt-4 text-gray-700 dark:text-gray-300 leading-relaxed pl-8">
                                                        <div class="prose dark:prose-invert max-w-none text-base">
                                                            <?php echo $item['answer']; ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <!-- No Results Placeholder -->
                    <div x-show="filter !== '' && !document.querySelector('.space-y-10').innerText.toLowerCase().includes(filter.toLowerCase())" 
                         class="text-center py-20" x-cloak>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-600 dark:text-gray-400">Solusi Tidak Ditemukan</h4>
                        <p class="text-gray-500 mt-2">Coba gunakan kata kunci lain seperti 'kamera' atau 'absen'.</p>
                        <button @click="filter = ''" class="mt-6 text-indigo-600 font-bold hover:underline">Tampilkan Semua Solusi</button>
                    </div>
                </div>

                <div class="mt-20 p-8 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl text-white shadow-2xl overflow-hidden relative">
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between">
                        <div class="mb-6 md:mb-0 text-center md:text-left">
                            <h4 class="text-2xl font-extrabold mb-2">Butuh Bantuan Lebih Lanjut?</h4>
                            <p class="text-indigo-100 opacity-90 text-lg">Jika FAQ ini belum menjawab masalah Anda, silakan hubungi tim IT Sekolah.</p>
                        </div>
                        <div class="flex space-x-4">
                            <a href="mailto:admin@sekolah.sch.id" class="px-8 py-3 bg-white text-indigo-700 font-bold rounded-2xl hover:bg-indigo-50 transition transform hover:-translate-y-1 shadow-lg">
                                Email IT Sekolah
                            </a>
                            <a href="#" class="px-8 py-3 bg-indigo-500/30 backdrop-blur-md border border-white/20 text-white font-bold rounded-2xl hover:bg-indigo-500/50 transition transform hover:-translate-y-1 shadow-lg">
                                Chat Admin
                            </a>
                        </div>
                    </div>
                    <!-- Background Decoration -->
                    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-indigo-900/20 rounded-full blur-3xl"></div>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /home/ruanmei/Dokumen/xampp/htdocs/absensi-sekolah/resources/views/pengaturan/faq.blade.php ENDPATH**/ ?>