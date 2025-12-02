<?php if($paginator->hasPages()): ?>
    <nav role="navigation" aria-label="<?php echo e(__('Pagination Navigation')); ?>" class="flex items-center justify-center space-x-4">
        <div class="flex items-center space-x-2">
            
            <?php if(!$paginator->onFirstPage()): ?>
                <a href="<?php echo e($paginator->url(1)); ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300" aria-label="<?php echo e(__('Go to first page')); ?>">
                    <<
                </a>
            <?php endif; ?>

            
            <?php if(!$paginator->onFirstPage()): ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300" aria-label="<?php echo e(__('pagination.previous')); ?>">
                    <
                </a>
            <?php endif; ?>
        </div>

        <div class="flex items-center">
            
            <?php
                $window = 10; // Menampilkan 10 halaman
                $start = $paginator->currentPage() - floor($window / 2);
                $start = $start < 1 ? 1 : $start;
                $end = $start + $window - 1;
                if ($end > $paginator->lastPage()) {
                    $end = $paginator->lastPage();
                    $start = $end - $window + 1;
                    $start = $start < 1 ? 1 : $start;
                }
            ?>

            <?php for($page = $start; $page <= $end; $page++): ?>
                <?php if($page == $paginator->currentPage()): ?>
                    <span aria-current="page">
                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-indigo-600 border border-indigo-600 cursor-default leading-5 dark:bg-indigo-500 dark:text-white"><?php echo e($page); ?></span>
                </span>
            <?php else: ?>
                <a href="<?php echo e($paginator->url($page)); ?>" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:text-gray-300 dark:active:bg-gray-700 dark:focus:border-blue-800" aria-label="<?php echo e(__('Go to page :page', ['page' => $page])); ?>">
                    <?php echo e($page); ?>

                </a>
            <?php endif; ?>
        <?php endfor; ?>
        </div>

        <div class="flex items-center space-x-2">
            
            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300" aria-label="<?php echo e(__('pagination.next')); ?>">
                    >
                </a>
            <?php endif; ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->url($paginator->lastPage())); ?>" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300" aria-label="<?php echo e(__('Go to last page')); ?>">
                    >>
                </a>
            <?php endif; ?>
        </div>
    </nav>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/vendor/pagination/compact-tailwind.blade.php ENDPATH**/ ?>