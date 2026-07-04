<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Konfirmasi Pembayaran</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            border: 1px solid #eee;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #6366f1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #6366f1;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        .status-badge {
            position: absolute;
            top: 40px;
            right: 40px;
            background: #10b981;
            color: white;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 10px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #666;
            width: 150px;
        }
        .value {
            color: #111;
        }
        .invoice-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #e2e8f0;
        }
        .invoice-box h3 {
            margin-top: 0;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .total-row {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #cbd5e1;
            font-size: 18px;
            font-weight: bold;
            color: #6366f1;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
        .stamp {
            margin-top: 30px;
            text-align: right;
        }
        .stamp-box {
            display: inline-block;
            text-align: center;
            border: 2px solid #10b981;
            color: #10b981;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: bold;
            transform: rotate(-5deg);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-badge">Lunas</div>
        
        <div class="header">
            <h1>MersifLab</h1>
            <p>Konfirmasi Pembayaran Resmi</p>
        </div>

        <div class="info-section">
            <table class="info-grid">
                <tr>
                    <td class="label">No. Transaksi</td>
                    <td class="value">: #TRX-{{ str_pad($pendaftaran->id, 5, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="value">: {{ $pendaftaran->updated_at->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Peserta</td>
                    <td class="value">: {{ $pendaftaran->peserta->name }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">: {{ $pendaftaran->peserta->email }}</td>
                </tr>
            </table>
        </div>

        <div class="invoice-box">
            <h3>Detail Pelatihan</h3>
            <table style="width: 100%">
                <tr>
                    <td style="color: #64748b">Program Pelatihan</td>
                    <td style="text-align: right; font-weight: bold">{{ $pendaftaran->pelatihan->nama }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; padding-top: 10px">Kategori</td>
                    <td style="text-align: right; padding-top: 10px">{{ $pendaftaran->pelatihan->kategori }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Pembayaran</td>
                    <td style="text-align: right">Rp {{ number_format($pendaftaran->pelatihan->biaya, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 30px; font-size: 13px; color: #64748b;">
            <p><strong>Catatan:</strong></p>
            <p>Nota ini merupakan bukti pembayaran yang sah dan telah diverifikasi oleh sistem MersifLab. Harap simpan nota ini sebagai bukti pendaftaran pelatihan Anda.</p>
        </div>

        <div class="stamp">
            <p style="margin-bottom: 5px; font-size: 14px">Admin MersifLab,</p>
            <div class="stamp-box">
                TERVERIFIKASI SISTEM<br>
                {{ $pendaftaran->updated_at->format('d/m/Y') }}
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} MersifLab. All rights reserved.
        </div>
    </div>
</body>
</html>
