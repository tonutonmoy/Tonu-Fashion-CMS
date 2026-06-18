@props([
    'variant' => 'edit',
    'href' => null,
    'action' => null,
    'method' => 'POST',
    'label' => null,
    'confirm' => false,
    'confirmTitle' => null,
    'confirmMessage' => null,
    'target' => null,
])

@php
    $labels = [
        'edit' => __('common.edit'),
        'view' => __('common.view'),
        'delete' => __('common.delete'),
        'approve' => __('common.approve'),
        'reject' => __('common.reject'),
        'external' => __('common.view'),
    ];
    $label = $label ?? ($labels[$variant] ?? ucfirst($variant));
    $classes = match ($variant) {
        'delete', 'reject' => 'admin-action-btn admin-action-btn--danger',
        'approve' => 'admin-action-btn admin-action-btn--success',
        'view', 'external' => 'admin-action-btn admin-action-btn--muted',
        default => 'admin-action-btn admin-action-btn--primary',
    };
@endphp

@if($action)
<form action="{{ $action }}" method="POST" class="inline" @if($confirm) data-confirm data-confirm-title="{{ $confirmTitle ?? __('admin.confirm_delete') }}" data-confirm-message="{{ $confirmMessage }}" data-confirm-ok="{{ $label }}" @endif>
    @csrf
    @if(strtoupper($method) !== 'POST') @method($method) @endif
    <button type="submit" class="{{ $classes }}" title="{{ $label }}" aria-label="{{ $label }}">
        <x-admin.icon :name="$variant === 'reject' ? 'delete' : $variant" class="w-4 h-4" />
    </button>
</form>
@else
<a href="{{ $href }}" class="{{ $classes }}" title="{{ $label }}" aria-label="{{ $label }}" @if($target) target="{{ $target }}" @endif>
    <x-admin.icon :name="$variant" class="w-4 h-4" />
</a>
@endif
