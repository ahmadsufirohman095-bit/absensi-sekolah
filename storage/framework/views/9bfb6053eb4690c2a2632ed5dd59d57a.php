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
            <?php echo e(__('Kustomisasi Kartu Absensi')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12" x-data="cardCustomizer()" x-init="init()" x-cloak>
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            <div x-show="isLoading" class="flex justify-center items-center p-8">
                <div class="w-16 h-16 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
            </div>

            <div x-show="!isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tema Tersimpan</h3>
                        <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['@click' => 'createNew()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'createNew()']); ?>Buat Baru <?php echo $__env->renderComponent(); ?>
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
                    <div class="space-y-2">
                        <template x-for="config in configs" :key="config.id">
                            <div @click="selectConfig(config)" class="p-3 rounded-lg cursor-pointer flex justify-between items-center transition-all duration-200" :class="{'bg-blue-100 dark:bg-blue-900 ring-2 ring-blue-500': selectedConfig && selectedConfig.id === config.id, 'hover:bg-gray-100 dark:hover:bg-gray-700': !selectedConfig || selectedConfig.id !== config.id}">
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-200" x-text="config.name"></p>
                                    <span x-show="config.is_default" class="text-xs bg-green-200 text-green-800 font-bold px-2 py-1 rounded-full">Default</span>
                                </div>
                                <button @click.stop="deleteConfig(config.id)" class="text-gray-400 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </div>
                        </template>
                    </div>
                </div>

                <template x-if="selectedConfig">
                    <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6">
                        <div x-show="selectedConfig">
                            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Edit Tema: <span x-text="selectedConfig.name" class="font-bold"></span></h3>
                            <form @submit.prevent="saveChanges()" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'name','value' => __('Nama Tema')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Nama Tema'))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => 'name','class' => 'block mt-1 w-full','type' => 'text','xModel' => 'selectedConfig.name','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'name','class' => 'block mt-1 w-full','type' => 'text','x-model' => 'selectedConfig.name','required' => true]); ?>
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
                                    </div>
                                    <div class="flex items-end">
                                        <label class="inline-flex items-center"><input type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500" x-model="selectedConfig.is_default"><span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Jadikan Tema Default</span></label>
                                    </div>
                                </div>

                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Pengaturan Kode QR</h4>
                                    <div class="mt-4">
                                        <label for="qr_size" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                            Ukuran Kode QR: <span x-text="selectedConfig.config_json.qr_size"></span>px
                                        </label>
                                        <input type="range" id="qr_size" min="30" max="200" step="10" x-model.number="selectedConfig.config_json.qr_size" class="w-full">
                                    </div>
                                </div>

                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Pengaturan Warna</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div><?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'bg_color','value' => __('Latar Kartu')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'bg_color','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Latar Kartu'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?><input type="color" id="bg_color" x-model="selectedConfig.config_json.theme.background_color" class="w-full h-10 p-1 bg-white dark:bg-gray-800 border rounded-lg cursor-pointer"></div>
                                        <div><?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'header_bg_color','value' => __('Latar Header')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'header_bg_color','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Latar Header'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?><input type="color" id="header_bg_color" x-model="selectedConfig.config_json.theme.header_background_color" class="w-full h-10 p-1 bg-white dark:bg-gray-800 border rounded-lg cursor-pointer"></div>
                                        <div><?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'text_header','value' => __('Teks Header')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'text_header','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Teks Header'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?><input type="color" id="text_header" x-model="selectedConfig.config_json.theme.text_color_header" class="w-full h-10 p-1 bg-white dark:bg-gray-800 border rounded-lg cursor-pointer"></div>
                                        <div><?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'text_body','value' => __('Teks Body')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'text_body','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Teks Body'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?><input type="color" id="text_body" x-model="selectedConfig.config_json.theme.text_color_body" class="w-full h-10 p-1 bg-white dark:bg-gray-800 border rounded-lg cursor-pointer"></div>
                                    </div>
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'bg_opacity']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'bg_opacity']); ?>Transparansi Latar Kartu (<span x-text="selectedConfig.config_json.theme.background_opacity"></span>) <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                            <input type="range" id="bg_opacity" min="0" max="1" step="0.05" x-model.number="selectedConfig.config_json.theme.background_opacity" class="w-full">
                                        </div>
                                        <div>
                                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'header_opacity']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'header_opacity']); ?>Transparansi Latar Header (<span x-text="selectedConfig.config_json.theme.header_background_opacity"></span>) <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                            <input type="range" id="header_opacity" min="0" max="1" step="0.05" x-model.number="selectedConfig.config_json.theme.header_background_opacity" class="w-full">
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Pengaturan Header</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'header_title','value' => __('Judul Header')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'header_title','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Judul Header'))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => 'header_title','class' => 'block mt-1 w-full','type' => 'text','xModel' => 'selectedConfig.config_json.header_title']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'header_title','class' => 'block mt-1 w-full','type' => 'text','x-model' => 'selectedConfig.config_json.header_title']); ?>
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
                                        </div>
                                        <div>
                                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'school_name','value' => __('Nama Sekolah')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'school_name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Nama Sekolah'))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => 'school_name','class' => 'block mt-1 w-full','type' => 'text','xModel' => 'selectedConfig.config_json.school_name']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'school_name','class' => 'block mt-1 w-full','type' => 'text','x-model' => 'selectedConfig.config_json.school_name']); ?>
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
                                        </div>
                                        <div class="mt-4 col-span-1 md:col-span-2">
                                            <label for="header_padding_x" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                                Posisi Horizontal Header: <span x-text="selectedConfig.config_json.header_padding_x"></span>px
                                            </label>
                                            <input type="range" id="header_padding_x" min="0" max="50" step="1" x-model.number="selectedConfig.config_json.header_padding_x" class="w-full">
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Pengaturan Aset Gambar</h4>
                                    
                                    <!-- Logo -->
                                    <div class="mb-4">
                                        <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'logo_upload','value' => __('Logo Sekolah')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'logo_upload','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Logo Sekolah'))]); ?>
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
                                        <div class="flex items-center space-x-2 mt-1">
                                            <div x-show="!selectedConfig.config_json.assets.logo_url" class="flex-grow">
                                                <input type="file" id="logo_upload" @change="uploadAsset($event, 'logo')" :disabled="isUploading.logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                                            </div>
                                            <div x-show="isUploading.logo" class="w-5 h-5 border-2 border-blue-500 border-dashed rounded-full animate-spin"></div>
                                        </div>
                                        <div x-show="selectedConfig.config_json.assets.logo_url" class="mt-2 flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-2 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-300 truncate" x-text="selectedConfig.config_json.assets.logo_path"></span>
                                            <button @click.prevent="deleteAsset('logo')" type="button" class="ml-2 text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                                        </div>
                                    </div>

                                    <!-- Watermark -->
                                    <div class="mb-4">
                                        <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'watermark_upload','value' => __('Gambar Watermark')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'watermark_upload','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Gambar Watermark'))]); ?>
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
                                        <div class="flex items-center space-x-2 mt-1">
                                            <div x-show="!selectedConfig.config_json.assets.watermark_url" class="flex-grow">
                                                <input type="file" id="watermark_upload" @change="uploadAsset($event, 'watermark')" :disabled="isUploading.watermark" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                                            </div>
                                            <div x-show="isUploading.watermark" class="w-5 h-5 border-2 border-blue-500 border-dashed rounded-full animate-spin"></div>
                                        </div>
                                        <div x-show="selectedConfig.config_json.assets.watermark_url" class="mt-2 flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-2 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-300 truncate" x-text="selectedConfig.config_json.assets.watermark_path"></span>
                                            <button @click.prevent="deleteAsset('watermark')" type="button" class="ml-2 text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                                        </div>
                                    </div>

                                    <div class="mt-4"><?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'watermark_opacity']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'watermark_opacity']); ?>Transparansi Watermark (<span x-text="selectedConfig.config_json.assets.watermark_opacity"></span>) <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?><input type="range" id="watermark_opacity" min="0" max="1" step="0.05" x-model.number="selectedConfig.config_json.assets.watermark_opacity" class="w-full"></div>
                                    <div class="mt-4">
                                        <label for="watermark_size" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                            Ukuran Watermark: <span x-text="selectedConfig.config_json.assets.watermark_size"></span>%
                                        </label>
                                        <input type="range" id="watermark_size" min="10" max="100" step="5" x-model.number="selectedConfig.config_json.assets.watermark_size" class="w-full">
                                    </div>
                                    <div class="mt-4">
                                        <label for="watermark_position_y" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                            Posisi Vertikal Watermark: <span x-text="selectedConfig.config_json.assets.watermark_position_y"></span>%
                                        </label>
                                        <input type="range" id="watermark_position_y" min="0" max="100" step="5" x-model.number="selectedConfig.config_json.assets.watermark_position_y" class="w-full">
                                    </div>
                                </div>

                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Pengaturan Foto Profil</h4>
                                    <div class="mt-4">
                                        <label for="photo_width" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                            Lebar Foto: <span x-text="selectedConfig.config_json.photo_width"></span>px
                                        </label>
                                        <input type="range" id="photo_width" min="30" max="150" step="5" x-model.number="selectedConfig.config_json.photo_width" class="w-full">
                                    </div>
                                    <div class="mt-4">
                                        <label for="photo_height" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                            Tinggi Foto: <span x-text="selectedConfig.config_json.photo_height"></span>px
                                        </label>
                                        <input type="range" id="photo_height" min="40" max="200" step="5" x-model.number="selectedConfig.config_json.photo_height" class="w-full">
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-semibold mb-2 text-gray-900 dark:text-gray-100">Pratinjau Langsung</h4>
                                    <style>
                                        .card-container-preview { display: flex; justify-content: center; padding: 5px; }
                                        .card-preview { width: 323.5px; height: 204.0px; position: relative; overflow: hidden; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
                                        .card-header-preview { padding: 5px 8px; display: flex; align-items: center; position: relative; z-index: 1; }
                                        .card-header-preview img { width: 35px; height: 35px; margin-right: 8px; }
                                        .card-header-preview p { font-size: 10px; margin: 0; }
                                        .card-header-preview .title { font-size: 12px; font-weight: bold; text-transform: uppercase; }
                                        .card-body-preview { flex-grow: 1; display: flex; padding: 8px; position: relative; z-index: 1; }
                                        .card-body-preview .photo { flex-shrink: 0; margin-right: 8px; }
                                        .card-body-preview .photo img { object-fit: cover; border: 1px solid white; border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                                        .card-body-preview .info { flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
                                        .card-body-preview .info table { font-size: 9px; width: 100%; color: inherit; }
                                        .card-body-preview .info table td { padding-bottom: 1px; }
                                        .card-body-preview .info .qr-section { display: flex; justify-content: flex-end; align-self: flex-end; margin-top: 3px; }
                                        .qr-code-container-preview { border: 2px solid white; border-radius: 6px; box-shadow: 0 3px 5px rgba(0,0,0,0.1); padding: 2px; background-color: white; display: flex; justify-content: center; align-items: center; }
                                        .card-watermark-preview { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-repeat: no-repeat; z-index: 0; }
                                    </style>
                                    <div class="card-container-preview">
                                        <div class="card-preview rounded-lg" :style="`background-color: ${hexToRgba(selectedConfig.config_json.theme.background_color, selectedConfig.config_json.theme.background_opacity)};`">
                                            <div class="card-watermark-preview" :style="`background-image: ${selectedConfig.config_json.assets.watermark_url ? `url(${selectedConfig.config_json.assets.watermark_url})` : 'none'}; opacity: ${selectedConfig.config_json.assets.watermark_opacity}; background-size: ${selectedConfig.config_json.assets.watermark_size}%; background-position: center ${selectedConfig.config_json.assets.watermark_position_y}%;`"></div>
                                            
                                            <!-- Header -->
                                            <div class="card-header-preview" :style="`background-color: ${hexToRgba(selectedConfig.config_json.theme.header_background_color, selectedConfig.config_json.theme.header_background_opacity)}; color: ${selectedConfig.config_json.theme.text_color_header}; padding-left: ${selectedConfig.config_json.header_padding_x}px; padding-right: ${selectedConfig.config_json.header_padding_x}px;`">
                                                <img :src="selectedConfig.config_json.assets.logo_url || ''" alt="Logo Sekolah" x-show="selectedConfig.config_json.assets.logo_url">
                                                <div>
                                                    <p class="title" x-text="selectedConfig.config_json.header_title || 'Kartu Absensi Siswa'"></p>
                                                    <p style="font-size: 10px;" x-text="selectedConfig.config_json.school_name || '<?php echo e(setting('nama_sekolah', 'Nama Sekolah')); ?>'"></p>
                                                </div>
                                            </div>

                                            <!-- Body -->
                                            <div class="card-body-preview" :style="`color: ${selectedConfig.config_json.theme.text_color_body};`">
                                                <!-- Foto -->
                                                <div x-show="selectedConfig.config_json.selected_fields.includes('foto')" class="photo">
                                                    <img src="https://xsgames.co/randomusers/avatar.php?g=pixel&key=1" alt="Foto Siswa" :style="`width: ${selectedConfig.config_json.photo_width}px; height: ${selectedConfig.config_json.photo_height}px;`">
                                                </div>
                                                <!-- Info & QR -->
                                                <div class="info">
                                                    <table>
                                                        <tbody>
                                                            <tr x-show="selectedConfig.config_json.selected_fields.includes('name')">
                                                                <td class="font-semibold pr-1 align-top">Nama</td>
                                                                <td class="align-top">:</td>
                                                                <td class="align-top font-bold">SISWA CONTOH</td>
                                                            </tr>
                                                            <tr x-show="selectedConfig.config_json.selected_fields.includes('nis')">
                                                                <td class="font-semibold pr-1 align-top">NIS</td>
                                                                <td class="align-top">:</td>
                                                                <td class="align-top">1234567890</td>
                                                            </tr>
                                                            <tr x-show="selectedConfig.config_json.selected_fields.includes('kelas')">
                                                                <td class="font-semibold pr-1 align-top">Kelas</td>
                                                                <td class="align-top">:</td>
                                                                <td class="align-top">XII-A</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-semibold pr-1 align-top">Masa Berlaku</td>
                                                                <td class="align-top">:</td>
                                                                <td class="align-top">Selama menjadi siswa</td>
                                                            </tr>
                                                            <tr x-show="selectedConfig.config_json.selected_fields.includes('tanggal_lahir')">
                                                                <td class="font-semibold pr-1 align-top">Lahir</td>
                                                                <td class="align-top">:</td>
                                                                <td class="align-top">Tempat, 01 Jan 2010</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="qr-section">
                                                        <div class="qr-code-container-preview">
                                                            <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=${selectedConfig.config_json.qr_size}x${selectedConfig.config_json.qr_size}&data=1234567890`" :style="`width: ${selectedConfig.config_json.qr_size}px; height: ${selectedConfig.config_json.qr_size}px;`" alt="QR Code Preview">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-6">
                                    <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit']); ?>Simpan Perubahan <?php echo $__env->renderComponent(); ?>
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
                        </div>
                        <div x-show="!selectedConfig" class="text-center text-gray-500 dark:text-gray-400">
                            <p>Pilih tema untuk diedit atau buat tema baru.</p>
                        </div>
                    </div>
                </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function cardCustomizer() {
            return {
                hexToRgba(hex, alpha = 1) {
                    if (!hex) return `rgba(255, 255, 255, ${alpha})`;
                    hex = hex.replace('#', '');
                    if (hex.length === 3) {
                        hex = hex.split('').map(char => char + char).join('');
                    }
                    const bigint = parseInt(hex, 16);
                    if (isNaN(bigint)) return `rgba(255, 255, 255, ${alpha})`;
                    const r = (bigint >> 16) & 255;
                    const g = (bigint >> 8) & 255;
                    const b = bigint & 255;
                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                },
                isLoading: true,
                isUploading: { logo: false, watermark: false },
                configs: [],
                selectedConfig: null,
                defaultConfigTemplate: {
                    name: 'Tema Baru',
                    is_default: false,
                    config_json: {
                        theme: {
                            background_color: '#ffffff',
                            background_opacity: 1,
                            header_background_color: '#1e3a8a',
                            header_background_opacity: 1,
                            text_color_header: '#ffffff',
                            text_color_body: '#333333',
                        },
                        assets: { logo_path: null, logo_url: null, watermark_path: null, watermark_url: null, watermark_opacity: 0.1, watermark_size: 70, watermark_position_y: 50 },
                        selected_fields: ['foto', 'name', 'nis', 'kelas', 'tanggal_lahir'],
                        qr_size: 70,
                        photo_width: 70,
                        photo_height: 90,
                        header_title: 'Kartu Absensi Siswa',
                        school_name: 'Nama Sekolah Contoh',
                        header_padding_x: 8
                    }
                },
                init() { this.fetchConfigs(); },
                async fetchConfigs() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('<?php echo e(url("/print-card-configs")); ?>');
                        const data = await response.json();
                        this.configs = data;
                        if (this.configs.length > 0) {
                            this.selectConfig(this.configs.find(c => c.is_default) || this.configs[0]);
                        } else {
                            this.createNew();
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Gagal memuat konfigurasi.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                selectConfig(config) {
                    let tempConfig = JSON.parse(JSON.stringify(config));
                    const defaultConfig = this.defaultConfigTemplate.config_json;
                    // Ensure backward compatibility for all new properties
                    for (const key in defaultConfig) {
                        if (!tempConfig.config_json.hasOwnProperty(key)) {
                            tempConfig.config_json[key] = defaultConfig[key];
                        }
                        // Deep merge for nested objects
                        if (typeof defaultConfig[key] === 'object' && defaultConfig[key] !== null && !Array.isArray(defaultConfig[key])) {
                            if (!tempConfig.config_json[key]) {
                                tempConfig.config_json[key] = {};
                            }
                            for (const subKey in defaultConfig[key]) {
                                if (!tempConfig.config_json[key].hasOwnProperty(subKey)) {
                                    tempConfig.config_json[key][subKey] = defaultConfig[key][subKey];
                                }
                            }
                        }
                    }
                    this.selectedConfig = tempConfig;
                },
                createNew() { this.selectedConfig = JSON.parse(JSON.stringify(this.defaultConfigTemplate)); this.selectedConfig.id = null; },
                async uploadAsset(event, assetType) {
                    if (!event.target.files.length) return;

                    this.isUploading[assetType] = true;

                    let formData = new FormData();
                    formData.append('file', event.target.files[0]);

                    let oldPath = this.selectedConfig.config_json.assets[assetType + '_path'];
                    if (oldPath) {
                        formData.append('old_path', oldPath);
                    }

                    try {
                        const response = await fetch('<?php echo e(route("card-assets.store")); ?>', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            const err = await response.json();
                            throw err;
                        }

                        const data = await response.json();
                        this.selectedConfig.config_json.assets[assetType + '_path'] = data.path;
                        this.selectedConfig.config_json.assets[assetType + '_url'] = data.url;
                        alert('Aset berhasil diupload.');

                    } catch (err) {
                        console.error(err);
                        alert('Gagal mengupload aset: ' + (err.message || JSON.stringify(err.errors)));
                    } finally {
                        this.isUploading[assetType] = false;
                        event.target.value = null;
                    }
                },
                async deleteAsset(assetType) {
                    if (!confirm(`Apakah Anda yakin ingin menghapus ${assetType} ini? Tindakan ini tidak dapat diurungkan.`)) return;

                    const path = this.selectedConfig.config_json.assets[assetType + '_path'];
                    if (!path) return;

                    this.isLoading = true;

                    try {
                        const response = await fetch('<?php echo e(route("card-assets.destroy")); ?>', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ path: path })
                        });

                        if (!response.ok) {
                            const err = await response.json();
                            throw err;
                        }

                        await response.json();
                        this.selectedConfig.config_json.assets[assetType + '_path'] = null;
                        this.selectedConfig.config_json.assets[assetType + '_url'] = null;
                        alert(`${assetType.charAt(0).toUpperCase() + assetType.slice(1)} berhasil dihapus. Jangan lupa klik "Simpan Perubahan" untuk menyimpan.`);

                    } catch (err) {
                        console.error(err);
                        alert(`Gagal menghapus ${assetType}: ` + (err.message || 'Error tidak diketahui'));
                    } finally {
                        this.isLoading = false;
                    }
                },
                async saveChanges() {
                    if (!this.selectedConfig) return;
                    const isNew = !this.selectedConfig.id;
                    const url = isNew ? '<?php echo e(url("/print-card-configs")); ?>' : `<?php echo e(url("/print-card-configs")); ?>/${this.selectedConfig.id}`;
                    const method = isNew ? 'POST' : 'PUT';
                    const payload = {
                        name: this.selectedConfig.name,
                        is_default: this.selectedConfig.is_default,
                        config_json: this.selectedConfig.config_json
                    };

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!response.ok) {
                            const err = await response.json();
                            throw err;
                        }

                        await response.json();
                        alert('Perubahan berhasil disimpan!');
                        await this.fetchConfigs();
                        this.selectedConfig = null;

                    } catch (err) {
                        console.error(err);
                        alert('Gagal menyimpan perubahan: ' + (err.message || 'Error tidak diketahui'));
                    }
                },
                async deleteConfig(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus tema ini?')) return;

                    try {
                        const response = await fetch(`<?php echo e(url("/print-card-configs")); ?>/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                'Accept': 'application/json',
                            }
                        });

                        if (!response.ok) {
                            const err = await response.json();
                            throw err;
                        }

                        alert('Tema berhasil dihapus.');
                        await this.fetchConfigs();
                        if (this.selectedConfig && this.selectedConfig.id === id) {
                            this.selectedConfig = null;
                        }

                    } catch (err) {
                        console.error(err);
                        alert('Gagal menghapus tema: ' + (err.error || 'Error tidak diketahui'));
                    }
                }
            }
        }
    </script>
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

<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/admin/print_cards/customize.blade.php ENDPATH**/ ?>