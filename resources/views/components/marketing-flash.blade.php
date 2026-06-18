@if(session('marketing_add_to_cart'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.FashionMarketing) return;
    const data = @json(session('marketing_add_to_cart'));
    FashionMarketing.addToCart(data.product, data.quantity, data.value);
});
</script>
@endpush
@endif
