<?php if(isset($sections['faq']) && !empty($sections['faq']['items'])): ?>
<section class="theme-section">
    <div class="theme-container theme-faq">
        <h2 class="theme-section-title">FAQ</h2>
        <div class="space-y-3">
            <?php $__currentLoopData = $sections['faq']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <details class="theme-faq-item">
                <summary class="theme-faq-question"><?php echo e($item['question'] ?? ''); ?></summary>
                <p class="theme-faq-answer"><?php echo e($item['answer'] ?? ''); ?></p>
            </details>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/sections/faq.blade.php ENDPATH**/ ?>