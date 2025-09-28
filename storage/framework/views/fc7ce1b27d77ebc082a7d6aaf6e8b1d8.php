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
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                
                <?php if(isset($kelasFilter)): ?>
                    <?php echo e(__('Daftar Siswa Kelas: ') . $kelasFilter->nama_kelas); ?>

                <?php else: ?>
                    <?php echo e(__('Manajemen User')); ?>

                <?php endif; ?>
            </h2>
            <div class="flex flex-wrap items-center justify-start md:justify-end gap-2">
                
                <a href="<?php echo e(route('users.export', request()->query())); ?>" data-turbo="false" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-blue-700 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Ekspor</a>
                <a href="<?php echo e(route('users.import.form')); ?>" class="px-4 py-2 bg-green-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-green-700 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Impor</a>
                <a href="<?php echo e(route('users.create')); ?>" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-indigo-700 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Tambah User</a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <turbo-frame id="main-content">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100" x-data="userManagement()" x-init="init(<?php echo e(json_encode($users->getCollection()->pluck('id'))); ?>)">
                        
                        <!-- Form Filter & Pencarian -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <form method="GET" action="<?php echo e(route('users.index')); ?>">
                                
                                <?php if(request('kelas_id')): ?>
                                    <input type="hidden" name="kelas_id" value="<?php echo e(request('kelas_id')); ?>">
                                    <input type="hidden" name="role" value="siswa">
                                <?php endif; ?>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    
                                    <div class="md:col-span-2">
                                        <label for="search" class="sr-only">Cari</label>
                                        <input type="text" name="search" id="search" placeholder="Cari berdasarkan Nama, Email, atau ID..." value="<?php echo e(request('search')); ?>"
                                               class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    
                                    <select name="kelas_id" id="kelas_id" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Semua Kelas</option>
                                        <?php $__currentLoopData = $allKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($kelas->id); ?>" <?php if(request('kelas_id') == $kelas->id): echo 'selected'; endif; ?>><?php echo e($kelas->nama_kelas); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <?php if(!request('kelas_id')): ?>
                                        <select name="role" id="role" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Semua Role</option>
                                            <option value="admin" <?php if(request('role') == 'admin'): echo 'selected'; endif; ?>>Admin</option>
                                            <option value="guru" <?php if(request('role') == 'guru'): echo 'selected'; endif; ?>>Guru</option>
                                            <option value="siswa" <?php if(request('role') == 'siswa'): echo 'selected'; endif; ?>>Siswa</option>
                                        </select>
                                        <?php endif; ?>
                                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <?php if(session('success')): ?>
                            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>
                        <?php if(session('error')): ?>
                            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-lg">
                                <?php echo e(session('error')); ?>

                            </div>
                        <?php endif; ?>

                        <!-- Form Aksi Massal -->
                        <form x-ref="bulkActionForm" action="<?php echo e(route('users.bulkToggleStatus')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" x-model="bulkAction">
                            <template x-for="userId in selectedUsers" :key="userId">
                                <input type="hidden" name="user_ids[]" :value="userId">
                            </template>
                        </form>

                        <!-- Panel Aksi Massal -->
                        <div x-show="selectedUsers.length > 0" x-cloak class="bg-gray-100 dark:bg-gray-700/50 p-4 rounded-lg mb-4 flex items-center justify-between">
                            <div>
                                <span x-text="selectedUsers.length"></span> pengguna terpilih.
                            </div>
                            <div class="flex items-center space-x-3">
                                <select x-model="bulkAction" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Pilih Aksi</option>
                                    <option value="activate">Aktifkan yang Dipilih</option>
                                    <option value="deactivate">Nonaktifkan yang Dipilih</option>
                                    <option value="delete">Hapus yang Dipilih</option>
                                </select>
                                <button @click="submitBulkAction" :disabled="!bulkAction" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terapkan</button>
                            </div>
                        </div>

                        <!-- Tabel User -->
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-sm text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400 hidden sm:table-header-group">
                                    <tr>
                                        <th scope="col" class="p-4">
                                            <div class="flex items-center">
                                                <input id="checkbox-all-search" type="checkbox" @click="toggleSelectAll($event)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3">Pengguna</th>
                                        <th scope="col" class="px-6 py-3">ID (NIS/NIP)</th>
                                        <th scope="col" class="px-6 py-3">Role</th>
                                        <th scope="col" class="px-6 py-3">Status Aktif</th>
                                        <th scope="col" class="px-6 py-3">Login Terakhir</th>
                                        <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="block sm:table-row-group">
                                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 block sm:table-row mb-4 sm:mb-0 rounded-lg shadow-md sm:shadow-none transition duration-150 ease-in-out">
                                        <td class="w-4 p-4">
                                            <?php if(in_array($user->role, ['guru', 'siswa'])): ?>
                                            <div class="flex items-center">
                                                <input type="checkbox" :value="<?php echo e($user->id); ?>" x-model="selectedUsers" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white block sm:table-cell">
                                            <div class="flex items-center">
                                                <img class="w-10 h-10 rounded-full object-cover mr-4" src="<?php echo e($user->foto_url); ?>" alt="<?php echo e($user->name); ?>">
                                                <div>
                                                    <div class="text-base font-semibold"><a href="<?php echo e(route('users.show', $user->id)); ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline"><?php echo e($user->name); ?></a></div>
                                                    <div class="font-normal text-gray-500"><?php echo e($user->email); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">ID (NIS/NIP): </span><?php echo e($user->identifier); ?>

                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">Role: </span>
                                            <?php
                                                $roleColors = [
                                                    'admin' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300',
                                                    'guru'  => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                                    'siswa' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                                ];
                                                $roleClasses = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($roleClasses); ?>">
                                                <?php echo e(ucfirst($user->role)); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">Status Aktif: </span>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'); ?>">
                                                <?php echo e($user->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 block sm:table-cell">
                                            <span class="font-bold sm:hidden block mb-1">Login Terakhir: </span><?php echo e($user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum pernah login'); ?>

                                        </td>
                                        <td class="px-6 py-4 text-right block sm:table-cell">
                                            <div class="relative flex items-center justify-end sm:justify-end space-x-2">
                                                <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '32','contentClasses' => 'py-1 bg-white dark:bg-gray-700 rounded-md shadow-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '32','contentClasses' => 'py-1 bg-white dark:bg-gray-700 rounded-md shadow-lg']); ?>
                                                     <?php $__env->slot('trigger', null, []); ?> 
                                                        <button class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                            Aksi <svg class="ms-1 fill-current h-4 w-4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                        </button>
                                                     <?php $__env->endSlot(); ?>
                                                     <?php $__env->slot('content', null, []); ?> 
                                                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('users.edit', $user->id),'class' => 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('users.edit', $user->id)),'class' => 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600']); ?>
                                                            <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z" /></svg> Edit
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                                                        <?php if($user->role === 'siswa'): ?>
                                                            <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('users.cetak-satu-kartu', $user->id),'target' => '_blank','class' => 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('users.cetak-satu-kartu', $user->id)),'target' => '_blank','class' => 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600']); ?>
                                                            <svg class="mr-3 h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l-3-3m0 0l3-3m-3 3h6" /></svg> Cetak Kartu
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                                                        <?php endif; ?>
                                                        
                                                        
                                                        <?php if(in_array($user->role, ['guru', 'siswa'])): ?>
                                                        <button type="button" x-on:click="$dispatch('open-modal', 'confirm-toggle-status-<?php echo e($user->id); ?>')" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                                            <?php if($user->is_active): ?>
                                                        <svg class="mr-3 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2A9 9 0 111 10a9 9 0 0118 0z" />
                                                        </svg> Nonaktifkan
                                                        <?php else: ?>
                                                        <svg class="mr-3 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg> Aktifkan
                                                        <?php endif; ?>
                                                        </button>
                                                        <?php endif; ?>

                                                        
                                                        <button x-on:click="$dispatch('open-modal', 'confirm-user-deletion-<?php echo e($user->id); ?>')" class="block w-full px-4 py-2 text-start text-sm leading-5 text-red-700 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/50 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                                            <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg> Hapus
                                                        </button>
                                                     <?php $__env->endSlot(); ?>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
                                            </div>

                                            
                                            <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'confirm-toggle-status-'.e($user->id).'','show' => $errors->any(),'focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'confirm-toggle-status-'.e($user->id).'','show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->any()),'focusable' => true]); ?>
                                                <form method="post" action="<?php echo e(route('users.toggleStatus', $user->id)); ?>" class="p-6" x-on:submit="$dispatch('close')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('patch'); ?>
                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                        Konfirmasi Perubahan Status
                                                    </h2>
                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                        Apakah Anda yakin ingin <?php echo e($user->is_active ? 'menonaktifkan' : 'mengaktifkan'); ?> akun <span class="font-bold"><?php echo e($user->name); ?></span>?
                                                    </p>
                                                    <div class="mt-6 flex justify-end">
                                                        <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['xOn:click' => '$dispatch(\'close\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => '$dispatch(\'close\')']); ?>
                                                            Batal
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                                                        <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['class' => 'ms-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ms-3']); ?>
                                                            Ya, Lanjutkan
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                                                    </div>
                                                </form>
                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

                                            <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'confirm-user-deletion-'.e($user->id).'','show' => $errors->any(),'focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'confirm-user-deletion-'.e($user->id).'','show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->any()),'focusable' => true]); ?>
                                                <form method="post" action="<?php echo e(route('users.destroy', $user->id)); ?>" class="p-6" x-on:submit="$dispatch('close')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('delete'); ?>
                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                        Konfirmasi Hapus Pengguna
                                                    </h2>
                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                        Apakah Anda yakin ingin menghapus akun <span class="font-bold"><?php echo e($user->name); ?></span>? Tindakan ini tidak dapat dibatalkan.
                                                    </p>
                                                    <div class="mt-6 flex justify-end">
                                                        <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['xOn:click' => '$dispatch(\'close\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => '$dispatch(\'close\')']); ?>
                                                            Batal
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                                                        <?php if (isset($component)) { $__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.danger-button','data' => ['class' => 'ms-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('danger-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ms-3']); ?>
                                                            Yakin, Hapus
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11)): ?>
<?php $attributes = $__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11; ?>
<?php unset($__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11)): ?>
<?php $component = $__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11; ?>
<?php unset($__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11); ?>
<?php endif; ?>
                                                    </div>
                                                </form>
                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 block sm:table-row">
                                        <td colspan="7" class="px-6 py-4 text-center block sm:table-cell">Tidak ada data user yang cocok.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            <?php echo e($users->links()); ?>

                        </div>
                    </div>

                    <script>
                        function userManagement() {
                            return {
                                selectedUsers: [],
                                bulkAction: '',
                                allUserIds: [],
                                init(ids) {
                                    this.allUserIds = ids;
                                },
                                toggleSelectAll(event) {
                                    if (event.target.checked) {
                                        this.selectedUsers = [...this.allUserIds];
                                    } else {
                                        this.selectedUsers = [];
                                    }
                                },
                                submitBulkAction() {
                                    if (this.bulkAction && this.selectedUsers.length > 0) {
                                        const form = this.$refs.bulkActionForm;
                                        if (this.bulkAction === 'delete') {
                                            if (!confirm('Apakah Anda yakin ingin menghapus pengguna yang dipilih? Tindakan ini tidak dapat dibatalkan.')) {
                                                return; // User cancelled
                                            }
                                            form.action = '<?php echo e(route("users.bulkDestroy")); ?>';
                                            form.method = 'POST'; // Laravel uses POST for DELETE via _method field
                                            // Add a hidden _method field for DELETE
                                            let methodField = form.querySelector('input[name="_method"]');
                                            if (!methodField) {
                                                methodField = document.createElement('input');
                                                methodField.type = 'hidden';
                                                methodField.name = '_method';
                                                form.appendChild(methodField);
                                            }
                                            methodField.value = 'DELETE';
                                        } else {
                                            form.action = '<?php echo e(route("users.bulkToggleStatus")); ?>';
                                            form.method = 'POST';
                                            // Remove _method field if it exists
                                            let methodField = form.querySelector('input[name="_method"]');
                                            if (methodField) {
                                                form.removeChild(methodField);
                                            }
                                        }
                                        form.submit();
                                    }
                                }
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </turbo-frame>
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
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/users/index.blade.php ENDPATH**/ ?>