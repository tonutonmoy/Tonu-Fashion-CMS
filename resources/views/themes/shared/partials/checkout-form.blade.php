@php
    $totals = $checkoutTotals ?? [];
    $requiresDelivery = $totals['requires_delivery_selection'] ?? true;
    $deliveryOptions = $totals['delivery_options'] ?? [];
    $shippingAmount = $totals['shipping'] ?? 0;
    $totalAmount = $totals['total'] ?? (($subtotal ?? 0) + $shippingAmount);
    $freeReason = $totals['free_delivery_reason'] ?? null;
    $defaultPayment = old('payment_method', ($paymentMethods[0] ?? null)?->value ?? 'cash_on_delivery');
@endphp

<form action="{{ route('checkout.store') }}" method="POST" id="checkout-form" class="checkout-layout"
    data-subtotal="{{ $subtotal }}"
    data-item-count="{{ $items->sum('quantity') }}"
    data-checkout-event-id="{{ $checkoutEventId ?? '' }}"
    data-requires-delivery="{{ $requiresDelivery ? '1' : '0' }}"
    data-shipping-quote-url="{{ route('checkout.shipping-quote') }}"
    data-cart-url="{{ route('cart.index') }}"
    data-free-label="{{ __('common.free') }}"
    data-free-delivery-label="{{ __('common.free_delivery') }}"
    data-quantity-label="{{ __('common.quantity') }}">
    @csrf
    <input type="hidden" name="purchase_event_id" id="purchase_event_id" value="{{ $checkoutEventId ?? '' }}">
    <input type="hidden" name="fbp" id="fbp" value="">
    <input type="hidden" name="fbc" id="fbc" value="">
    <input type="hidden" name="payment_method" value="{{ $defaultPayment }}">

    <div class="checkout-main">
        <section class="checkout-section">
            <h2 class="checkout-section-title">{{ __('common.contact_information') }}</h2>
            <div class="checkout-fields">
                <div class="checkout-field">
                    <label for="customer_name" class="checkout-label">{{ __('common.full_name') }} *</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" class="checkout-input" required autocomplete="name">
                </div>
                <div class="checkout-field-row">
                    <div class="checkout-field">
                        <label for="customer_phone" class="checkout-label">{{ __('common.phone') }} *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" class="checkout-input" pattern="01[0-9]{9}" placeholder="01XXXXXXXXX" required autocomplete="tel">
                    </div>
                    <div class="checkout-field">
                        <label for="customer_email" class="checkout-label">{{ __('common.email') }} <span class="checkout-optional">({{ __('common.optional') }})</span></label>
                        <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" class="checkout-input" autocomplete="email">
                    </div>
                </div>
            </div>
        </section>

        <section class="checkout-section">
            <h2 class="checkout-section-title">{{ __('common.delivery_address') }}</h2>
            <div class="checkout-fields">
                <div class="checkout-field">
                    <label for="shipping_address" class="checkout-label">{{ __('common.full_address') }} *</label>
                    <textarea id="shipping_address" name="shipping_address" rows="3" class="checkout-input checkout-textarea" required placeholder="{{ __('common.address_placeholder') }}">{{ old('shipping_address') }}</textarea>
                </div>
            </div>
        </section>

        <section class="checkout-section" id="delivery-section">
            <h2 class="checkout-section-title">{{ __('common.delivery_method') }}</h2>

            @if($freeReason)
                <div class="checkout-delivery-free" id="delivery-free-banner">
                    <span class="checkout-delivery-free-badge">{{ __('common.free') }}</span>
                    <p>{{ $freeReason }}</p>
                </div>
            @endif

            <div class="checkout-delivery-options @if(!$requiresDelivery) hidden @endif" id="delivery-options">
                @foreach($deliveryOptions as $index => $option)
                <label class="checkout-delivery-option">
                    <input type="radio" name="delivery_zone" value="{{ $option['id'] }}" @checked(old('delivery_zone', $index === 0 ? $option['id'] : null) === $option['id']) @required($requiresDelivery)>
                    <span class="checkout-delivery-option-body">
                        <span class="checkout-delivery-option-label">{{ $option['label'] }}</span>
                        <span class="checkout-delivery-option-price">{{ format_bdt($option['price']) }}</span>
                    </span>
                </label>
                @endforeach
            </div>
        </section>

        <div class="checkout-mobile-submit lg:hidden">
            <button type="submit" class="checkout-submit-btn" id="place-order-btn-mobile">{{ __('common.place_order') }} · <span id="summary-total-mobile">{{ format_bdt($totalAmount) }}</span></button>
        </div>
    </div>

    <aside class="checkout-sidebar">
        <div class="checkout-summary">
            <h2 class="checkout-summary-title">{{ __('common.order_summary') }}</h2>

            <ul class="checkout-line-items" id="checkout-line-items">
                @foreach($items as $item)
                <li class="checkout-line-item" data-checkout-item="{{ $item->id }}">
                    <div class="checkout-line-thumb-wrap">
                        @if($item->product->primary_image)
                        <img src="{{ image_url($item->product->primary_image) }}" alt="" class="checkout-line-thumb">
                        @else
                        <div class="checkout-line-thumb checkout-line-thumb--empty"></div>
                        @endif
                        <span class="checkout-line-qty" data-checkout-qty-badge>{{ $item->quantity }}</span>
                    </div>
                    <div class="checkout-line-info">
                        <p class="checkout-line-name">{{ $item->product->name }}</p>
                        @if($item->variant)
                        <p class="checkout-line-variant text-xs text-gray-500">{{ $item->variant->display_name }}</p>
                        @endif
                        @if($item->product->free_delivery)
                        <span class="checkout-line-badge">{{ __('common.free_delivery') }}</span>
                        @endif
                        <div class="checkout-line-qty-controls" aria-label="{{ __('common.quantity') }}">
                            <button type="button" class="checkout-qty-btn" data-checkout-qty="dec" data-id="{{ $item->id }}" aria-label="{{ __('common.decrease_quantity') }}">−</button>
                            <span class="checkout-qty-value" data-checkout-qty-value>{{ $item->quantity }}</span>
                            <button type="button" class="checkout-qty-btn" data-checkout-qty="inc" data-id="{{ $item->id }}" aria-label="{{ __('common.increase_quantity') }}">+</button>
                        </div>
                    </div>
                    <span class="checkout-line-price" data-checkout-line-total>{{ format_bdt($item->line_total) }}</span>
                </li>
                @endforeach
            </ul>

            <div class="checkout-totals">
                <div class="checkout-total-row">
                    <span>{{ __('common.subtotal') }}</span>
                    <span id="summary-subtotal">{{ format_bdt($subtotal) }}</span>
                </div>
                <div class="checkout-total-row">
                    <span>{{ __('common.shipping') }} <span id="shipping-label" class="checkout-shipping-label">@if($freeReason)({{ __('common.free') }})@endif</span></span>
                    <span id="summary-shipping">{{ $shippingAmount > 0 ? format_bdt($shippingAmount) : __('common.free') }}</span>
                </div>
                <div class="checkout-total-row checkout-total-row--grand">
                    <span>{{ __('common.total') }}</span>
                    <span id="summary-total">{{ format_bdt($totalAmount) }}</span>
                </div>
            </div>

            <button type="submit" class="checkout-submit-btn hidden lg:flex" id="place-order-btn">{{ __('common.place_order') }}</button>
        </div>
    </aside>
</form>

@once
@push('scripts')
@vite('resources/js/checkout.js')
@endpush
@endonce
