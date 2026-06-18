<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #111; margin: 24px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .totals { margin-top: 16px; width: 300px; margin-left: auto; }
        .totals div { display: flex; justify-content: space-between; padding: 4px 0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom:16px"><button onclick="window.print()">Print</button></div>
    <div class="header">
        <div>
            <h1>{{ $store['name'] }}</h1>
            <p>{{ $store['phone'] }}</p>
            <p>{{ $store['address'] }}</p>
        </div>
        <div style="text-align:right">
            <h2>INVOICE</h2>
            <p><strong>{{ $order->order_number }}</strong></p>
            <p>{{ $order->created_at->format('d M Y') }}</p>
            <p>Payment: COD</p>
        </div>
    </div>
    <p><strong>Bill To:</strong> {{ $order->customer_name }} · {{ $order->customer_phone }}</p>
    <p>{{ $order->shipping_address }}, {{ $order->shipping_district }}, {{ $order->shipping_division }}</p>
    <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ format_bdt($item->unit_price) }}</td>
                <td>{{ format_bdt($item->total_price) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="totals">
        <div><span>Subtotal</span><span>{{ format_bdt($order->subtotal) }}</span></div>
        <div><span>Discount</span><span>-{{ format_bdt($order->discount) }}</span></div>
        <div><span>Shipping</span><span>{{ format_bdt($order->shipping_cost) }}</span></div>
        <div style="font-weight:bold;font-size:18px"><span>Total</span><span>{{ format_bdt($order->total) }}</span></div>
    </div>
</body>
</html>
