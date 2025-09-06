// resources/js/kelasEditor.js

export default function kelasEditor(config) {
    return {
        // --- DATA --- //
        currentSiswa: config.initialSiswa || [],
        siswaTanpaKelas: config.siswaTanpaKelas || [],
        currentMapel: config.initialMapel || [],
        allMapel: config.allMapel || [],

        addedSiswaIds: [],
        removedSiswaIds: [],

        removedSiswaForDisplay: [], // Untuk menyimpan sementara siswa yang dihapus agar bisa ditampilkan di UI
        removedMapelForDisplay: [], // NEW: Untuk menyimpan sementara mapel yang dihapus agar bisa ditampilkan di UI

        isModalOpen: false,
        searchTerm: "",
        selectedMapelId: "", // Pastikan ini string kosong untuk select option default

        // --- COMPUTED PROPERTIES (GETTERS) --- //
        get availableSiswa() {
            // Filter siswaTanpaKelas yang belum ada di currentSiswa dan belum di-stage untuk penambahan
            const currentSiswaIds = this.currentSiswa.map(s => s.id);
            const stagedAddedSiswaIds = this.addedSiswaIds;

            return this.siswaTanpaKelas.filter((siswa) => {
                const matchesSearch = this.searchTerm === "" ||
                                      siswa.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                                      siswa.identifier.toLowerCase().includes(this.searchTerm.toLowerCase());
                const notInCurrent = !currentSiswaIds.includes(siswa.id);
                const notStagedForAdd = !stagedAddedSiswaIds.includes(siswa.id);
                return matchesSearch && notInCurrent && notStagedForAdd;
            });
        },

        get availableMapel() {
            const currentMapelIds = this.currentMapel.map((m) => m.id);
            // Also filter out mapel that are staged for removal
            const stagedRemovedMapelIds = this.removedMapelForDisplay.map(m => m.id);
            return this.allMapel.filter(
                (mapel) => !currentMapelIds.includes(mapel.id) && !stagedRemovedMapelIds.includes(mapel.id)
            );
        },

        // --- METHODS --- //
        openModal() {
            this.isModalOpen = true;
            // Reset search term and selected options when opening modal
            this.searchTerm = "";
            // If using TomSelect, you might need to clear its value here
            // For standard select, no specific reset needed beyond initial state
        },
        closeModal() {
            this.isModalOpen = false;
            this.searchTerm = "";
        },

        // Siswa Management
        stageForAddition() {
            const selectedOptions = Array.from(this.$refs.addSiswaSelect.selectedOptions);
            const selectedSiswaIds = selectedOptions.map(option => parseInt(option.value));

            selectedSiswaIds.forEach(siswaId => {
                const siswaToAdd = this.siswaTanpaKelas.find(s => s.id === siswaId);
                if (siswaToAdd) {
                    // Add to currentSiswa
                    this.currentSiswa.push(siswaToAdd);
                    // Remove from siswaTanpaKelas
                    this.siswaTanpaKelas = this.siswaTanpaKelas.filter(s => s.id !== siswaId);

                    // Update addedSiswaIds and removedSiswaIds
                    if (this.removedSiswaIds.includes(siswaId)) {
                        this.removedSiswaIds = this.removedSiswaIds.filter(id => id !== siswaId);
                    } else {
                        this.addedSiswaIds.push(siswaId);
                    }

                    // Remove from removedSiswaForDisplay if it was there
                    this.removedSiswaForDisplay = this.removedSiswaForDisplay.filter(s => s.id !== siswaId);
                }
            });

            // Clear selected options in the select element
            this.$refs.addSiswaSelect.value = "";
            this.closeModal();
        },

        stageForRemoval(siswaId) {
            const siswaToRemove = this.currentSiswa.find(s => s.id === siswaId);
            if (siswaToRemove) {
                // Add to removedSiswaForDisplay
                this.removedSiswaForDisplay.push(siswaToRemove);
                // Remove from currentSiswa
                this.currentSiswa = this.currentSiswa.filter(s => s.id !== siswaId);

                // Update addedSiswaIds and removedSiswaIds
                if (this.addedSiswaIds.includes(siswaId)) {
                    this.addedSiswaIds = this.addedSiswaIds.filter(id => id !== siswaId);
                } else {
                    this.removedSiswaIds.push(siswaId);
                }
            }
        },

        undoRemoval(siswaId) {
            const siswaToUndo = this.removedSiswaForDisplay.find(s => s.id === siswaId);
            if (siswaToUndo) {
                // Add back to currentSiswa
                this.currentSiswa.push(siswaToUndo);
                // Remove from removedSiswaForDisplay
                this.removedSiswaForDisplay = this.removedSiswaForDisplay.filter(s => s.id !== siswaId);

                // Update addedSiswaIds and removedSiswaIds
                // If it was originally part of the class, it's not "added"
                // If it was added in this session and then removed, it should be removed from addedSiswaIds
                if (this.removedSiswaIds.includes(siswaId)) {
                    this.removedSiswaIds = this.removedSiswaIds.filter(id => id !== siswaId);
                } else if (this.addedSiswaIds.includes(siswaId)) {
                    // This case means it was added in this session, then removed, then undone.
                    // So it should remain in addedSiswaIds. No change needed here.
                }
            }
        },

        // Mata Pelajaran Management
        stageMapelForAddition() {
            // Get all selected options from the multi-select dropdown
            const selectedOptions = Array.from(this.$refs.addMapelSelect.selectedOptions);
            const selectedMapelIds = selectedOptions.map(option => parseInt(option.value));

            selectedMapelIds.forEach(mapelId => {
                const mapelToAdd = this.allMapel.find(m => m.id === mapelId);
                if (mapelToAdd) {
                    // Add to currentMapel if not already present
                    if (!this.currentMapel.some(m => m.id === mapelId)) {
                        this.currentMapel.push(mapelToAdd);
                    }
                    // Remove from removedMapelForDisplay if it was there
                    this.removedMapelForDisplay = this.removedMapelForDisplay.filter(m => m.id !== mapelId);
                }
            });

            // Clear selected options in the select element
            this.$refs.addMapelSelect.value = "";
        },

        stageMapelForRemoval(mapelId) {
            const mapelToRemove = this.currentMapel.find(m => m.id === mapelId);
            if (mapelToRemove) {
                this.removedMapelForDisplay.push(mapelToRemove);
                this.currentMapel = this.currentMapel.filter(
                    (m) => m.id !== mapelId
                );
            }
        },

        undoMapelRemoval(mapelId) {
            const mapelToUndo = this.removedMapelForDisplay.find(m => m.id === mapelId);
            if (mapelToUndo) {
                this.currentMapel.push(mapelToUndo);
                this.removedMapelForDisplay = this.removedMapelForDisplay.filter(m => m.id !== mapelId);
            }
        },
    };
}