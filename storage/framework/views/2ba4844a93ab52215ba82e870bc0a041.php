<?php $gtmId = app(\App\Services\MarketingService::class)->get('gtm_id'); ?>
<?php if($gtmId): ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo e($gtmId); ?>" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal3b8c8e6f3aa29cfb5cc3f5e9dc8c256a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b8c8e6f3aa29cfb5cc3f5e9dc8c256a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.social-chat','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('social-chat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b8c8e6f3aa29cfb5cc3f5e9dc8c256a)): ?>
<?php $attributes = $__attributesOriginal3b8c8e6f3aa29cfb5cc3f5e9dc8c256a; ?>
<?php unset($__attributesOriginal3b8c8e6f3aa29cfb5cc3f5e9dc8c256a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b8c8e6f3aa29cfb5cc3f5e9dc8c256a)): ?>
<?php $component = $__componentOriginal3b8c8e6f3aa29cfb5cc3f5e9dc8c256a; ?>
<?php unset($__componentOriginal3b8c8e6f3aa29cfb5cc3f5e9dc8c256a); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalb7ec5efbce2c02a0ddf3459034234313 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7ec5efbce2c02a0ddf3459034234313 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.support-chat-widget','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('support-chat-widget'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7ec5efbce2c02a0ddf3459034234313)): ?>
<?php $attributes = $__attributesOriginalb7ec5efbce2c02a0ddf3459034234313; ?>
<?php unset($__attributesOriginalb7ec5efbce2c02a0ddf3459034234313); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7ec5efbce2c02a0ddf3459034234313)): ?>
<?php $component = $__componentOriginalb7ec5efbce2c02a0ddf3459034234313; ?>
<?php unset($__componentOriginalb7ec5efbce2c02a0ddf3459034234313); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginala6d10de2b59920ab8797554604eab7ca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala6d10de2b59920ab8797554604eab7ca = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketing-flash','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketing-flash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala6d10de2b59920ab8797554604eab7ca)): ?>
<?php $attributes = $__attributesOriginala6d10de2b59920ab8797554604eab7ca; ?>
<?php unset($__attributesOriginala6d10de2b59920ab8797554604eab7ca); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala6d10de2b59920ab8797554604eab7ca)): ?>
<?php $component = $__componentOriginala6d10de2b59920ab8797554604eab7ca; ?>
<?php unset($__componentOriginala6d10de2b59920ab8797554604eab7ca); ?>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/body-marketing.blade.php ENDPATH**/ ?>