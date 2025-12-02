<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pindai QR Code Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6">
                    <!-- 1. Camera Viewport -->
                                    <div class="p-6">
                    <!-- Schedule Selection -->
                    <div id="schedule-selection-area" class="mb-6">
                        <label for="schedule-search-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari Jadwal Absensi:</label>
                        <input type="text" id="schedule-search-input" placeholder="Ketik untuk mencari jadwal..." class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                        <div id="schedule-results-container" class="mt-2 max-h-60 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 shadow-lg">
                            <!-- Search results will be rendered here by JavaScript -->
                        </div>
                    </div>

                    <!-- Selected Schedule Info -->
                    <div id="selected-schedule-info" class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-800 dark:text-indigo-200 font-medium hidden">
                        <div class="flex justify-between items-center">
                            <span>Jadwal Aktif: <span id="active-schedule-text"></span></span>
                            <button id="change-schedule-button" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-75">
                                Ganti Jadwal
                            </button>
                        </div>
                    </div>

                    <div id="scanner-section" class="hidden">
                        <!-- 1. Camera Viewport -->
                        <div id="camera-container" class="w-full h-[480px] bg-gray-900 rounded-lg overflow-hidden relative shadow-2xl border-8 border-gray-600 transition-all duration-300 ease-in-out">
                            <div class="absolute bottom-4 left-0 right-0 flex items-center justify-center pointer-events-none z-10">
                                <p class="text-white text-lg font-semibold bg-black bg-opacity-50 px-4 py-2 rounded-md">Posisikan QR Code di sini</p>
                            </div>
                            <div id="reader" class="w-full h-full"></div>
                            <div id="loading-overlay" class="absolute inset-0 flex-col items-center justify-center bg-gray-900 bg-opacity-75 transition-opacity duration-500 hidden">
                                <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-16 w-16 mb-4"></div>
                                <p id="loading-text" class="text-white font-semibold text-lg">Memulai kamera...</p>
                            </div>
                            <div id="camera-off-overlay" class="absolute inset-0 flex-col items-center justify-center bg-gray-900 bg-opacity-90 hidden">
                                <svg class="w-20 h-20 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2A9 9 0 111 10a9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-white font-semibold text-lg text-center">Kamera tidak aktif.</p>
                                <p class="text-gray-300 text-sm text-center">Silakan aktifkan kamera untuk memulai pemindaian.</p>
                            </div>
                        </div>

                        <!-- Camera Controls -->
                        <div class="mt-4 flex flex-wrap justify-center gap-4">
                            <button id="disable-camera-button" title="Nonaktifkan Kamera" class="px-6 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-75">
                                Nonaktifkan
                            </button>
                            <button id="enable-camera-button" title="Aktifkan Kamera" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-75" disabled>
                                Aktifkan
                            </button>
                        </div>
                    </div>

                    <div id="feedback-section" class="hidden">
                        <!-- 2. Status & History -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Status Pindai</h3>
                            <div id="status-message" class="mt-2 text-center font-semibold p-4 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200" role="alert">
                                Menunggu kamera siap...
                            </div>
                            
                            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mt-4 mb-2">Riwayat Terakhir:</h4>
                            <div id="history-log" class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                <p class="text-center text-gray-500 dark:text-gray-400">Belum ada aktivitas.</p>
                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                    

                    
                </div>
            </div>
        </div>
    </div>

    <audio id="success-sound" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>

    @push('styles')
    <style>
        /* Ensures the video stream fills the container without distortion */
        #reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain; /* Changed from cover to contain */
        }
        /* Custom scrollbar for history log */
        #history-log::-webkit-scrollbar { width: 6px; }
        #history-log::-webkit-scrollbar-track { background: transparent; }
        #history-log::-webkit-scrollbar-thumb { background: #888; border-radius: 3px; }

        /* Loading Overlay Centering */
        #loading-overlay {
            width: 100% !important;
            height: 100% !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* Spinner CSS */
        .loader {
            border-top-color: #3498db;
            -webkit-animation: spinner 1.5s linear infinite;
            animation: spinner 1.5s linear infinite;
        }

        @-webkit-keyframes spinner {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('js/html5-qrcode.min.js') }}" type="text/javascript"></script>
    <script>
        // Declare global variables for elements and scanner instance
        // This prevents re-declaration errors when Turbo re-evaluates the script block.
        window.disableCameraButton = window.disableCameraButton || null;
        window.enableCameraButton = window.enableCameraButton || null;
        window.cameraOffOverlay = window.cameraOffOverlay || null;
        window.loadingOverlay = window.loadingOverlay || null;
        window.loadingText = window.loadingText || null;

        // New global variables for schedule selection and display
        window.scheduleSearchInput = window.scheduleSearchInput || null;
        window.scheduleResultsContainer = window.scheduleResultsContainer || null;
        window.allAvailableSchedules = @json($availableSchedules); // Store all schedules
        window.selectedScheduleInfo = window.selectedScheduleInfo || null;
        window.activeScheduleText = window.activeScheduleText || null;
        window.scannerSection = window.scannerSection || null;
        window.feedbackSection = window.feedbackSection || null;
        window.scannedStudentsList = window.scannedStudentsList || null;
        window.activeJadwalAbsensiId = window.activeJadwalAbsensiId || null; // Still used for API calls
        window.selectedScheduleId = window.selectedScheduleId || null; // New variable for selected ID
        window.selectedScheduleType = window.selectedScheduleType || null; // New variable for selected schedule type
        window.isUserAdmin = {{ $user->isAdmin() ? 'true' : 'false' }}; // Pass user role to JS
        window.initialJadwalId = @json(request('jadwal_id')); // Get jadwal_id from URL
        window.initialJadwal = null; // To store the full schedule object if found

        // Global flags for scanner state
        window.scannerStarted = window.scannerStarted || false;
        window.scanner = window.scanner || null; // Global instance for Html5Qrcode
        window.isStartingOrStoppingScanner = window.isStartingOrStoppingScanner || false;

        (function() { // Wrap in IIFE for isolated function definitions and local variables
            // Helper functions
            const updateStatus = function(message, type = 'info') {
                const statusDiv = document.getElementById('status-message');
                if (!statusDiv) return;
                statusDiv.textContent = message;
                statusDiv.className = 'mt-2 text-center font-semibold p-4 rounded-lg transition-colors duration-300 '; // Reset classes
                switch (type) {
                    case 'success':
                        statusDiv.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-800/30', 'dark:text-green-200');
                        break;
                    case 'error':
                        statusDiv.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-800/30', 'dark:text-red-200');
                        break;
                    default:
                        statusDiv.classList.add('bg-gray-100', 'text-gray-800', 'dark:bg-gray-700', 'dark:text-gray-200');
                        break;
                }
            };

            const addHistory = function(message, type) {
                const historyLog = document.getElementById('history-log');
                if (!historyLog) return;
                if (historyLog.querySelector('p')) {
                    historyLog.innerHTML = ''; // Clear initial message
                }
                const entry = document.createElement('div');
                entry.textContent = `${new Date().toLocaleTimeString('id-ID')}: ${message}`;
                entry.className = `p-2 rounded-md text-sm `;
                if (type === 'success') entry.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-900', 'dark:text-green-200');
                else if (type === 'error') entry.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-900', 'dark:text-red-200');
                historyLog.prepend(entry);
                if (historyLog.children.length > 10) {
                    historyLog.removeChild(historyLog.lastChild);
                }
            };

            // Function to stop the scanner
            const stopScanner = async function() {
                if (window.isStartingOrStoppingScanner) {
                    console.log("Peringatan: Upaya untuk menghentikan scanner saat operasi sedang berlangsung dicegah.");
                    return;
                }
                if (window.scanner && window.scanner.isScanning) {
                    window.isStartingOrStoppingScanner = true;
                    try {
                        await window.scanner.stop();
                        console.log("Scanner berhasil dihentikan.");
                        window.scannerStarted = false;
                        updateStatus('Kamera dinonaktifkan.', 'info');
                        window.cameraOffOverlay.classList.remove('hidden');
                        window.cameraOffOverlay.classList.add('flex');
                        window.disableCameraButton.disabled = true;
                        window.enableCameraButton.disabled = false;

                        // Hide scanner and feedback sections, then reset schedule selection
                        // by calling resetScheduleSelection which handles UI visibility
                        resetScheduleSelection();

                    } catch (err) {
                        console.error("Gagal menghentikan scanner:", err);
                        updateStatus('Gagal menonaktifkan kamera.', 'error');
                    } finally {
                        window.isStartingOrStoppingScanner = false;
                    }
                }
            };

            // Function to start the scanner
            const startScanner = async function() {
                if (window.scannerStarted || window.isStartingOrStoppingScanner) {
                    console.log("Peringatan: Upaya untuk memulai scanner yang sudah berjalan atau sedang dalam transisi dicegah.");
                    return;
                }

                const scannerContainer = document.getElementById('reader');
                if (!scannerContainer) {
                    console.log("Info: Elemen 'reader' tidak ditemukan di halaman ini, scanner tidak dimulai.");
                    return;
                }

                // Ensure Html5Qrcode is defined before proceeding
                if (typeof Html5Qrcode === 'undefined') {
                    console.log("Html5Qrcode library not loaded yet. Retrying startScanner...");
                    setTimeout(startScanner, 100); // Retry after a short delay
                    return;
                }

                if (!window.scanner) {
                    window.scanner = new Html5Qrcode("reader");
                }

                window.loadingOverlay.classList.remove('hidden');
                window.loadingOverlay.classList.add('flex');
                window.loadingOverlay.style.opacity = '1';
                window.loadingOverlay.style.pointerEvents = 'auto';
                window.loadingText.textContent = 'Memulai kamera...';
                updateStatus('Memulai kamera...', 'info');

                const config = {
                    fps: 15, // Increased FPS for faster scanning
                    qrbox: function(viewfinderWidth, viewfinderHeight) {
                        let minEdgePercentage = 0.75; // 75% of the smaller edge for a larger box
                        let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                        let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
                        // Ensure qrbox is within a reasonable range (e.g., min 250, max 400)
                        qrboxSize = Math.max(250, Math.min(400, qrboxSize)); // Increased min and max
                        return {
                            width: qrboxSize,
                            height: qrboxSize
                        };
                    },
                    disableFlip: true, // Disable camera flip for potentially faster processing
                };

                try {
                    await window.scanner.start(
                        {
                            facingMode: "environment"
                        }, // Gunakan kamera belakang
                        config,
                        onScanSuccess,
                        onScanFailure
                    );
                    console.log("Kamera berhasil dimulai. Scanner sekarang aktif.");
                    window.scannerStarted = true;
                    window.loadingOverlay.style.opacity = '0'; // Ensure opacity is 0
                    window.loadingOverlay.style.pointerEvents = 'none'; // Disable pointer events
                    window.loadingOverlay.classList.add('hidden');
                    window.loadingOverlay.classList.remove('flex');
                    window.cameraOffOverlay.classList.add('hidden');
                    window.cameraOffOverlay.classList.remove('flex'); // Remove flex when camera is active
                    updateStatus('Kamera aktif. Siap memindai...', 'success');
                    window.disableCameraButton.disabled = false;
                    window.enableCameraButton.disabled = true;
                } catch (err) {
                    console.error("GAGAL TOTAL memulai scanner:", err);
                    console.log("Detail error:", err.name, err.message, err.stack);
                    window.loadingText.textContent = err.message || 'Gagal memulai kamera.';
                    updateStatus(err.message || 'Gagal memulai kamera.', 'error');
                    window.loadingOverlay.style.opacity = '0';
                    window.loadingOverlay.style.pointerEvents = 'none';
                    window.loadingOverlay.classList.add('hidden');
                    window.loadingOverlay.classList.remove('flex'); // Remove flex when camera fails
                    window.scannerStarted = false;
                    window.disableCameraButton.disabled = true; // Keep disabled if camera fails to start
                    window.enableCameraButton.disabled = false;
                    window.cameraOffOverlay.classList.remove('hidden'); // Tampilkan overlay jika gagal
                    window.cameraOffOverlay.classList.add('flex'); // Add flex when camera fails
                }
            };

            // Function to render schedules into the results container
            const renderSchedules = function(schedules) {
                if (!window.scheduleResultsContainer) return;

                window.scheduleResultsContainer.innerHTML = ''; // Clear previous results

                if (schedules.length === 0) {
                    window.scheduleResultsContainer.innerHTML = '<p class="p-2 text-gray-500 dark:text-gray-400">Tidak ada jadwal yang cocok.</p>';
                    return;
                }

                schedules.forEach(schedule => {
                    const scheduleItem = document.createElement('div');
                    scheduleItem.className = 'p-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-indigo-800 border-b border-gray-200 dark:border-gray-600 last:border-b-0';
                    scheduleItem.dataset.scheduleId = schedule.formatted_id; // Use formatted_id
                    scheduleItem.dataset.scheduleType = schedule.type; // Store type
                    
                    let scheduleText = '';
                    let displayHtml = '';

                    if (schedule.type === 'siswa') {
                        scheduleText = `${schedule.hari} - ${schedule.mata_pelajaran.nama_mapel} - ${schedule.kelas.nama_kelas} (${new Date(schedule.jam_mulai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})} - ${new Date(schedule.jam_selesai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})})`;
                        displayHtml = `
                            <p class="font-semibold text-gray-800 dark:text-gray-200">${schedule.hari} - ${schedule.mata_pelajaran.nama_mapel}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">${schedule.kelas.nama_kelas} (${new Date(schedule.jam_mulai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})} - ${new Date(schedule.jam_selesai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})})</p>
                        `;
                    } 
                    // else if (schedule.type === 'pegawai') { // No need to render employee schedules here
                    //     scheduleText = `${schedule.hari} - ${schedule.user.name} - Pegawai (${new Date(schedule.jam_mulai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})} - ${new Date(schedule.jam_selesai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})})`;
                    //     displayHtml = `
                    //         <p class="font-semibold text-gray-800 dark:text-gray-200">${schedule.hari} - ${schedule.user.name}</p>
                    //         <p class="text-sm text-gray-600 dark:text-gray-400">Pegawai (${new Date(schedule.jam_mulai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})} - ${new Date(schedule.jam_selesai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})})</p>
                    //     `;
                    // }
                    
                    scheduleItem.dataset.scheduleText = scheduleText;
                    scheduleItem.innerHTML = displayHtml;
                    scheduleItem.addEventListener('click', (event) => {
                        const selectedId = event.currentTarget.dataset.scheduleId;
                        const selectedType = event.currentTarget.dataset.scheduleType;
                        const selectedText = event.currentTarget.dataset.scheduleText;
                        handleScheduleSelection(selectedId, selectedType, selectedText);
                    });
                    window.scheduleResultsContainer.appendChild(scheduleItem);
                });
            };

            // Function to filter schedules based on input and render them
            const filterAndRenderSchedules = function() {
                const searchTerm = window.scheduleSearchInput.value.toLowerCase();
                const filteredSchedules = window.allAvailableSchedules.filter(schedule => {
                    let scheduleString = '';
                    if (schedule.type === 'siswa') {
                        scheduleString = `${schedule.hari} ${schedule.mata_pelajaran.nama_mapel} ${schedule.kelas.nama_kelas} ${new Date(schedule.jam_mulai).toLocaleTimeString('id-ID')} ${new Date(schedule.jam_selesai).toLocaleTimeString('id-ID')}`.toLowerCase();
                    } 
                    // else if (schedule.type === 'pegawai') { // No need to filter employee schedules here
                    //     scheduleString = `${schedule.hari} ${schedule.user.name} Pegawai ${new Date(schedule.jam_mulai).toLocaleTimeString('id-ID')} ${new Date(schedule.jam_selesai).toLocaleTimeString('id-ID')}`.toLowerCase();
                    // }
                    return scheduleString.includes(searchTerm);
                });
                renderSchedules(filteredSchedules);
            };

            // Function to handle schedule selection from the search results
            const handleScheduleSelection = function(jadwalFormattedId, jadwalType, jadwalText) {
                window.selectedScheduleId = jadwalFormattedId; // Store formatted ID
                window.selectedScheduleType = jadwalType; // Store type
                window.activeScheduleText.textContent = jadwalText;

                // Hide search area, show selected schedule info and scanner
                document.getElementById('schedule-selection-area').classList.add('hidden');
                window.selectedScheduleInfo.classList.remove('hidden');
                window.scannerSection.classList.remove('hidden');
                window.feedbackSection.classList.remove('hidden');

                startScanner();
            };

            // Function to reset schedule selection and manage UI visibility based on user role
            const resetScheduleSelection = function() {
                window.activeJadwalAbsensiId = null;
                window.selectedScheduleId = null;
                window.selectedScheduleType = null; // Clear selected schedule type
                window.activeScheduleText.textContent = '';

                if (window.isUserAdmin) {
                    // For admin, keep schedule selection and info hidden, just ensure scanner is manageable
                    document.getElementById('schedule-selection-area').classList.add('hidden');
                    window.selectedScheduleInfo.classList.add('hidden');
                    window.scannerSection.classList.remove('hidden');
                    window.feedbackSection.classList.remove('hidden');
                    // Ensure camera is stopped and buttons are in correct state
                    stopScanner(); // Will set cameraOffOverlay and button states
                    updateStatus('Kamera tidak aktif. Klik Aktifkan untuk memulai pemindaian.', 'info');
                } else {
                    // For non-admin (guru), revert to schedule selection state
                    window.selectedScheduleInfo.classList.add('hidden');
                    window.scannerSection.classList.add('hidden');
                    window.feedbackSection.classList.add('hidden');
                    document.getElementById('schedule-selection-area').classList.remove('hidden');

                    // Clear search input and re-render all schedules
                    if (window.scheduleSearchInput) {
                        window.scheduleSearchInput.value = '';
                        filterAndRenderSchedules(); // Render all schedules
                    }
                    stopScanner(); // Stop camera
                }
            };

            // Fungsi untuk menangani hasil scan yang berhasil
            const onScanSuccess = async function(decodedText, decodedResult) { // Made async
                // Tangani hasil scan di sini.
                console.log(`Hasil Pindai: ${decodedText}`, decodedResult);

                if (window.scanner && window.scanner.isScanning) {
                    window.scanner.pause(); // Pause scanner to prevent multiple scans of the same QR
                }

                updateStatus(`Memproses: ${decodedText}`, 'info');
                if (navigator.vibrate) navigator.vibrate(100);
                if (window.cameraContainer) {
                    window.cameraContainer.classList.add('border-green-500', 'ring-4', 'ring-green-500');
                    setTimeout(() => {
                        window.cameraContainer.classList.remove('border-green-500', 'ring-4', 'ring-green-500');
                    }, 500); // Remove after 0.5 seconds
                }
                // Play success sound
                const successSound = document.getElementById('success-sound');
                if (successSound) {
                    successSound.play();
                }

                try {
                    const response = await fetch('{{ route("scan.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            identifier: decodedText,
                            // For admin, jadwal_id and jadwal_type are determined on backend
                            // For guru, these are set by schedule selection
                            ...(window.isUserAdmin ? {} : {
                                jadwal_id: window.selectedScheduleId,
                                jadwal_type: window.selectedScheduleType
                            })
                        })
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Terjadi kesalahan yang tidak diketahui.');
                    }

                    updateStatus(result.message, 'success');
                    addHistory(result.message, 'success');

                    

                } catch (error) {
                    const errorMessage = error.message || 'Gagal terhubung ke server.';
                    updateStatus(errorMessage, 'error');
                    addHistory(errorMessage, 'error');
                    if (window.cameraContainer) {
                        window.cameraContainer.classList.add('border-red-500', 'ring-4', 'ring-red-500');
                        setTimeout(() => {
                            window.cameraContainer.classList.remove('border-red-500', 'ring-4', 'ring-red-500');
                        }, 500); // Remove after 0.5 seconds
                    }
                    } finally {
                    // Resume scanning after a delay
                    setTimeout(() => {
                        if (window.scanner) { // Use window.scanner
                            window.scanner.resume();
                            updateStatus('Siap memindai...', 'info');
                        }
                    }, 1500); // Reduced delay for faster resumption
                }
            };

            // Fungsi untuk menangani kegagalan (opsional, tapi disarankan)
            const onScanFailure = function(error) {
                // Biasanya ini akan sering terpanggil karena scanner terus mencari QR code.
                // Anda bisa biarkan kosong atau hanya log untuk debug.
                // console.warn(`Scan Error: ${error}`);
            };

            // Fungsi utama untuk mengatur dan memulai scanner
            const setupScanner = function() {
                // Re-get elements and assign to global window properties
                window.disableCameraButton = document.getElementById('disable-camera-button');
                window.enableCameraButton = document.getElementById('enable-camera-button');
                window.cameraOffOverlay = document.getElementById('camera-off-overlay');
                window.loadingOverlay = document.getElementById('loading-overlay');
                window.loadingText = document.getElementById('loading-text');
                window.cameraContainer = document.getElementById('camera-container'); // Added for visual feedback

                // Get references to new elements
                window.scheduleSearchInput = document.getElementById('schedule-search-input');
                window.scheduleResultsContainer = document.getElementById('schedule-results-container');
                window.selectedScheduleInfo = document.getElementById('selected-schedule-info');
                window.activeScheduleText = document.getElementById('active-schedule-text');
                window.scannerSection = document.getElementById('scanner-section');
                window.feedbackSection = document.getElementById('feedback-section');
                window.scannedStudentsList = document.getElementById('scanned-students-list');
                window.changeScheduleButton = document.getElementById('change-schedule-button');

                // Initial state: scanner and feedback sections are hidden
                // startScanner() will be called when a schedule is selected

                // Add event listeners to buttons, removing old ones first to prevent duplicates
                if (window.disableCameraButton) {
                    window.disableCameraButton.removeEventListener('click', stopScanner);
                    window.disableCameraButton.addEventListener('click', stopScanner);
                }
                if (window.enableCameraButton) {
                    window.enableCameraButton.removeEventListener('click', startScanner);
                    window.enableCameraButton.addEventListener('click', startScanner);
                }

                // Add event listener for search input
                if (window.scheduleSearchInput) {
                    window.scheduleSearchInput.addEventListener('input', filterAndRenderSchedules);
                }

                // Add event listener for change schedule button
                if (window.changeScheduleButton) {
                    window.changeScheduleButton.removeEventListener('click', resetScheduleSelection);
                    window.changeScheduleButton.addEventListener('click', resetScheduleSelection);
                }

                // Initially render all schedules
                renderSchedules(window.allAvailableSchedules);

                // If user is admin, hide schedule selection and start camera directly.
                if (window.isUserAdmin) {
                    document.getElementById('schedule-selection-area').classList.add('hidden');
                    window.selectedScheduleInfo.classList.add('hidden'); // Ensure this is also hidden
                    window.scannerSection.classList.remove('hidden');
                    window.feedbackSection.classList.remove('hidden');
                    startScanner();
                } else if (window.initialJadwalId) { // For Guru, check initial_jadwal_id from URL
                    window.initialJadwal = window.allAvailableSchedules.find(
                        schedule => schedule.formatted_id === `siswa_${window.initialJadwalId}` // Match formatted_id for students
                    );
                    if (window.initialJadwal) {
                        const jadwalText = `${window.initialJadwal.hari} - ${window.initialJadwal.mata_pelajaran.nama_mapel} - ${window.initialJadwal.kelas.nama_kelas} (${new Date(window.initialJadwal.jam_mulai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})} - ${new Date(window.initialJadwal.jam_selesai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})})`;
                        handleScheduleSelection(window.initialJadwal.formatted_id, window.initialJadwal.type, jadwalText);
                    } else {
                        console.warn(`Jadwal siswa dengan ID ${window.initialJadwalId} tidak ditemukan.`);
                        // If not found, proceed with normal schedule selection
                        document.getElementById('schedule-selection-area').classList.remove('hidden');
                        window.scannerSection.classList.add('hidden');
                        window.feedbackSection.classList.add('hidden');
                    }
                } else {
                    // For Guru, if no initial jadwal_id, show schedule selection
                    document.getElementById('schedule-selection-area').classList.remove('hidden');
                    window.scannerSection.classList.add('hidden');
                    window.feedbackSection.classList.add('hidden');
                }
            };


            // 5. Pemicu
            //    Gunakan 'turbo:load' untuk memastikan skrip dijalankan setelah setiap navigasi Turbo.
            //    'DOMContentLoaded' dihapus untuk mencegah eksekusi ganda.
            document.addEventListener('turbo:load', setupScanner);


            // 6. PENTING: Membersihkan (Cleanup)
            //    Tambahkan listener untuk menghentikan kamera saat pengguna akan meninggalkan halaman.
            //    Ini akan MELEPAS KUNCI pada kamera dan mencegah eror 'Failed to allocate videosource'
            //    saat kembali lagi ke halaman scan.
            document.addEventListener('turbo:before-cache', async () => {
                if (window.scanner && window.scanner.isScanning && !window.isStartingOrStoppingScanner) {
                    window.isStartingOrStoppingScanner = true;
                    console.log("Mencoba menghentikan scanner sebelum meninggalkan halaman (turbo:before-cache).");
                    try {
                        await window.scanner.stop();
                        console.log("Scanner berhasil dihentikan.");
                        window.scannerStarted = false;
                        window.scanner = null; // Clean up scanner instance
                    } catch (err) {
                        console.error("Gagal menghentikan scanner:", err);
                        // Lanjutkan meskipun ada error, agar halaman bisa di-cache
                    } finally {
                        window.isStartingOrStoppingScanner = false;
                    }
                }
            });
        })(); // End IIFE
    </script>
    @endpush
</x-app-layout>
