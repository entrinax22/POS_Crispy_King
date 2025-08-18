<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f8f8f8 0%, #fff7e6 100%);
            margin: 0;
            padding: 0;
        }
        .container {
            background: #fff;
            max-width: 600px;
            margin: 40px auto;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(230, 103, 34, 0.08);
            padding: 40px 32px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        .brand-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e67e22;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            font-weight: bold;
        }
        h1 {
            margin-top: 0;
            font-size: 2rem;
        }
        .details {
            background: #fff7e6;
            border-radius: 10px;
            padding: 20px;
            margin-top: 24px;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin-bottom: 8px;
            font-size: 1rem;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        .order-table th {
            background: #ffe0b2;
            color: #e67e22;
            font-weight: 600;
            padding: 10px 6px;
            border-bottom: 1px solid #f2f2f2;
        }
        .order-table td {
            padding: 10px 6px;
            border-bottom: 1px solid #f2f2f2;
            font-size: 0.98rem;
        }
        .order-table tr:last-child td {
            border-bottom: none;
        }
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #888;
            text-align: center;
        }
        .highlight {
            color: #e67e22;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <h1 style="color:#e62222;">Crispy King</h1>
        </div>
        <h2 style="color:#e62222;">Order Notification</h2>
        <p style="font-size:1.1rem;">{{ $customMessage }}</p>

        @if($order)
            <div class="details">
                <h3 style="color:#e67e22;">Order Details</h3>
                <ul>
                    <li><strong>Customer Name:</strong> {{ $order->user->name ?? 'N/A' }}</li>
                    <li><strong>Total Amount:</strong> <span class="highlight">₱{{ number_format($order->total_amount ?? 0, 2) }}</span></li>
                    <li><strong>Status:</strong> {{ ucfirst($order->status ?? 'N/A') }}</li>
                    <li><strong>Order Type:</strong> {{ ucfirst($order->order_type ?? 'N/A') }}</li>
                </ul>
                @if($order->orderItems && count($order->orderItems))
                    <h4 style="margin-top:18px;color:#e67e22;">Ordered Items</h4>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th align="left">Product</th>
                                <th align="center">Qty</th>
                                <th align="right">Price</th>
                                <th align="right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product->product_name ?? 'Unknown' }}</td>
                                <td align="center">{{ $item->quantity }}</td>
                                <td align="right">₱{{ number_format($item->price, 2) }}</td>
                                <td align="right">₱{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endif
        <div class="footer">
            &copy; {{ date('Y') }} Crispy King. This is an automated message. Please do not reply.<br>
            <span style="color:#e67e22;">Thank you for choosing Crispy King!</span>
        </div>
    </div>
</body>
</html>
