@php $marketing = app(\App\Services\MarketingService::class)->all(); @endphp
<script>
window.__MARKETING__ = {
    fb_pixel: @json($marketing['facebook_pixel_id'] ?? ''),
    ga_id: @json($marketing['ga_measurement_id'] ?? ''),
    gtm_id: @json($marketing['gtm_id'] ?? ''),
    tiktok_id: @json($marketing['tiktok_pixel_id'] ?? ''),
};
</script>
