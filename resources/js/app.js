import axios from 'axios';
import { getSidebarState, setSidebarState } from './sidebar.js';
import { isDarkMode, setTheme } from './theme.js';

window.isDarkMode = isDarkMode;
window.setTheme = setTheme;
window.getSidebarState = getSidebarState;
window.setSidebarState = setSidebarState;
window.axios = axios;
import Alpine from "alpinejs";
import collapse from '@alpinejs/collapse';
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";
window.TomSelect = TomSelect;
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import * as Turbo from "@hotwired/turbo";

window.Turbo = Turbo; // Make Turbo globally available

// Import Alpine.js components
import kelasEditor from "./kelasEditor.js";
import tomSelectManager from "./tomSelectManager.js";
import jadwalCreateManager from "./jadwalCreateManager.js";
import { togglePasswordVisibility, initializePasswordStrengthChecker } from "./passwordHandler.js";

window.initializePasswordStrengthChecker = initializePasswordStrengthChecker;

window.togglePasswordVisibility = togglePasswordVisibility; // Expose globally
import './adminDashboardCharts.js';
import './tableDragScroll.js';

// Global store for Rekap Absensi page
document.addEventListener('alpine:init', () => {
    Alpine.store('rekapAbsensi', {
        selectedAbsensi: [],
        isDeleting: false,
        showBulkDeleteConfirm: false,
        showExportModal: false,
        showSingleDeleteConfirm: false,
        deleteAbsensiId: null,
        tomSelectInstances: [],
        exportTomSelectInstances: [],
        exportFlatpickrInstances: [],
        bulkDeleteUrl: '', // Akan diisi saat initPage dipanggil
        filters: {
            startDate: '',
            endDate: '',
            kelasId: '',
            mapelId: '',
            guruId: '',
            userId: '',
            attendanceType: '',
            status: '',
            search: ''
        },

        initPage(config) {
            if (config && config.bulkDeleteUrl) {
                this.bulkDeleteUrl = config.bulkDeleteUrl;
            }

            if (config && config.filters) {
                this.filters = { ...this.filters, ...config.filters };
            }

            const urlParams = new URLSearchParams(window.location.search);
            this.filters.startDate = urlParams.get('start_date') || '';
            this.filters.endDate = urlParams.get('end_date') || '';
            this.filters.kelasId = urlParams.get('kelas_id') || '';
            this.filters.mapelId = urlParams.get('mata_pelajaran_id') || '';
            this.filters.guruId = urlParams.get('guru_id') || '';
            this.filters.userId = urlParams.get('user_id') || '';
            this.filters.attendanceType = urlParams.get('attendance_type') || '';
            this.filters.status = urlParams.get('status') || '';
            this.filters.search = urlParams.get('search') || '';

            // Initialize Flatpickr for date inputs
            document.querySelectorAll('.flatpickr-date').forEach(fp => {
                flatpickr(fp, {
                    dateFormat: "Y-m-d",
                    allowInput: false // Disables manual input to prevent autofill conflicts
                });
            });

            // Initialize TomSelect for main filters
            document.querySelectorAll('select.tom-select-rekap').forEach(el => {
                if (el.tomselect) return;
                const instance = new TomSelect(el, {
                    plugins: ['remove_button'],
                    create: false,
                });
                this.tomSelectInstances.push(instance);
            });
        },

        initExportModal() {
            // Initialize TomSelect for export modal filters
            document.querySelectorAll('select.tom-select-export').forEach(el => {
                if (el.tomselect) return;
                const instance = new TomSelect(el, {
                    plugins: ['remove_button'],
                    create: false,
                });
                this.exportTomSelectInstances.push(instance);
            });

            // Initialize Flatpickr for export modal date filters
            const self = this;
            document.querySelectorAll('.flatpickr-date-export').forEach(el => {
                const instance = flatpickr(el, {
                    dateFormat: "Y-m-d",
                    defaultDate: self.filters[el.name === 'start_date' ? 'startDate' : 'endDate'],
                    onChange: function(selectedDates, dateStr, instance) {
                        if (instance.input.name === 'start_date') {
                            self.filters.startDate = dateStr;
                        } else if (instance.input.name === 'end_date') {
                            self.filters.endDate = dateStr;
                        }
                    },
                });
                this.exportFlatpickrInstances.push(instance);
            });
        },

        destroyExportModal() {
            this.exportTomSelectInstances.forEach(instance => {
                if (instance && instance.destroy) {
                    instance.destroy();
                }
            });
            this.exportTomSelectInstances = [];

            this.exportFlatpickrInstances.forEach(instance => {
                if (instance && instance.destroy) {
                    instance.destroy();
                }
            });
            this.exportFlatpickrInstances = [];
        },

        destroyPage() {
            this.tomSelectInstances.forEach(instance => {
                if (instance && instance.destroy) {
                    instance.destroy();
                }
            });
            this.tomSelectInstances = [];
            this.destroyExportModal();
            this.resetExportFilters();
        },

        toggleSelectAll(event) {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][id^="checkbox-item-"]');
            let selected = [];
            if (event.target.checked) {
                checkboxes.forEach(cb => {
                    selected.push(cb.value);
                });
            }
            this.selectedAbsensi = selected;
        },

        confirmSingleDelete(id) {
            this.deleteAbsensiId = id;
            this.showSingleDeleteConfirm = true;
        },

        async deleteSingleAbsensi() {
            if (!this.deleteAbsensiId) return;

            this.isDeleting = true;
            const deleteUrl = `/rekap_absensi/${this.deleteAbsensiId}`;

            try {
                const response = await axios.delete(deleteUrl);

                if (response.data.success) {
                    this.showNotification(response.data.message, 'success');
                    Turbo.visit(window.location.href, { action: 'replace' });
                } else {
                    this.showNotification(response.data.message || 'Gagal menghapus absensi.', 'error');
                }
            } catch (error) {
                console.error('Single delete error:', error);
                this.showNotification('Terjadi kesalahan saat menghapus absensi.', 'error');
            } finally {
                this.isDeleting = false;
                this.showSingleDeleteConfirm = false;
                this.deleteAbsensiId = null;
            }
        },

        bulkDelete: async () => {
            if (Alpine.store('rekapAbsensi').selectedAbsensi.length === 0 || !Alpine.store('rekapAbsensi').bulkDeleteUrl) return;
            Alpine.store('rekapAbsensi').isDeleting = true;

            try {
                const response = await axios.post(Alpine.store('rekapAbsensi').bulkDeleteUrl, {
                    absensi_ids: Alpine.store('rekapAbsensi').selectedAbsensi
                });

                if (response.data.success) {
                    Alpine.store('rekapAbsensi').showNotification(response.data.message, 'success');
                    
                    // Clear selected items and uncheck checkboxes immediately
                    Alpine.store('rekapAbsensi').selectedAbsensi = [];
                    document.querySelectorAll('input[type="checkbox"][id^="checkbox-item-"]').forEach(cb => cb.checked = false);
                    document.getElementById('checkbox-all-items').checked = false;
                    document.getElementById('checkbox-all-items').indeterminate = false;

                    Turbo.visit(window.location.href, { action: 'replace' });

                } else {
                    Alpine.store('rekapAbsensi').showNotification(response.data.message || 'Gagal menghapus absensi.', 'error');
                }
            } catch (error) {
                console.error('Bulk delete error:', error);
                Alpine.store('rekapAbsensi').showNotification('Terjadi kesalahan saat menghapus absensi.', 'error');
            } finally {
                Alpine.store('rekapAbsensi').isDeleting = false;
                Alpine.store('rekapAbsensi').showBulkDeleteConfirm = false;
            }
        },

        resetExportFilters() {
            this.filters = {
                startDate: '',
                endDate: '',
                kelasId: '',
                mapelId: '',
                guruId: '',
                userId: '',
                attendanceType: '',
                status: '',
                search: ''
            };

            this.exportFlatpickrInstances.forEach(instance => {
                instance.clear();
            });

            this.exportTomSelectInstances.forEach(instance => {
                instance.clear();
            });
        },

        showNotification: (message, type) => {
            Alpine.store('rekapAbsensi').toastMessage = message;
            Alpine.store('rekapAbsensi').toastType = type;
            Alpine.store('rekapAbsensi').showToast = true;

            setTimeout(() => {
                Alpine.store('rekapAbsensi').showToast = false;
                Alpine.store('rekapAbsensi').toastMessage = '';
            }, 3000); // Hide after 3 seconds
        }
    });
});


window.Alpine = Alpine;

Alpine.plugin(collapse);

// Daftarkan komponen Alpine.js
Alpine.data("kelasEditor", kelasEditor);
Alpine.data("tomSelectManager", tomSelectManager);
Alpine.data("jadwalCreateManager", jadwalCreateManager);

Alpine.start();

// Function to initialize Flatpickr time pickers
window.initializeFlatpickrTime = function() {
    document.querySelectorAll('.flatpickr-time').forEach(function(element) {
        flatpickr(element, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i", // 24-hour format
            time_24hr: true,
            minuteIncrement: 1,
        });
    });
};

window.initializeFlatpickrDMY = function() {
    document.querySelectorAll('.flatpickr-dmy').forEach(function(element) {
        if (element._flatpickr) {
            return; // Already initialized
        }
        flatpickr(element, {
            altInput: true,
            altFormat: "d/m/Y", // User-friendly format
            dateFormat: "Y-m-d", // Server-friendly format
        });
    });
};

function restoreSidebarState() {
    const sidebarOpen = localStorage.getItem("sidebarOpen") === "true";
    const root = document.querySelector("[x-data]");
    if (root && root.__x) {
        root.__x.$data.sidebarOpen = sidebarOpen;
    }
}

document.addEventListener("turbo:load", () => {
    restoreSidebarState();
    initializeFlatpickrTime();
    initializeFlatpickrDMY();
    window.initTableDragScroll(); // Panggil ulang inisialisasi geser tabel

    initializePasswordStrengthChecker('update_password_password', 'password-strength');
    initializePasswordStrengthChecker('password', 'password-strength-login');
    initializePasswordStrengthChecker('password', 'password-strength-register');
    initializePasswordStrengthChecker('password_confirmation', 'password-strength-confirm');
});

document.addEventListener("turbo:frame-load", restoreSidebarState);
document.addEventListener("turbo:frame-render", restoreSidebarState);
