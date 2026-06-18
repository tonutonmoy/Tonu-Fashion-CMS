<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $order->order_number }}</title>
    <style>
        body { font-family: monospace; width: 80mm; margin: 0 auto; font-size: 12px; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; }
        @media print { @page { size: 80mm auto; margin: 4mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <strong>{{ $store['name'] }}</strong><br>
        {{ $store['phone'] }}<br>
        {{ $order->order_number }}<br>
        {{ $order->created_at->format('d/m/Y H:i') }}
    </div>
    <div class="line"></div>
    <p>{{ $order->customer_name }}<br>{{ $order->customer_phone }}</p>
    <div class="line"></div>
    @foreach($order->items as $item)
    <div>{{ $item->product_name }} x{{ $item->quantity }}</div>
    <div class="row"><span></span><span>{{ format_bdt($item->total_price) }}</span></div>
    @endforeach
    <div class="line"></div>
    <div class="row"><span>Shipping</span><span>{{ format_bdt($order->shipping_cost) }}</span></div>
    <div class="row"><strong>Total COD</strong><strong>{{ format_bdt($order->total) }}</strong></div>
    <div class="line"></div>
    <p class="center">Thank you!</p>
</body>
</html>
