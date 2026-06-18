@extends('themes.fashion-modern.layouts.app')

@section('content')
<div class="theme-container">
    <div class="checkout-page">
        <h1 class="checkout-page-title">{{ __('common.checkout') }}</h1>
        @include('themes.shared.partials.checkout-form')
    </div>
</div>
@endsection
