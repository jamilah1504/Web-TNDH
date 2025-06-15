{{-- <!DOCTYPE html>
<html>
<head>
    <title>Laporan Pemasukan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .total { font-weight: bold; margin-top: 20px; text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Laporan Pemasukan</h2>
            <p>Tanggal: {{ now()->format('d M Y') }}</p>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Pembayaran</th>
                    <th>Order ID</th>
                    <th>Pengguna</th>
                    <th>Jumlah</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->order_id }}</td>
                        <td>{{ $payment->user->name }}</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>{{ $payment->payment_date->format('d M Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total">
            Total Pemasukan: Rp {{ number_format($totalIncome, 0, ',', '.') }}
        </div>
    </div>
</body>
</html> --}}
