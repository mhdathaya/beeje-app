<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Orders Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
        }
        .header p {
            font-size: 16px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orders Report</h1>
        <p>Generated on: {{ now()->format('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer Name</th>
                <th>Status</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->products->pluck('name')->first() }}</td>
                <td>#{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('M jS, Y') }}</td>
                <td>{{ $order->user->name }}</td>
                <td class="status">
                    <span class="badge {{ $order->status == 'delivered' ? 'bg-success' : 
                                       ($order->status == 'canceled' ? 'bg-danger' : 
                                       ($order->status == 'paid' ? 'bg-info' : 'bg-warning')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
