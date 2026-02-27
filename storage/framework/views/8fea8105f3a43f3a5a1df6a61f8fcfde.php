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
            <?php echo e(__('Pindai QR Code Absensi')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6">
                    
                    <div id="schedule-selection-area" class="mb-6">
                        <label for="schedule-search-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari Jadwal Absensi:</label>
                        <input type="text" id="schedule-search-input" placeholder="Ketik untuk mencari jadwal..." class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 transition-colors shadow-sm">
                        <div id="schedule-results-container" class="mt-2 max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 shadow-lg custom-scrollbar">
                            </div>
                    </div>

                    <div id="selected-schedule-info" class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/40 border border-indigo-100 dark:border-indigo-800 rounded-lg text-indigo-800 dark:text-indigo-200 font-medium hidden transition-all">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Jadwal Aktif: <span id="active-schedule-text" class="font-bold"></span></span>
                            </span>
                            <button id="change-schedule-button" class="w-full sm:w-auto px-4 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all shadow-sm">
                                Ganti Jadwal
                            </button>
                        </div>
                    </div>

                    <div id="scanner-section" class="hidden">
                        <div id="camera-container" class="w-full h-[400px] sm:h-[480px] bg-black rounded-2xl overflow-hidden relative shadow-inner transition-all duration-300 ease-in-out border border-gray-200 dark:border-gray-700 mx-auto max-w-3xl">
                            
                            <div id="reader" class="absolute inset-0 w-full h-full z-0"></div>
                            
                            <div id="scanner-overlay" class="absolute inset-0 z-20 pointer-events-none hidden flex-col items-center justify-center overflow-hidden">
                                
                                <div class="relative w-64 h-64 sm:w-72 sm:h-72">
                                    <div class="absolute inset-0 rounded-2xl shadow-[0_0_0_9999px_rgba(0,0,0,0.65)] pointer-events-none"></div>

                                    <div class="absolute top-0 left-0 w-12 h-12 border-t-4 border-l-4 border-indigo-500 rounded-tl-2xl"></div>
                                    <div class="absolute top-0 right-0 w-12 h-12 border-t-4 border-r-4 border-indigo-500 rounded-tr-2xl"></div>
                                    <div class="absolute bottom-0 left-0 w-12 h-12 border-b-4 border-l-4 border-indigo-500 rounded-bl-2xl"></div>
                                    <div class="absolute bottom-0 right-0 w-12 h-12 border-b-4 border-r-4 border-indigo-500 rounded-br-2xl"></div>
                                    
                                    <div class="absolute left-0 right-0 h-1 bg-gradient-to-r from-transparent via-indigo-400 to-transparent shadow-[0_0_15px_rgba(99,102,241,1)] animate-scan-line"></div>
                                </div>

                                <div class="absolute bottom-10 left-0 right-0 text-center z-30">
                                    <span class="text-white text-xs font-bold bg-gray-900/80 px-4 py-2 rounded-full backdrop-blur-md tracking-wider uppercase shadow-lg border border-white/10">Arahkan QR ke Dalam Kotak</span>
                                </div>
                            </div>

                            <div id="loading-overlay" class="absolute inset-0 z-30 flex-col items-center justify-center bg-gray-900/80 backdrop-blur-sm transition-opacity duration-300 hidden">
                                <div class="loader ease-linear rounded-full border-4 border-t-4 border-indigo-500 border-gray-200 h-12 w-12 mb-4"></div>
                                <p id="loading-text" class="text-white font-medium text-base tracking-wide">Memulai kamera...</p>
                            </div>

                            <div id="camera-off-overlay" class="absolute inset-0 z-30 flex-col items-center justify-center bg-gray-900/90 hidden">
                                <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3l18 18M4 7h16M4 17h16M10 12l4 4m0-4l-4 4"></path>
                                </svg>
                                <p class="text-white font-medium text-lg text-center">Kamera Tidak Aktif</p>
                                <p class="text-gray-400 text-sm text-center mt-1 max-w-xs">Silakan tekan tombol Aktifkan di bawah untuk memulai pemindaian absen.</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap justify-center gap-4">
                            <button id="disable-camera-button" title="Nonaktifkan Kamera" class="px-6 py-2.5 bg-rose-600 text-white font-medium rounded-lg shadow-sm hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all">
                                Nonaktifkan
                            </button>
                            <button id="enable-camera-button" title="Aktifkan Kamera" class="px-6 py-2.5 bg-emerald-600 text-white font-medium rounded-lg shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all" disabled>
                                Aktifkan
                            </button>
                        </div>
                    </div>

                    <div id="feedback-section" class="hidden">
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 max-w-3xl mx-auto">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Status Pemindaian
                            </h3>
                            <div id="status-message" class="mt-3 text-center font-medium p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 border border-transparent" role="alert">
                                Menunggu kamera siap...
                            </div>
                            
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Riwayat Terakhir</h4>
                                <div id="history-log" class="space-y-2 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                                    <p class="text-center text-sm text-gray-400 dark:text-gray-500 py-4">Belum ada aktivitas.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <audio id="success-sound" src="<?php echo e(asset('sounds/success.mp3')); ?>" preload="auto"></audio>

    <?php $__env->startPush('styles'); ?>
    <style>
        /* Paksa element reader dan video agar fullscreen di dalam kontainer */
        #reader {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
            border: none !important;
            background: #000;
        }
        
        #reader video {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important; /* Mencegah video menjadi gepeng/letterbox */
            z-index: 10;
        }

        /* HILANGKAN SEMUA UI BAWAAN HTML5-QRCODE */
        #qr-shaded-region,
        #reader__dashboard_section_csr,
        #reader__dashboard_section_swaplink,
        #reader__scan_region img {
            display: none !important;
            opacity: 0 !important;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }

        /* Loading Overlay Centering */
        #loading-overlay.flex { display: flex !important; }

        /* Spinner CSS */
        .loader {
            border-top-color: transparent;
            -webkit-animation: spinner 1s linear infinite;
            animation: spinner 1s linear infinite;
        }

        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Scanning Animation */
        @keyframes scan-line {
            0% { top: 0%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan-line {
            position: absolute;
            animation: scan-line 2.5s ease-in-out infinite;
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('js/html5-qrcode.min.js')); ?>" type="text/javascript"></script>
    <script>
        // Global Variables
        window.disableCameraButton = window.disableCameraButton || null;
        window.enableCameraButton = window.enableCameraButton || null;
        window.cameraOffOverlay = window.cameraOffOverlay || null;
        window.loadingOverlay = window.loadingOverlay || null;
        window.loadingText = window.loadingText || null;
        window.scannerOverlay = window.scannerOverlay || null;
        window.cameraContainer = window.cameraContainer || null;

        window.scheduleSearchInput = window.scheduleSearchInput || null;
        window.scheduleResultsContainer = window.scheduleResultsContainer || null;
        window.allAvailableSchedules = <?php echo json_encode($availableSchedules ?? [], 15, 512) ?>; 
        window.selectedScheduleInfo = window.selectedScheduleInfo || null;
        window.activeScheduleText = window.activeScheduleText || null;
        window.scannerSection = window.scannerSection || null;
        window.feedbackSection = window.feedbackSection || null;
        window.activeJadwalAbsensiId = window.activeJadwalAbsensiId || null;
        window.selectedScheduleId = window.selectedScheduleId || null;
        window.selectedScheduleType = window.selectedScheduleType || null;
        window.isUserAdmin = <?php echo e(auth()->check() && auth()->user()->isAdmin() ? 'true' : 'false'); ?>;
        window.initialJadwalId = <?php echo json_encode(request('jadwal_id'), 15, 512) ?>;
        window.initialJadwal = null;

        window.scannerStarted = window.scannerStarted || false;
        window.scanner = window.scanner || null;
        window.isStartingOrStoppingScanner = window.isStartingOrStoppingScanner || false;

        (function() {
            const updateStatus = function(message, type = 'info') {
                const statusDiv = document.getElementById('status-message');
                if (!statusDiv) return;
                statusDiv.textContent = message;
                statusDiv.className = 'mt-3 text-center font-medium p-4 rounded-lg transition-colors duration-300 border ';
                
                if (type === 'success') {
                    statusDiv.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'dark:bg-emerald-900/30', 'dark:text-emerald-400', 'dark:border-emerald-800/50');
                } else if (type === 'error') {
                    statusDiv.classList.add('bg-rose-50', 'text-rose-700', 'border-rose-200', 'dark:bg-rose-900/30', 'dark:text-rose-400', 'dark:border-rose-800/50');
                } else {
                    statusDiv.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-700');
                }
            };

            const addHistory = function(message, type) {
                const historyLog = document.getElementById('history-log');
                if (!historyLog) return;
                
                // Clear placeholder if exists
                if (historyLog.querySelector('p.text-center')) {
                    historyLog.innerHTML = ''; 
                }
                
                const entry = document.createElement('div');
                entry.className = `p-3 rounded-lg text-sm mb-2 shadow-sm border flex items-center gap-2 `;
                
                const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                
                if (type === 'success') {
                    entry.classList.add('bg-emerald-50', 'border-emerald-100', 'text-emerald-800', 'dark:bg-emerald-900/20', 'dark:border-emerald-800/30', 'dark:text-emerald-200');
                    entry.innerHTML = `<span class="font-mono text-emerald-600 dark:text-emerald-400">[${time}]</span> <span>${message}</span>`;
                } else {
                    entry.classList.add('bg-rose-50', 'border-rose-100', 'text-rose-800', 'dark:bg-rose-900/20', 'dark:border-rose-800/30', 'dark:text-rose-200');
                    entry.innerHTML = `<span class="font-mono text-rose-600 dark:text-rose-400">[${time}]</span> <span>${message}</span>`;
                }
                
                historyLog.prepend(entry);
                if (historyLog.children.length > 15) {
                    historyLog.removeChild(historyLog.lastChild);
                }
            };

            const stopScanner = async function() {
                if (window.isStartingOrStoppingScanner) return;
                
                if (window.scanner && window.scanner.isScanning) {
                    window.isStartingOrStoppingScanner = true;
                    try {
                        await window.scanner.stop();
                        window.scannerStarted = false;
                        updateStatus('Kamera dinonaktifkan.', 'info');
                        
                        if (window.cameraOffOverlay) {
                            window.cameraOffOverlay.classList.remove('hidden');
                            window.cameraOffOverlay.classList.add('flex');
                        }
                        if (window.scannerOverlay) {
                            window.scannerOverlay.classList.add('hidden');
                            window.scannerOverlay.classList.remove('flex');
                        }
                        
                        window.disableCameraButton.disabled = true;
                        window.enableCameraButton.disabled = false;
                        
                        resetScheduleSelection();

                    } catch (err) {
                        updateStatus('Gagal menonaktifkan kamera.', 'error');
                    } finally {
                        window.isStartingOrStoppingScanner = false;
                    }
                }
            };

            const startScanner = async function() {
                if (window.scannerStarted || window.isStartingOrStoppingScanner) return;

                const scannerContainer = document.getElementById('reader');
                if (!scannerContainer) return;

                if (typeof Html5Qrcode === 'undefined') {
                    setTimeout(startScanner, 100); 
                    return;
                }

                if (!window.scanner) {
                    window.scanner = new Html5Qrcode("reader");
                }

                window.loadingOverlay.classList.remove('hidden');
                window.loadingOverlay.classList.add('flex');
                window.loadingOverlay.style.opacity = '1';
                window.loadingText.textContent = 'Memulai kamera...';
                updateStatus('Memulai kamera...', 'info');

                // PERBAIKAN: qrbox DIHAPUS agar scan full frame dan UI bawaan tidak muncul
                const config = {
                    fps: 15, 
                    disableFlip: false, 
                };

                try {
                    await window.scanner.start(
                        { facingMode: "environment" },
                        config,
                        onScanSuccess,
                        onScanFailure
                    );
                    
                    window.scannerStarted = true;
                    window.loadingOverlay.classList.add('hidden');
                    window.loadingOverlay.classList.remove('flex');
                    
                    if (window.cameraOffOverlay) {
                        window.cameraOffOverlay.classList.add('hidden');
                        window.cameraOffOverlay.classList.remove('flex');
                    }
                    if (window.scannerOverlay) {
                        window.scannerOverlay.classList.remove('hidden');
                        window.scannerOverlay.classList.add('flex');
                    }

                    updateStatus('Kamera aktif. Arahkan QR Code ke area kotak.', 'success');
                    window.disableCameraButton.disabled = false;
                    window.enableCameraButton.disabled = true;
                } catch (err) {
                    window.loadingText.textContent = 'Gagal akses kamera.';
                    updateStatus('Izin kamera ditolak atau perangkat tidak mendukung.', 'error');
                    window.loadingOverlay.classList.add('hidden');
                    window.loadingOverlay.classList.remove('flex');
                    
                    if (window.cameraOffOverlay) {
                        window.cameraOffOverlay.classList.remove('hidden');
                        window.cameraOffOverlay.classList.add('flex');
                    }
                    
                    window.disableCameraButton.disabled = true;
                    window.enableCameraButton.disabled = false;
                }
            };

            const renderSchedules = function(schedules) {
                if (!window.scheduleResultsContainer) return;
                window.scheduleResultsContainer.innerHTML = '';

                if (!schedules || schedules.length === 0) {
                    window.scheduleResultsContainer.innerHTML = '<p class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">Tidak ada jadwal yang cocok.</p>';
                    return;
                }

                schedules.forEach(schedule => {
                    const scheduleItem = document.createElement('div');
                    scheduleItem.className = 'p-3 cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900/30 border-b border-gray-100 dark:border-gray-600 last:border-b-0 transition-colors';
                    scheduleItem.dataset.scheduleId = schedule.formatted_id;
                    scheduleItem.dataset.scheduleType = schedule.type;
                    
                    let scheduleText = '';
                    let displayHtml = '';

                    if (schedule.type === 'siswa') {
                        const jamMulai = schedule.jam_mulai ? new Date(schedule.jam_mulai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) : '-';
                        const jamSelesai = schedule.jam_selesai ? new Date(schedule.jam_selesai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) : '-';
                        const mapel = schedule.mata_pelajaran?.nama_mapel || 'Tidak diketahui';
                        const kelas = schedule.kelas?.nama_kelas || 'Tidak diketahui';

                        scheduleText = `${schedule.hari} - ${mapel} - ${kelas} (${jamMulai} - ${jamSelesai})`;
                        displayHtml = `
                            <p class="font-medium text-gray-800 dark:text-gray-200">${schedule.hari} - ${mapel}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><span class="font-semibold">${kelas}</span> &bull; ${jamMulai} - ${jamSelesai}</p>
                        `;
                    }
                    
                    scheduleItem.dataset.scheduleText = scheduleText;
                    scheduleItem.innerHTML = displayHtml;
                    scheduleItem.addEventListener('click', (event) => {
                        handleScheduleSelection(
                            event.currentTarget.dataset.scheduleId, 
                            event.currentTarget.dataset.scheduleType, 
                            event.currentTarget.dataset.scheduleText
                        );
                    });
                    window.scheduleResultsContainer.appendChild(scheduleItem);
                });
            };

            const filterAndRenderSchedules = function() {
                const searchTerm = window.scheduleSearchInput.value.toLowerCase();
                const filteredSchedules = window.allAvailableSchedules.filter(schedule => {
                    if (schedule.type === 'siswa') {
                        const mapel = schedule.mata_pelajaran?.nama_mapel || '';
                        const kelas = schedule.kelas?.nama_kelas || '';
                        const searchStr = `${schedule.hari} ${mapel} ${kelas}`.toLowerCase();
                        return searchStr.includes(searchTerm);
                    }
                    return false;
                });
                renderSchedules(filteredSchedules);
            };

            const handleScheduleSelection = function(id, type, text) {
                window.selectedScheduleId = id;
                window.selectedScheduleType = type;
                window.activeScheduleText.textContent = text;

                document.getElementById('schedule-selection-area').classList.add('hidden');
                window.selectedScheduleInfo.classList.remove('hidden');
                window.scannerSection.classList.remove('hidden');
                window.feedbackSection.classList.remove('hidden');

                startScanner();
            };

            const resetScheduleSelection = function() {
                window.selectedScheduleId = null;
                window.selectedScheduleType = null;
                window.activeScheduleText.textContent = '';

                if (window.isUserAdmin) {
                    document.getElementById('schedule-selection-area').classList.add('hidden');
                    window.selectedScheduleInfo.classList.add('hidden');
                    window.scannerSection.classList.remove('hidden');
                    window.feedbackSection.classList.remove('hidden');
                    stopScanner();
                    updateStatus('Kamera nonaktif. Klik Aktifkan untuk mulai.', 'info');
                } else {
                    window.selectedScheduleInfo.classList.add('hidden');
                    window.scannerSection.classList.add('hidden');
                    window.feedbackSection.classList.add('hidden');
                    document.getElementById('schedule-selection-area').classList.remove('hidden');

                    if (window.scheduleSearchInput) {
                        window.scheduleSearchInput.value = '';
                        filterAndRenderSchedules();
                    }
                    stopScanner();
                }
            };

            const onScanSuccess = async function(decodedText, decodedResult) {
                if (window.scanner && window.scanner.isScanning) {
                    window.scanner.pause(true); // pause and freeze frame
                }

                updateStatus(`Memproses QR: ${decodedText}...`, 'info');
                if (navigator.vibrate) navigator.vibrate(150);
                
                if (window.cameraContainer) {
                    window.cameraContainer.classList.add('ring-4', 'ring-emerald-400');
                    setTimeout(() => window.cameraContainer.classList.remove('ring-4', 'ring-emerald-400'), 600);
                }
                
                const successSound = document.getElementById('success-sound');
                if (successSound) {
                    successSound.currentTime = 0;
                    successSound.play().catch(e => console.log('Audio play blocked:', e));
                }

                try {
                    const response = await fetch('<?php echo e(route("scan.store")); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            identifier: decodedText,
                            ...(window.isUserAdmin ? {} : {
                                jadwal_id: window.selectedScheduleId,
                                jadwal_type: window.selectedScheduleType
                            })
                        })
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Terjadi kesalahan pada server.');
                    }

                    updateStatus(result.message, 'success');
                    addHistory(result.message, 'success');

                } catch (error) {
                    updateStatus(error.message || 'Gagal terhubung ke server.', 'error');
                    addHistory(error.message || 'Gagal terhubung.', 'error');
                    if (window.cameraContainer) {
                        window.cameraContainer.classList.add('ring-4', 'ring-rose-400');
                        setTimeout(() => window.cameraContainer.classList.remove('ring-4', 'ring-rose-400'), 600);
                    }
                } finally {
                    setTimeout(() => {
                        if (window.scanner && window.scannerStarted) {
                            window.scanner.resume();
                            updateStatus('Siap memindai...', 'info');
                        }
                    }, 2000); // Jeda sebelum siap scan selanjutnya
                }
            };

            const onScanFailure = function(error) {
                // Diabaikan agar konsol tidak spam error saat sedang mencari QR
            };

            const setupScanner = function() {
                window.disableCameraButton = document.getElementById('disable-camera-button');
                window.enableCameraButton = document.getElementById('enable-camera-button');
                window.cameraOffOverlay = document.getElementById('camera-off-overlay');
                window.loadingOverlay = document.getElementById('loading-overlay');
                window.loadingText = document.getElementById('loading-text');
                window.cameraContainer = document.getElementById('camera-container');
                window.scannerOverlay = document.getElementById('scanner-overlay');

                window.scheduleSearchInput = document.getElementById('schedule-search-input');
                window.scheduleResultsContainer = document.getElementById('schedule-results-container');
                window.selectedScheduleInfo = document.getElementById('selected-schedule-info');
                window.activeScheduleText = document.getElementById('active-schedule-text');
                window.scannerSection = document.getElementById('scanner-section');
                window.feedbackSection = document.getElementById('feedback-section');
                window.changeScheduleButton = document.getElementById('change-schedule-button');

                if (window.disableCameraButton) {
                    window.disableCameraButton.removeEventListener('click', stopScanner);
                    window.disableCameraButton.addEventListener('click', stopScanner);
                }
                if (window.enableCameraButton) {
                    window.enableCameraButton.removeEventListener('click', startScanner);
                    window.enableCameraButton.addEventListener('click', startScanner);
                }
                if (window.scheduleSearchInput) {
                    window.scheduleSearchInput.addEventListener('input', filterAndRenderSchedules);
                }
                if (window.changeScheduleButton) {
                    window.changeScheduleButton.removeEventListener('click', resetScheduleSelection);
                    window.changeScheduleButton.addEventListener('click', resetScheduleSelection);
                }

                renderSchedules(window.allAvailableSchedules);

                if (window.isUserAdmin) {
                    document.getElementById('schedule-selection-area').classList.add('hidden');
                    window.selectedScheduleInfo.classList.add('hidden');
                    window.scannerSection.classList.remove('hidden');
                    window.feedbackSection.classList.remove('hidden');
                    startScanner();
                } else if (window.initialJadwalId) {
                    window.initialJadwal = window.allAvailableSchedules.find(
                        s => s.formatted_id === `siswa_${window.initialJadwalId}`
                    );
                    if (window.initialJadwal) {
                        const mapel = window.initialJadwal.mata_pelajaran?.nama_mapel || '';
                        const kelas = window.initialJadwal.kelas?.nama_kelas || '';
                        const jamMulai = window.initialJadwal.jam_mulai ? new Date(window.initialJadwal.jam_mulai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) : '-';
                        const jamSelesai = window.initialJadwal.jam_selesai ? new Date(window.initialJadwal.jam_selesai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) : '-';
                        
                        const jadwalText = `${window.initialJadwal.hari} - ${mapel} - ${kelas} (${jamMulai} - ${jamSelesai})`;
                        handleScheduleSelection(window.initialJadwal.formatted_id, window.initialJadwal.type, jadwalText);
                    } else {
                        document.getElementById('schedule-selection-area').classList.remove('hidden');
                    }
                } else {
                    document.getElementById('schedule-selection-area').classList.remove('hidden');
                }
            };

            document.addEventListener('turbo:load', setupScanner);

            document.addEventListener('turbo:before-cache', async () => {
                if (window.scanner && window.scanner.isScanning && !window.isStartingOrStoppingScanner) {
                    try {
                        await window.scanner.stop();
                        window.scannerStarted = false;
                        window.scanner = null;
                    } catch (err) {}
                }
            });
        })();
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /var/www/resources/views/scan/index.blade.php ENDPATH**/ ?>