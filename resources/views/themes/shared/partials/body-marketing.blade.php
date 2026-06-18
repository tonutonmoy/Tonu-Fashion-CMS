@php $gtmId = app(\App\Services\MarketingService::class)->get('gtm_id'); @endphp
@if($gtmId)
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
@endif
    <x-social-chat />
    <x-support-chat-widget />
    <x-marketing-flash />
