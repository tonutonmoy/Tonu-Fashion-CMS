<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Packing Slip {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; font-size: 14px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f9fafb; text-align: left; }
        .qr { text-align: center; margin-top: 24px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom:16px"><button onclick="window.print()">Print</button></div>
    <h1>Packing Slip</h1>
    <p><strong>Order:</strong> {{ $order->order_number }} · <strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
    <div class="grid">
        <div>
            <h3>Customer</h3>
            <p>{{ $order->customer_name }}<br>{{ $order->customer_phone }}<br>
            {{ $order->shipping_address }}<br>
            {{ $order->shipping_area ? $order->shipping_area.', ' : '' }}{{ $order->shipping_district }}, {{ $order->shipping_division }}</p>
        </div>
        <div>
            <h3>Courier</h3>
            @if($order->courierParcel)
            <p>{{ ucfirst($order->courierParcel->courier_name) }}<br>Tracking: {{ $order->courierParcel->tracking_code }}</p>
            @else
            <p>Not assigned yet</p>
            @endif
            @if($order->order_note)<p><strong>Note:</strong> {{ $order->order_note }}</p>@endif
        </div>
    </div>
    <table>
        <thead><tr><th>Product</th><th>SKU</th><th>Qty</th></tr></thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }} @if($item->size)({{ $item->size }}/{{ $item->color }})@endif</td>
                <td>{{ $item->product_sku }}</td>
                <td>{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="qr">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($trackUrl) }}" alt="Track order QR" width="140" height="140">
        <p>Scan to track order</p>
    </div>
</body>
</html>
