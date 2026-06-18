@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $defaultLocale = setting('default_locale', config('locales.default', 'en'));
    $defaultColorMode = setting('default_color_mode', config('locales.default_color_mode', 'light'));
@endphp

@if(auth()->user()?->canAdmin('settings'))
<div class="card p-6 mb-6">
    <h3 class="text-lg font-semibold mb-1">{{ __('admin.store_preferences') }}</h3>
    <p class="text-sm text-gray-500 mb-4">{{ __('admin.store_preferences_hint') }}</p>
    <form action="{{ route('admin.preferences.update') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        @csrf
        <div>
            <label class="label" for="default_locale">{{ __('admin.default_language') }}</label>
            <select name="default_locale" id="default_locale" class="input">
                @foreach(config('locales.supported', ['en', 'bn']) as $locale)
                <option value="{{ $locale }}" @selected($defaultLocale === $locale)>{{ strtoupper($locale) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label" for="default_color_mode">{{ __('admin.default_color_mode') }}</label>
            <select name="default_color_mode" id="default_color_mode" class="input">
                <option value="light" @selected($defaultColorMode === 'light')>{{ __('admin.light') }}</option>
                <option value="dark" @selected($defaultColorMode === 'dark')>{{ __('admin.dark') }}</option>
            </select>
        </div>
        <div class="sm:col-span-2 lg:col-span-2 flex justify-end">
            <button type="submit" class="btn-primary">{{ __('common.save') }}</button>
        </div>
    </form>
</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="orders" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Today's Orders</p>
      <p class="text-3xl font-bold">{{ $courier['today_orders'] }}</p>
    </div>
  </div>
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-green-100 text-green-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="delivery" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Today's Deliveries</p>
      <p class="text-3xl font-bold text-green-600">{{ $courier['today_deliveries'] }}</p>
    </div>
  </div>
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="courier" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Delivered Orders</p>
      <p class="text-3xl font-bold">{{ $courier['delivered_orders'] }}</p>
    </div>
  </div>
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="return" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Return Orders</p>
      <p class="text-3xl font-bold text-orange-600">{{ $courier['return_orders'] }}</p>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="orders" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Total Orders</p>
      <p class="text-3xl font-bold">{{ $stats['orders'] }}</p>
    </div>
  </div>
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="clock" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Pending Orders</p>
      <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</p>
    </div>
  </div>
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="products" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Products</p>
      <p class="text-3xl font-bold">{{ $stats['products'] }}</p>
    </div>
  </div>
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="customers" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Customers</p>
      <p class="text-3xl font-bold">{{ $stats['customers'] }}</p>
    </div>
  </div>
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="chart" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Delivery Rate</p>
      <p class="text-3xl font-bold text-brand-600">{{ $courier['delivery_rate'] }}%</p>
    </div>
  </div>
  <div class="card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-green-100 text-green-700 flex items-center justify-center shrink-0">
      <x-admin.icon name="revenue" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-sm text-gray-500">Revenue</p>
      <p class="text-3xl font-bold text-green-600">{{ format_bdt($stats['revenue']) }}</p>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
  <div class="card">
    <div class="p-4 border-b border-gray-200 font-semibold flex items-center gap-2">
      <x-admin.icon name="courier" class="w-5 h-5 text-gray-500" />
      Courier Performance
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">Courier</th>
            <th class="px-4 py-3 text-right">Total</th>
            <th class="px-4 py-3 text-right">Delivered</th>
            <th class="px-4 py-3 text-right">Rate</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($courier['courier_performance'] as $row)
          <tr>
            <td class="px-4 py-3 capitalize">{{ $row['courier'] }}</td>
            <td class="px-4 py-3 text-right">{{ $row['total'] }}</td>
            <td class="px-4 py-3 text-right">{{ $row['delivered'] }}</td>
            <td class="px-4 py-3 text-right">{{ $row['rate'] }}%</td>
          </tr>
          @empty
          <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No courier data yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card">
    <div class="p-4 border-b border-gray-200 font-semibold flex items-center justify-between">
      <span class="flex items-center gap-2">
        <x-admin.icon name="activity" class="w-5 h-5 text-gray-500" />
        Recent Activity
      </span>
      <a href="{{ route('admin.courier.activity') }}" class="text-sm text-brand-600">View all</a>
    </div>
    <div class="divide-y divide-gray-100 text-sm">
      @forelse($activityLogs as $log)
      <div class="px-4 py-3">
        <p class="font-medium">{{ $log->description }}</p>
        <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }} · {{ $log->action }}</p>
      </div>
      @empty
      <p class="px-4 py-6 text-gray-500">No activity yet.</p>
      @endforelse
    </div>
  </div>
</div>

<div class="card">
  <div class="p-4 border-b border-gray-200 font-semibold flex items-center gap-2">
    <x-admin.icon name="orders" class="w-5 h-5 text-gray-500" />
    Recent Orders
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left">Order</th>
          <th class="px-4 py-3 text-left">Customer</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Courier</th>
          <th class="px-4 py-3 text-right">Total</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach($recentOrders as $order)
        <tr>
          <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-brand-600 hover:underline">{{ $order->order_number }}</a></td>
          <td class="px-4 py-3">{{ $order->customer_name }}</td>
          <td class="px-4 py-3"><span class="badge bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">{{ $order->status->label() }}</span></td>
          <td class="px-4 py-3 capitalize">{{ $order->courierParcel?->courier_name ?? '—' }}</td>
          <td class="px-4 py-3 text-right">{{ format_bdt($order->total) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
