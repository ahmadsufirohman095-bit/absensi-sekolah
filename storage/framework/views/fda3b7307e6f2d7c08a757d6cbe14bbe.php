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
            <?php echo e(__('Kelola Kelas: ')); ?> <span class="text-indigo-500"><?php echo e($kela->nama_kelas); ?></span>
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="kelasPage({
                initialSiswa: <?php echo e($siswaDiKelas->map->only(['id', 'name', 'identifier', 'foto_url'])->toJson()); ?>,
                siswaTanpaKelas: <?php echo e($siswaTanpaKelas->map->only(['id', 'name', 'identifier', 'foto_url'])->toJson()); ?>

            })" class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">

                
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button @click="tab = 'detail'"
                                :class="{ 'border-indigo-500 text-indigo-600': tab === 'detail', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'detail' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Detail & Siswa
                        </button>
                        
                    </nav>
                </div>

                
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div x-show="tab === 'detail'" x-cloak>
                        <form id="update-kelas-form" method="POST" action="<?php echo e(route('kelas.update', $kela)); ?>">
                            <?php echo method_field('PUT'); ?>
                            <?php echo csrf_field(); ?>

                            
                            <input type="hidden" name="add_siswa_ids" :value="JSON.stringify(addedSiswaIds)">
                            <input type="hidden" name="remove_siswa_ids" :value="JSON.stringify(removedSiswaIds)">

                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'nama_kelas','value' => __('Nama Kelas')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'nama_kelas','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Nama Kelas'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                    <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => 'nama_kelas','class' => 'block mt-1 w-full','type' => 'text','name' => 'nama_kelas','value' => old('nama_kelas', $kela->nama_kelas ?? ''),'required' => true,'autofocus' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'nama_kelas','class' => 'block mt-1 w-full','type' => 'text','name' => 'nama_kelas','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('nama_kelas', $kela->nama_kelas ?? '')),'required' => true,'autofocus' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('nama_kelas'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('nama_kelas')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                </div>
                                <div>
                                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'wali_kelas_id','value' => __('Wali Kelas')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'wali_kelas_id','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Wali Kelas'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                    <select name="wali_kelas_id" id="wali_kelas_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">-- Tidak ada --</option>
                                        <?php $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($guru->id); ?>" <?php if(old('wali_kelas_id', $kela->wali_kelas_id ?? '') == $guru->id): echo 'selected'; endif; ?>>
                                                <?php echo e($guru->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('wali_kelas_id'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('wali_kelas_id')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                </div>
                            </div>

                            
                            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                        Manajemen Siswa (<span x-text="filteredCurrentSiswa.length"></span> dari <span x-text="currentSiswa.length"></span>)
                                    </h3>
                                    <button @click="isModalOpen = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Tambah Siswa</span>
                                    </button>
                                </div>
                                <div class="mb-4">
                                    <input type="text" x-model="siswaSearchTerm" placeholder="Cari siswa di kelas ini..."
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                </div>
                                <div class="max-h-96 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 space-y-2">
                                    <p x-show="currentSiswa.length === 0 && removedSiswaForDisplay.length === 0" class="text-gray-500 dark:text-gray-400 text-sm text-center p-4">
                                        Belum ada siswa di kelas ini.
                                    </p>
                                    <p x-show="currentSiswa.length > 0 && filteredCurrentSiswa.length === 0" class="text-gray-500 dark:text-gray-400 text-sm text-center p-4">
                                        Tidak ada siswa yang cocok dengan pencarian.
                                    </p>
                                    <template x-for="siswa in filteredCurrentSiswa" :key="siswa.id">
                                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md transition-all">
                                            <div class="flex items-center">
                                                <img class="h-8 w-8 rounded-full object-cover mr-3" :src="siswa.foto_url ?? '/images/default-avatar.png'" :alt="siswa.name ?? 'N/A'">
                                                <div>
                                                    <span x-text="siswa.name ?? 'N/A'" class="font-medium"></span>
                                                    <span x-text="siswa.identifier ? '(' + siswa.identifier + ')' : ''" class="text-sm text-gray-600 dark:text-gray-400"></span>
                                                </div>
                                            </div>
                                            <button @click="stageForRemoval(siswa.id)" type="button" class="text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400">
                                                Keluarkan
                                            </button>
                                        </div>
                                    </template>
                                    <template x-for="siswa in removedSiswaForDisplay" :key="siswa.id">
                                        <div class="flex items-center justify-between p-2 bg-red-100 dark:bg-red-900/30 rounded-md opacity-60 transition-all">
                                            <div class="flex items-center">
                                                <img class="h-8 w-8 rounded-full object-cover mr-3" :src="siswa.foto_url ?? '/images/default-avatar.png'" :alt="siswa.name ?? 'N/A'">
                                                <div>
                                                    <span x-text="siswa.name ?? 'N/A'" class="font-medium line-through"></span>
                                                    <span x-text="siswa.identifier ? '(' + siswa.identifier + ')' : ''" class="text-sm text-gray-600 dark:text-gray-400 line-through"></span>
                                                </div>
                                            </div>
                                            <button @click="undoRemoval(siswa.id)" type="button" class="text-xs font-semibold text-gray-700 hover:text-black dark:text-gray-300 dark:hover:text-white">
                                                Urungkan
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            
                            <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <a href="<?php echo e(route('kelas.index')); ?>" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                                <button type="submit" form="update-kelas-form" class="ms-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:focus:bg-gray-700 active:bg-gray-900 dark:active:bg-gray-300">Update Kelas</button>
                            </div>
                        </form>
                    </div>

                    
                </div>

                
                <div x-show="isModalOpen" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div @click.away="isModalOpen = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl transform transition-all" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tambahkan Siswa ke Kelas</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Pilih siswa yang belum terdaftar di kelas manapun.</p>
                                </div>
                                <button @click="isModalOpen = false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-b border-gray-200 dark:border-gray-700 p-6">
                            <div x-show="availableSiswa.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <div>
                                    <label for="add_siswa_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari Siswa Tersedia</label>
                                    <div class="tom-select-wrapper">
                                        <select id="add_siswa_select" multiple x-ref="addSiswaSelect" placeholder="Ketik nama atau NIS siswa..."></select>
                                    </div>
                                </div>
                                
                                <div>
                                    <h5 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Akan ditambahkan (<span x-text="stagedSiswa.length"></span>):</h5>
                                    <div class="h-64 overflow-y-auto space-y-2 p-2 border rounded-md bg-gray-50 dark:bg-gray-900/50 dark:border-gray-700">
                                        <template x-if="stagedSiswa.length === 0">
                                            <div class="flex items-center justify-center h-full">
                                                <p class="text-center text-sm text-gray-500 dark:text-gray-400">Siswa yang dipilih akan muncul di sini.</p>
                                            </div>
                                        </template>
                                        <template x-for="siswa in stagedSiswa" :key="siswa.id">
                                            <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded-md shadow-sm animate-fade-in">
                                                <div class="flex items-center overflow-hidden">
                                                    <img class="h-8 w-8 rounded-full object-cover mr-3 flex-shrink-0" :src="siswa.foto_url ?? '/images/default-avatar.png'" :alt="siswa.name">
                                                    <div class="overflow-hidden">
                                                        <p x-text="siswa.name" class="font-medium text-gray-900 dark:text-gray-100 truncate"></p>
                                                        <p x-text="'(' + siswa.identifier + ')'" class="text-sm text-gray-600 dark:text-gray-400 truncate"></p>
                                                    </div>
                                                </div>
                                                <button @click="removeSelection(siswa.id)" type="button" class="ml-2 text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400 flex-shrink-0">
                                                    Hapus
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div x-show="availableSiswa.length === 0" class="text-center p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Luar Biasa!</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Semua siswa sudah terdaftar di dalam sebuah kelas.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4 p-6 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                            <div class="flex-1 text-sm text-gray-600 dark:text-gray-400">
                                <span x-show="selectedSiswaIdsToAdd.length > 0">
                                    <span x-text="selectedSiswaIdsToAdd.length"></span> siswa dipilih
                                </span>
                            </div>
                            <button @click="isModalOpen = false" type="button" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                Batal
                            </button>
                            <button @click="stageForAddition()" type="button" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:bg-indigo-400 disabled:cursor-not-allowed"
                                    :disabled="selectedSiswaIdsToAdd.length === 0">
                                Tambahkan Siswa Terpilih
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
    <script>
        function kelasPage(config) {
            return {
                // General State
                tab: new URLSearchParams(window.location.search).get('tab') || 'detail',

                // Siswa Management State
                isModalOpen: false,
                currentSiswa: config.initialSiswa || [],
                availableSiswa: config.siswaTanpaKelas || [],
                addedSiswaIds: [],
                removedSiswaIds: [],
                removedSiswaForDisplay: [],
                addSiswaTomSelect: null,
                selectedSiswaIdsToAdd: [],
                stagedSiswa: [], // Explicit array for selected students
                siswaSearchTerm: '',

                // Computed property for filtered current siswa
                get filteredCurrentSiswa() {
                    if (!this.siswaSearchTerm) {
                        return this.currentSiswa;
                    }
                    const searchTerm = this.siswaSearchTerm.toLowerCase();
                    return this.currentSiswa.filter(siswa =>
                        (siswa.name && siswa.name.toLowerCase().includes(searchTerm)) ||
                        (siswa.identifier && siswa.identifier.toLowerCase().includes(searchTerm))
                    );
                },

                // Methods for Siswa Management
                initSiswaSelect() {
                    this.$nextTick(() => {
                        if (this.$refs.addSiswaSelect) {
                            this.addSiswaTomSelect = new TomSelect(this.$refs.addSiswaSelect, {
                                plugins: ['remove_button'],
                                valueField: 'id',
                                labelField: 'name',
                                searchField: ['name', 'identifier'],
                                create: false,
                                options: this.availableSiswa.map(s => ({id: s.id, name: s.name, identifier: s.identifier, foto_url: s.foto_url})),
                                render: {
                                    option: (data, escape) => {
                                        return `<div class="flex items-center p-2">
                                                    <img class="h-7 w-7 rounded-full object-cover mr-3" src="${escape(data.foto_url ?? '/images/default-avatar.png')}" alt="${escape(data.name)}">
                                                    <div>
                                                        <div class="font-medium">${escape(data.name)}</div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">${escape(data.identifier)}</div>
                                                    </div>
                                                </div>`;
                                    },
                                    item: (item, escape) => {
                                        return `<div class="inline-flex items-center">${escape(item.name)}</div>`;
                                    }
                                }
                            });
                            this.addSiswaTomSelect.on('change', (values) => {
                                this.selectedSiswaIdsToAdd = values;
                                // Rebuild the stagedSiswa array explicitly
                                this.stagedSiswa = this.availableSiswa.filter(s => values.includes(s.id.toString()));
                            });
                        }
                    });
                },
                removeSelection(id) {
                    const idString = id.toString();

                    // Manually update the array that controls the button's state.
                    this.selectedSiswaIdsToAdd = this.selectedSiswaIdsToAdd.filter(sid => sid !== idString);

                    // Manually update the array for the "Akan ditambahkan" list.
                    this.stagedSiswa = this.stagedSiswa.filter(s => s.id.toString() !== idString);

                    // Silently update the TomSelect UI without triggering the 'change' event,
                    // to prevent conflicts with the manual state updates above.
                    if (this.addSiswaTomSelect) {
                        this.addSiswaTomSelect.removeItem(id, true); // silent removal
                    }
                },
                stageForRemoval(id) {
                    const siswa = this.currentSiswa.find(s => s.id === id);
                    if (siswa) {
                        this.removedSiswaForDisplay.push(siswa);
                        this.currentSiswa = this.currentSiswa.filter(s => s.id !== id);
                        if (!this.removedSiswaIds.includes(id)) {
                            this.removedSiswaIds.push(id);
                        }
                        const indexInAdded = this.addedSiswaIds.indexOf(id);
                        if (indexInAdded > -1) {
                            this.addedSiswaIds.splice(indexInAdded, 1);
                        }
                    }
                },
                undoRemoval(id) {
                    const siswa = this.removedSiswaForDisplay.find(s => s.id === id);
                    if (siswa) {
                        this.currentSiswa.push(siswa);
                        this.removedSiswaForDisplay = this.removedSiswaForDisplay.filter(s => s.id !== id);
                        const indexInRemoved = this.removedSiswaIds.indexOf(id);
                        if (indexInRemoved > -1) {
                            this.removedSiswaIds.splice(indexInRemoved, 1);
                        }
                    }
                },
                stageForAddition() {
                    if (!this.addSiswaTomSelect) return;

                    const addedIds = this.stagedSiswa.map(s => s.id.toString());

                    this.stagedSiswa.forEach(siswa => {
                        if (siswa && !this.currentSiswa.some(s => s.id == siswa.id)) {
                            this.currentSiswa.push(siswa);
                            if (!this.addedSiswaIds.includes(siswa.id.toString())) {
                                this.addedSiswaIds.push(siswa.id.toString());
                            }
                            const indexInRemoved = this.removedSiswaIds.indexOf(siswa.id);
                            if (indexInRemoved > -1) {
                                this.removedSiswaIds.splice(indexInRemoved, 1);
                            }
                        }
                    });

                    // Remove added students from the available list to prevent re-selection
                    this.availableSiswa = this.availableSiswa.filter(s => !addedIds.includes(s.id.toString()));
                    addedIds.forEach(id => {
                        this.addSiswaTomSelect.removeOption(id);
                    });

                    this.addSiswaTomSelect.clear();
                    this.selectedSiswaIdsToAdd = [];
                    this.stagedSiswa = [];
                    this.isModalOpen = false;
                },

                // Init method
                init() {
                    this.$watch('isModalOpen', isOpen => {
                        if (isOpen && this.addSiswaTomSelect === null) {
                            this.initSiswaSelect();
                        }
                    });
                }
            };
        }
    </script>
    <style>
        .ts-control input::placeholder,
        .ts-control input {
            color: #111827; /* Tailwind `text-gray-900` */
        }
        .dark .ts-control input::placeholder,
        .dark .ts-control input {
            color: #f3f4f6; /* Tailwind `text-gray-100` */
        }
        .ts-control .item,
        .ts-dropdown .option {
            color: #111827;
        }
        .dark .ts-control {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
        }
        .dark .ts-dropdown {
            background-color: #1f2937;
            border-color: #4b5563;
        }
        .dark .ts-control .item,
        .dark .ts-dropdown .option {
            color: #f3f4f6;
        }
        .dark .ts-dropdown .option.active {
            background-color: #4f46e5;
            color: #ffffff;
        }
        .dark .ts-dropdown .optgroup-header {
            color: #9ca3af;
        }
        @keyframes fade-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out forwards;
        }
    </style>
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
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/kelas/edit.blade.php ENDPATH**/ ?>