<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Payment Receipt</h1>
    <table>
        <tr>
            <th>Order ID</th>
            <td>{{ $payment->order_id }}</td>
        </tr>
        <tr>
            <th>User</th>
            <td>{{ $payment->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $payment->status }}</td>
        </tr>
        <tr>
            <th>Payment Date</th>
            <td>{{ $payment->payment_date->format('d-m-Y H:i') }}</td>
        </tr>
    </table>
</body>
</html>
