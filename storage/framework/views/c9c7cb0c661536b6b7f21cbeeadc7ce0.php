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
            <?php echo e(__('Cetak Kartu Absensi')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div id="printCardsContainer" class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 xl:px-12">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Form Filter dan Pencarian -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow-sm" x-data="tomSelectManager">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Filter dan Pilih Pengguna</h3>
                        <div>
                            <form action="<?php echo e(route('print-cards.index')); ?>" method="GET" id="filterForm">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-end mb-4">
                                    <div>
                                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Role:</label>
                                        <select name="role" id="role" class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="">Semua Role</option>
                                            <option value="guru" <?php echo e(request('role') == 'guru' ? 'selected' : ''); ?>>Guru</option>
                                            <option value="tu" <?php echo e(request('role') == 'tu' ? 'selected' : ''); ?>>Tata Usaha</option>
                                            <option value="other" <?php echo e(request('role') == 'other' ? 'selected' : ''); ?>>Lainnya</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Nama/Email/ID:</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            </div>
                                            <input type="text" name="search" id="search" class="block w-full pl-10 pr-3 py-2 rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Cari..." value="<?php echo e(request('search')); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end mb-4">
                                    <div class="md:col-span-1">
                                        <label for="config_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Konfigurasi Kartu:</label>
                                        <select name="config_id" id="config_id" class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <?php $__empty_1 = true; $__currentLoopData = $configs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <option value="<?php echo e($config->id); ?>" <?php echo e($selectedConfigId == $config->id ? 'selected' : ''); ?>><?php echo e($config->name); ?> <?php echo e($config->is_default ? '(Default)' : ''); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <option value="">Tidak ada konfigurasi tersedia</option>
                                            <?php endif; ?>
                                        </select>
                                        <?php if($configs->isEmpty()): ?>
                                            <p class="mt-2 text-red-500 text-sm dark:text-red-400">Belum ada konfigurasi kartu. Silakan buat di <a href="<?php echo e(route('absensi.cards.customize')); ?>" class="text-blue-500 dark:text-blue-400 hover:underline">halaman kustomisasi</a>.</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex justify-end gap-2 md:col-span-1">
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terapkan Filter</button>
                                        <a href="<?php echo e(route('print-cards.index')); ?>" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Reset Filter</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Pengguna yang Dapat Dipilih -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Daftar Pengguna</h3>
                                <button id="printSelectedBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md shadow-sm" disabled>Cetak Kartu Terpilih (<span id="selectedCount">0</span>)</button>
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-gray-900 dark:text-gray-100" id="usersTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="dark:bg-gray-700 dark:text-gray-200 p-2"><input type="checkbox" id="selectAll"></th>
                                            <th class="dark:bg-gray-700 dark:text-gray-200 p-2">Nama</th>
                                            <th class="dark:bg-gray-700 dark:text-gray-200 p-2">Role</th>
                                            <th class="dark:bg-gray-700 dark:text-gray-200 p-2">Identifier</th>
                                            <th class="dark:bg-gray-700 dark:text-gray-200 p-2">Email</th>
                                            <th class="dark:bg-gray-700 dark:text-gray-200 p-2">Status</th>
                                            <th class="dark:bg-gray-700 dark:text-gray-200 p-2">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="dark:bg-gray-800 dark:hover:bg-gray-600">
                                            <td class="p-2"><input type="checkbox" class="user-checkbox" value="<?php echo e($user->id); ?>"></td>
                                            <td class="p-2"><?php echo e($user->name); ?></td>
                                            <td class="p-2">
                                                <?php echo e(ucfirst($user->role)); ?>

                                                <?php if($user->role == 'other' && $user->custom_role): ?>
                                                    (<span class="text-purple-600 dark:text-purple-300"><?php echo e($user->custom_role); ?></span>)
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-2"><?php echo e($user->identifier); ?></td>
                                            <td class="p-2"><?php echo e($user->email); ?></td>
                                            <td class="p-2">
                                                <span class="badge badge-<?php echo e($user->is_active ? 'success' : 'danger'); ?> px-2 py-1 rounded-full text-xs <?php echo e($user->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white'); ?>">
                                                    <?php echo e($user->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                                </span>
                                            </td>
                                            <td class="p-2">
                                                <a href="javascript:void(0)" class="btn btn-sm btn-info print-single-card bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-2 rounded text-xs" data-user-id="<?php echo e($user->id); ?>" title="Cetak Kartu Individual">
                                                    <i class="fas fa-print"></i> Cetak
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-gray-700 dark:text-gray-300 p-4">Tidak ada pengguna yang ditemukan.</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-4">
                                <?php echo e($users->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="printForm" action="<?php echo e(route('print-cards.generate')); ?>" method="POST" target="_blank">
        <?php echo csrf_field(); ?>
         <!-- Dihapus karena dibuat dinamis -->
        <input type="hidden" name="config_id" id="hiddenConfigId">
    </form>
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

<script defer>
    function initializePrintCardListeners() {
        // Hanya jalankan script jika elemen utama halaman print-cards ada
        const printCardsContainer = document.getElementById('printCardsContainer'); // Asumsi ada div wrapper dengan ID ini di body halaman
        if (!printCardsContainer) {
            console.log('Not on print-cards page, skipping initialization.');
            return;
        }

        console.log('Script for print-cards started/re-initialized.'); // Global debug log

        const selectAllCheckbox = document.getElementById('selectAll');
        let userCheckboxes = document.querySelectorAll('.user-checkbox');
        const printSelectedBtn = document.getElementById('printSelectedBtn');
        const selectedCountSpan = document.getElementById('selectedCount');
        const hiddenConfigIdInput = document.getElementById('hiddenConfigId');
        const printForm = document.getElementById('printForm');
        const configSelect = document.getElementById('config_id');
        // const roleSelect = document.getElementById('role'); // Tidak digunakan langsung, diabaikan untuk konsistensi

        // Pastikan elemen-elemen penting ada sebelum melanjutkan
        if (!selectAllCheckbox || !printSelectedBtn || !selectedCountSpan || !hiddenConfigIdInput || !printForm || !configSelect) {
            console.error('One or more essential elements for print-cards script not found.');
            return;
        }

        function updateSelectedCount() {
            userCheckboxes = document.querySelectorAll('.user-checkbox'); // Perbarui nodeList
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            console.log('updateSelectedCount called. Checked count:', checkedCount); // Debugging
            selectedCountSpan.textContent = checkedCount;
            printSelectedBtn.disabled = checkedCount === 0;
            if (userCheckboxes.length > 0) {
                selectAllCheckbox.checked = checkedCount === userCheckboxes.length;
            } else {
                selectAllCheckbox.checked = false;
            }
        }

        function attachCheckboxListeners() {
            // Hapus listener yang mungkin sudah ada untuk mencegah duplikasi
            userCheckboxes.forEach(checkbox => {
                checkbox.removeEventListener('change', updateSelectedCount);
            });
            
            userCheckboxes = document.querySelectorAll('.user-checkbox'); // Perbarui nodeList
            userCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
            console.log('Listeners re-attached to', userCheckboxes.length, 'checkboxes'); // Debugging
        }

        // Attach listener for selectAllCheckbox
        selectAllCheckbox.removeEventListener('change', handleSelectAllChange); // Hapus listener sebelumnya jika ada
        selectAllCheckbox.addEventListener('change', handleSelectAllChange);

        function handleSelectAllChange() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        }

        // Initial attachment of listeners for individual checkboxes
        attachCheckboxListeners();

        // Event listener for "Cetak Kartu Terpilih" button
        printSelectedBtn.removeEventListener('click', handlePrintSelectedClick); // Hapus listener sebelumnya jika ada
        printSelectedBtn.addEventListener('click', handlePrintSelectedClick);

        function handlePrintSelectedClick() {
            console.log('Print Selected button clicked.'); // Debugging
            const selectedUserIds = Array.from(document.querySelectorAll('.user-checkbox:checked'))
                                       .map(checkbox => checkbox.value);
            
            if (selectedUserIds.length > 0) {
                // Hapus input tersembunyi sebelumnya dan tambahkan yang baru
                document.querySelectorAll('#printForm input[name="user_ids[]"]').forEach(input => input.remove());

                selectedUserIds.forEach(userId => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = userId;
                    printForm.appendChild(input);
                });
                
                hiddenConfigIdInput.value = configSelect.value;
                printForm.submit();
                console.log('Print form submitted with user IDs:', selectedUserIds); // Debugging
            } else {
                alert('Pilih setidaknya satu pengguna untuk dicetak.');
                console.log('No users selected for bulk print.'); // Debugging
            }
        }

        // Event listener for individual "Cetak" buttons
        document.querySelectorAll('.print-single-card').forEach(button => {
            button.removeEventListener('click', handlePrintSingleClick); // Hapus listener sebelumnya jika ada
            button.addEventListener('click', handlePrintSingleClick);
        });

        function handlePrintSingleClick(event) {
            event.preventDefault(); // Prevent default if it's a link
            console.log('Individual Print button clicked.'); // Debugging
            const userId = this.dataset.userId;
            // Hapus input tersembunyi sebelumnya dan tambahkan yang baru
            document.querySelectorAll('#printForm input[name="user_ids[]"]').forEach(input => input.remove());
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_ids[]';
            input.value = userId;
            printForm.appendChild(input);

            hiddenConfigIdInput.value = configSelect.value;
            printForm.submit();
            console.log('Single print form submitted for user ID:', userId); // Debugging
        }

        updateSelectedCount(); // Initial count update after DOM is loaded and listeners attached
        console.log('Script for print-cards finished initialization.'); // Debugging
    }

    // Panggil fungsi saat DOMContentLoaded (untuk pemuatan awal)
    document.addEventListener('DOMContentLoaded', initializePrintCardListeners);

    // Panggil fungsi saat Turbo.js memuat halaman baru
    document.addEventListener('turbo:load', initializePrintCardListeners);
</script>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/admin/print_cards/index.blade.php ENDPATH**/ ?>