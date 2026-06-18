<?php
    $totals = $checkoutTotals ?? [];
    $requiresDelivery = $totals['requires_delivery_selection'] ?? true;
    $deliveryOptions = $totals['delivery_options'] ?? [];
    $shippingAmount = $totals['shipping'] ?? 0;
    $totalAmount = $totals['total'] ?? (($subtotal ?? 0) + $shippingAmount);
    $freeReason = $totals['free_delivery_reason'] ?? null;
    $defaultPayment = old('payment_method', ($paymentMethods[0] ?? null)?->value ?? 'cash_on_delivery');
?>

<form action="<?php echo e(route('checkout.store')); ?>" method="POST" id="checkout-form" class="checkout-layout">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="purchase_event_id" id="purchase_event_id" value="<?php echo e($checkoutEventId ?? ''); ?>">
    <input type="hidden" name="fbp" id="fbp" value="">
    <input type="hidden" name="fbc" id="fbc" value="">
    <input type="hidden" name="payment_method" value="<?php echo e($defaultPayment); ?>">

    <div class="checkout-main">
        <section class="checkout-section">
            <h2 class="checkout-section-title"><?php echo e(__('common.contact_information')); ?></h2>
            <div class="checkout-fields">
                <div class="checkout-field">
                    <label for="customer_name" class="checkout-label"><?php echo e(__('common.full_name')); ?> *</label>
                    <input type="text" id="customer_name" name="customer_name" value="<?php echo e(old('customer_name', auth()->user()?->name)); ?>" class="checkout-input" required autocomplete="name">
                </div>
                <div class="checkout-field-row">
                    <div class="checkout-field">
                        <label for="customer_phone" class="checkout-label"><?php echo e(__('common.phone')); ?> *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" value="<?php echo e(old('customer_phone', auth()->user()?->phone)); ?>" class="checkout-input" pattern="01[0-9]{9}" placeholder="01XXXXXXXXX" required autocomplete="tel">
                    </div>
                    <div class="checkout-field">
                        <label for="customer_email" class="checkout-label"><?php echo e(__('common.email')); ?> <span class="checkout-optional">(<?php echo e(__('common.optional')); ?>)</span></label>
                        <input type="email" id="customer_email" name="customer_email" value="<?php echo e(old('customer_email', auth()->user()?->email)); ?>" class="checkout-input" autocomplete="email">
                    </div>
                </div>
            </div>
        </section>

        <section class="checkout-section">
            <h2 class="checkout-section-title"><?php echo e(__('common.delivery_address')); ?></h2>
            <div class="checkout-fields">
                <div class="checkout-field">
                    <label for="shipping_address" class="checkout-label"><?php echo e(__('common.full_address')); ?> *</label>
                    <textarea id="shipping_address" name="shipping_address" rows="3" class="checkout-input checkout-textarea" required placeholder="<?php echo e(__('common.address_placeholder')); ?>"><?php echo e(old('shipping_address')); ?></textarea>
                </div>
            </div>
        </section>

        <section class="checkout-section" id="delivery-section">
            <h2 class="checkout-section-title"><?php echo e(__('common.delivery_method')); ?></h2>

            <?php if($freeReason): ?>
                <div class="checkout-delivery-free" id="delivery-free-banner">
                    <span class="checkout-delivery-free-badge"><?php echo e(__('common.free')); ?></span>
                    <p><?php echo e($freeReason); ?></p>
                </div>
            <?php endif; ?>

            <div class="checkout-delivery-options <?php if(!$requiresDelivery): ?> hidden <?php endif; ?>" id="delivery-options">
                <?php $__currentLoopData = $deliveryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="checkout-delivery-option">
                    <input type="radio" name="delivery_zone" value="<?php echo e($option['id']); ?>" <?php if(old('delivery_zone', $index === 0 ? $option['id'] : null) === $option['id']): echo 'checked'; endif; ?> <?php if($requiresDelivery): echo 'required'; endif; ?>>
                    <span class="checkout-delivery-option-body">
                        <span class="checkout-delivery-option-label"><?php echo e($option['label']); ?></span>
                        <span class="checkout-delivery-option-price"><?php echo e(format_bdt($option['price'])); ?></span>
                    </span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <div class="checkout-mobile-submit lg:hidden">
            <button type="submit" class="checkout-submit-btn" id="place-order-btn-mobile"><?php echo e(__('common.place_order')); ?> · <span id="summary-total-mobile"><?php echo e(format_bdt($totalAmount)); ?></span></button>
        </div>
    </div>

    <aside class="checkout-sidebar">
        <div class="checkout-summary">
            <h2 class="checkout-summary-title"><?php echo e(__('common.order_summary')); ?></h2>

            <ul class="checkout-line-items">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="checkout-line-item">
                    <div class="checkout-line-thumb-wrap">
                        <?php if($item->product->primary_image): ?>
                        <img src="<?php echo e(image_url($item->product->primary_image)); ?>" alt="" class="checkout-line-thumb">
                        <?php else: ?>
                        <div class="checkout-line-thumb checkout-line-thumb--empty"></div>
                        <?php endif; ?>
                        <span class="checkout-line-qty"><?php echo e($item->quantity); ?></span>
                    </div>
                    <div class="checkout-line-info">
                        <p class="checkout-line-name"><?php echo e($item->product->name); ?></p>
                        <?php if($item->product->free_delivery): ?>
                        <span class="checkout-line-badge"><?php echo e(__('common.free_delivery')); ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="checkout-line-price"><?php echo e(format_bdt($item->line_total)); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>

            <div class="checkout-totals">
                <div class="checkout-total-row">
                    <span><?php echo e(__('common.subtotal')); ?></span>
                    <span id="summary-subtotal"><?php echo e(format_bdt($subtotal)); ?></span>
                </div>
                <div class="checkout-total-row">
                    <span><?php echo e(__('common.shipping')); ?> <span id="shipping-label" class="checkout-shipping-label"><?php if($freeReason): ?>(<?php echo e(__('common.free')); ?>)<?php endif; ?></span></span>
                    <span id="summary-shipping"><?php echo e($shippingAmount > 0 ? format_bdt($shippingAmount) : __('common.free')); ?></span>
                </div>
                <div class="checkout-total-row checkout-total-row--grand">
                    <span><?php echo e(__('common.total')); ?></span>
                    <span id="summary-total"><?php echo e(format_bdt($totalAmount)); ?></span>
                </div>
            </div>

            <button type="submit" class="checkout-submit-btn hidden lg:flex" id="place-order-btn"><?php echo e(__('common.place_order')); ?></button>
        </div>
    </aside>
</form>

<?php if (! $__env->hasRenderedOnce('f7a44bc7-4be5-4dd2-a8f2-06b2167aa59c')): $__env->markAsRenderedOnce('f7a44bc7-4be5-4dd2-a8f2-06b2167aa59c'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkout-form');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const checkoutEventId = <?php echo json_encode($checkoutEventId ?? null, 15, 512) ?>;
    const requiresDelivery = <?php echo json_encode($requiresDelivery, 15, 512) ?>;

    function cookie(name) {
        const m = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return m ? decodeURIComponent(m[2]) : '';
    }

    document.getElementById('fbp').value = cookie('_fbp');
    document.getElementById('fbc').value = cookie('_fbc');

    if (window.FashionMarketing && checkoutEventId) {
        FashionMarketing.initiateCheckout(<?php echo e($subtotal); ?>, <?php echo e($items->sum('quantity')); ?>, checkoutEventId);
    }

    form?.addEventListener('submit', function () {
        if (window.FashionMarketing) {
            document.getElementById('purchase_event_id').value = FashionMarketing.eventId();
        }
        document.getElementById('fbp').value = cookie('_fbp');
        document.getElementById('fbc').value = cookie('_fbc');
    });

    function formatBdt(n) {
        if (n === 0) return <?php echo json_encode(__('common.free'), 15, 512) ?>;
        return '৳' + Number(n).toLocaleString('en-BD', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function selectedZone() {
        return form.querySelector('input[name="delivery_zone"]:checked')?.value || '';
    }

    async function updateQuote() {
        const zone = selectedZone();
        if (requiresDelivery && !zone) return;

        const params = new URLSearchParams();
        if (zone) params.set('delivery_zone', zone);

        const res = await fetch('<?php echo e(route('checkout.shipping-quote')); ?>?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        document.getElementById('summary-shipping').textContent = formatBdt(data.shipping);
        document.getElementById('summary-total').textContent = formatBdt(data.total);
        const mobileTotal = document.getElementById('summary-total-mobile');
        if (mobileTotal) mobileTotal.textContent = formatBdt(data.total);

        const label = document.getElementById('shipping-label');
        if (label) {
            label.textContent = data.shipping_label && data.shipping > 0
                ? '(' + data.shipping_label + ')'
                : data.free_delivery_reason ? '(<?php echo e(__('common.free')); ?>)' : '';
        }
    }

    form.querySelectorAll('input[name="delivery_zone"]').forEach(radio => {
        radio.addEventListener('change', updateQuote);
    });

    if (requiresDelivery) {
        updateQuote();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/checkout-form.blade.php ENDPATH**/ ?>