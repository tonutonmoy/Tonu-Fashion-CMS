<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        :root { --ink: #1a1a2e; --muted: #64748b; --accent: #0f766e; --line: #e2e8f0; --bg: #f8fafc; }
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; font-size: 14px; color: var(--ink); margin: 0; background: var(--bg); }
        .page { max-width: 800px; margin: 24px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
        .head { display: flex; justify-content: space-between; align-items: flex-start; padding: 32px 36px 24px; background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%); color: #fff; }
        .head h1 { margin: 0; font-size: 22px; font-weight: 700; letter-spacing: -.02em; }
        .head .meta { text-align: right; font-size: 13px; opacity: .95; }
        .head .meta strong { display: block; font-size: 18px; margin-bottom: 4px; }
        .body { padding: 28px 36px 36px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
        .box h3 { margin: 0 0 8px; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); }
        .box p { margin: 0; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); padding: 10px 12px; border-bottom: 2px solid var(--line); }
        td { padding: 12px; border-bottom: 1px solid var(--line); vertical-align: top; }
        td.num, th.num { text-align: right; }
        .totals { margin-top: 20px; margin-left: auto; width: 280px; }
        .totals .row { display: flex; justify-content: space-between; padding: 6px 0; color: var(--muted); }
        .totals .grand { border-top: 2px solid var(--ink); margin-top: 8px; padding-top: 12px; font-size: 20px; font-weight: 700; color: var(--ink); }
        .foot { text-align: center; padding: 20px; font-size: 12px; color: var(--muted); border-top: 1px dashed var(--line); }
        .badge { display: inline-block; background: #ecfdf5; color: var(--accent); padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .no-print { padding: 16px; text-align: center; }
        .no-print button { background: var(--accent); color: #fff; border: 0; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-size: 14px; }
        @media print { body { background: #fff; } .page { box-shadow: none; margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print"><button onclick="window.print()">Print invoice</button></div>
    <div class="page">
        <div class="head">
            <div>
                <h1>{{ $store['name'] }}</h1>
                @if($store['phone'])<p style="margin:6px 0 0;opacity:.9">{{ $store['phone'] }}</p>@endif
                @if($store['address'])<p style="margin:4px 0 0;opacity:.85;font-size:12px">{{ $store['address'] }}</p>@endif
            </div>
            <div class="meta">
                <strong>INVOICE</strong>
                <div>{{ $order->order_number }}</div>
                <div>{{ $order->created_at->format('d M Y') }}</div>
                <div style="margin-top:8px"><span class="badge">Cash on Delivery</span></div>
            </div>
        </div>
        <div class="body">
            <div class="grid">
                <div class="box">
                    <h3>Bill to</h3>
                    <p><strong>{{ $order->customer_name }}</strong></p>
                    <p>{{ $order->customer_phone }}</p>
                </div>
                <div class="box">
                    <h3>Ship to</h3>
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_district }}, {{ $order->shipping_division }}</p>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="num">Qty</th>
                        <th class="num">Unit</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            @if($item->size || $item->color)
                            <br><small style="color:var(--muted)">{{ $item->size }} / {{ $item->color }}</small>
                            @endif
                        </td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">{{ format_bdt($item->unit_price) }}</td>
                        <td class="num">{{ format_bdt($item->total_price) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="totals">
                <div class="row"><span>Subtotal</span><span>{{ format_bdt($order->subtotal) }}</span></div>
                @if($order->discount > 0)
                <div class="row"><span>Discount</span><span>-{{ format_bdt($order->discount) }}</span></div>
                @endif
                <div class="row"><span>Shipping</span><span>{{ format_bdt($order->shipping_cost) }}</span></div>
                <div class="row grand"><span>Total due</span><span>{{ format_bdt($order->total) }}</span></div>
            </div>
        </div>
        <div class="foot">Thank you for shopping with {{ $store['name'] }}!</div>
    </div>
</body>
</html>
