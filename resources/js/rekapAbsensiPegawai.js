import Alpine from "alpinejs";
import flatpickr from "flatpickr";
import TomSelect from "tom-select";
import axios from 'axios';
import * as Turbo from "@hotwired/turbo"; // Ensure Turbo is imported if used for navigation

document.addEventListener('alpine:init', () => {
    Alpine.store('rekapAbsensiPegawai', {
        selectedAbsensi: [],
        isDeleting: false,
        showBulkDeleteConfirm: false,
        showExportModal: false,
        showSingleDeleteConfirm: false,
        deleteAbsensiId: null,
        tomSelectInstances: [],
        exportTomSelectInstances: [],
        exportFlatpickrInstances: [],
        bulkDeleteUrl: '', // Will be set when initPage is called
        filters: {
            startDate: '',
            endDate: '',
            userId: '',
            pegawaiRole: '',
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
            this.filters.userId = urlParams.get('user_id') || '';
            this.filters.pegawaiRole = urlParams.get('pegawai_role') || '';
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
            const deleteUrl = `/rekap-absensi-pegawai/${this.deleteAbsensiId}`; // Corrected route

            try {
                const response = await axios.delete(deleteUrl);

                if (response.data.success) {
                    this.showNotification(response.data.message, 'success');
                    Turbo.visit(window.location.href, { action: 'replace' });
                } else {
                    this.showNotification(response.data.message || 'Gagal menghapus absensi pegawai.', 'error');
                }
            } catch (error) {
                console.error('Single delete error:', error);
                this.showNotification('Terjadi kesalahan saat menghapus absensi pegawai.', 'error');
            } finally {
                this.isDeleting = false;
                this.showSingleDeleteConfirm = false;
                this.deleteAbsensiId = null;
            }
        },

        bulkDelete: async () => {
            if (Alpine.store('rekapAbsensiPegawai').selectedAbsensi.length === 0 || !Alpine.store('rekapAbsensiPegawai').bulkDeleteUrl) return;
            Alpine.store('rekapAbsensiPegawai').isDeleting = true;

            try {
                const response = await axios.post(Alpine.store('rekapAbsensiPegawai').bulkDeleteUrl, {
                    absensi_pegawai_ids: Alpine.store('rekapAbsensiPegawai').selectedAbsensi // Corrected payload key
                });

                if (response.data.success) {
                    Alpine.store('rekapAbsensiPegawai').showNotification(response.data.message, 'success');
                    
                    // Clear selected items and uncheck checkboxes immediately
                    Alpine.store('rekapAbsensiPegawai').selectedAbsensi = [];
                    document.querySelectorAll('input[type="checkbox"][id^="checkbox-item-"]').forEach(cb => cb.checked = false);
                    document.getElementById('checkbox-all-items').checked = false;
                    document.getElementById('checkbox-all-items').indeterminate = false;

                    Turbo.visit(window.location.href, { action: 'replace' });

                } else {
                    Alpine.store('rekapAbsensiPegawai').showNotification(response.data.message || 'Gagal menghapus absensi pegawai.', 'error');
                }
            } catch (error) {
                console.error('Bulk delete error:', error);
                Alpine.store('rekapAbsensiPegawai').showNotification('Terjadi kesalahan saat menghapus absensi pegawai.', 'error');
            } finally {
                Alpine.store('rekapAbsensiPegawai').isDeleting = false;
                Alpine.store('rekapAbsensiPegawai').showBulkDeleteConfirm = false;
            }
        },

        resetExportFilters() {
            this.filters = {
                startDate: '',
                endDate: '',
                userId: '',
                pegawaiRole: '',
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
            // Placeholder for a global notification system if available in the app.js
            // If not, you might need to implement a simple one here or use a custom event.
            console.log(`Notification (${type}): ${message}`);
            // Example: dispatch a custom event that can be listened to by a global toast component
            window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
        }
    });
});
