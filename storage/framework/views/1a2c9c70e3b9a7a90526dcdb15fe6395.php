<?php $__currentLoopData = $sectionKeys ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $view = config("cms.homepage_sections.{$sectionKey}");
        $productKeys = config('cms.product_section_keys', []);
    ?>
    <?php if($view && view()->exists($view)): ?>
        <div id="section-<?php echo e($sectionKey); ?>">
        <?php if(in_array($sectionKey, $productKeys, true)): ?>
            <?php echo $__env->make($view, ['sections' => $sections, 'sectionKey' => $sectionKey], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make($view, ['sections' => $sections], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/home.blade.php ENDPATH**/ ?>