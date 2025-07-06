<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembayaran</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .report-container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e89919;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .report-title {
            font-size: 16px;
            font-weight: 600;
            color: #e89919;
            margin-bottom: 5px;
        }
        .report-date {
            color: #7f8c8d;
            font-size: 13px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }
        th {
            background-color: #e89919;
            color: white;
            font-weight: 600;
            padding: 10px 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        .status-paid {
            color: #27ae60;
            font-weight: 600;
        }
        .status-pending {
            color: #f39c12;
            font-weight: 600;
        }
        .status-failed {
            color: #e74c3c;
            font-weight: 600;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="header">
            <h1>TNDH Commpany</h1>
            <p>Sistem Manajemen TNDH Food</p>
        </div>

        <div class="report-title">Laporan Pemasukan</div>
        <div class="report-date">Tanggal: {{ date('d F Y') }}</div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Order</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->user->name }}</td>
                        <td>#{{ $payment->order->id }}</td>
                        <td class="amount">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="status-{{ strtolower($payment->status) }}">
                            {{ ucfirst($payment->status) }}
                        </td>
                        <td>{{ date('d M Y', strtotime($payment->payment_date)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Dicetak pada {{ date('d/m/Y H:i') }} oleh Sistem
        </div>
    </div>
</body>
</html>
