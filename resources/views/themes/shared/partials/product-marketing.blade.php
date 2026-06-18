@if(!empty($marketingProduct))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.FashionMarketing) {
        FashionMarketing.viewContent(@json($marketingProduct));
    }
});
</script>
@endpush
@endif
