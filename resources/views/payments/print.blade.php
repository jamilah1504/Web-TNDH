<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pembayaran #{{ $payment->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f9f9f9;
        }
        .invoice-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e89919;
        }
        .company-info h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .company-info p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            margin: 0;
            color: #e89919;
            font-size: 22px;
        }
        .invoice-title p {
            margin: 5px 0 0;
            color: #7f8c8d;
        }
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .detail-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }
        .detail-box h3 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: 600;
            color: #7f8c8d;
        }
        .detail-value {
            text-align: right;
        }
        .amount {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            font-size: 16px;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            display: inline-block;
        }
        .status-paid {
            background-color: #e8f5e9;
            color: #27ae60;
        }
        .status-pending {
            background-color: #fff8e1;
            color: #f39c12;
        }
        .status-failed {
            background-color: #ffebee;
            color: #e74c3c;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        @media print {
            body {
                background: none;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <h1>TNDH Food</h1>
                <p>Jl. Contoh No. 123, Kota</p>
                <p>Telp: (021) 12345678</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p>#{{ $payment->id }}</p>
                <p>Tanggal: {{ date('d F Y', strtotime($payment->payment_date)) }}</p>
            </div>
        </div>

        <div class="invoice-details">
            <div class="detail-box">
                <h3>Informasi Pelanggan</h3>
                <div class="detail-row">
                    <span class="detail-label">Nama:</span>
                    <span class="detail-value">{{ $payment->user->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $payment->user->email }}</span>
                </div>
            </div>

            <div class="detail-box">
                <h3>Detail Pembayaran</h3>
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">#{{ $payment->order->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status status-{{ strtolower($payment->status) }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-box">
            <h3>Rincian Pembayaran</h3>
            <div class="detail-row">
                <span class="detail-label">Total Pembayaran:</span>
                <span class="detail-value amount">Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Metode Pembayaran:</span>
                <span class="detail-value">{{ $payment->payment_method ?? 'Transfer Bank' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Pembayaran:</span>
                <span class="detail-value">{{ date('d F Y H:i', strtotime($payment->payment_date)) }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja dengan kami</p>
            <p>Invoice ini sah dan diproses oleh komputer</p>
        </div>
    </div>
</body>
</html>
