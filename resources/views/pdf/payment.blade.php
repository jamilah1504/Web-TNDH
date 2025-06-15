{{-- <!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Payment Receipt</h2>
            <p>Payment ID: {{ $payment->id }}</p>
        </div>
        <table class="table">
            <tr>
                <th>Order ID</th>
                <td>{{ $payment->order_id }}</td>
            </tr>
            <tr>
                <th>User</th>
                <td>{{ $payment->user->name }}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ ucfirst($payment->status) }}</td>
            </tr>
            <tr>
                <th>Payment Date</th>
                <td>{{ $payment->payment_date->format('d M Y H:i') }}</td>
            </tr>
        </table>
    </div>
</body>
</html> --}}
