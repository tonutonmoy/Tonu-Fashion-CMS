<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Shipping Label {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; width: 4in; margin: 16px auto; border: 2px solid #000; padding: 16px; }
        h2 { margin: 0 0 8px; }
        .barcode { font-size: 24px; font-weight: bold; letter-spacing: 2px; margin: 12px 0; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <h2>{{ $store['name'] }}</h2>
    <p><strong>TO:</strong> {{ $order->customer_name }}</p>
    <p>{{ $order->customer_phone }}</p>
    <p>{{ $order->shipping_address }}, {{ $order->shipping_district }}</p>
    <div class="barcode">{{ $parcel?->tracking_code ?? $order->order_number }}</div>
    <p>COD: {{ format_bdt($order->total) }}</p>
    <p>Order: {{ $order->order_number }}</p>
    @if($parcel)<p>Courier: {{ ucfirst($parcel->courier_name) }}</p>@endif
</body>
</html>
