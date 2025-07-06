<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { margin: 20px; }
        .header { text-align: center; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Pendapatan</h1>
            <p>Total Pendapatan: Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Pembayaran</th>
                    <th>Pelanggan</th>
                    <th>Order ID</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->user->name }}</td>
                        <td>{{ $payment->order->id }}</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>{{ $payment->payment_date->format('d M Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
