@if(session('track_purchase'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.FashionMarketing) return;
    FashionMarketing.purchase({
        order_number: @json($order->order_number),
        total: {{ (float) $order->total }},
        event_id: @json($order->purchase_event_id),
        content_ids: @json($order->items->pluck('product_sku')->filter()->values()),
        num_items: {{ $order->items->sum('quantity') }},
    });
});
</script>
@endpush
@endif
