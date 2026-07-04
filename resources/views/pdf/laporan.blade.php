<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #334155;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .container {
            padding: 40px 50px;
        }
        
        /* Header Table */
        .header-table {
            width: 100%;
            border-bottom: 3px solid #6366f1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-box {
            width: 50px;
            height: 50px;
            background-color: #6366f1;
            color: #ffffff;
            font-size: 24pt;
            font-weight: bold;
            text-align: center;
            line-height: 50px;
            border-radius: 8px;
        }
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
        }
        .company-sub {
            font-size: 9pt;
            color: #64748b;
            margin: 0;
        }
        .report-title {
            text-align: right;
            vertical-align: bottom;
        }
        .report-title h1 {
            font-size: 18pt;
            color: #6366f1;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-date {
            font-size: 8pt;
            color: #94a3b8;
        }

        /* Info Box */
        .info-table {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 25px;
            padding: 15px;
        }
        .info-label {
            font-size: 7.5pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 3px;
        }
        .info-value {
            font-size: 10pt;
            font-weight: bold;
            color: #1e293b;
            line-height: 1.3;
        }

        /* Summary Stats */
        .stats-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .stats-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            width: 23%;
        }
        .stats-val {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stats-lbl {
            font-size: 8pt;
            color: #64748b;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .data-table thead th {
            background-color: #f1f5f9;
            color: #475569;
            text-align: left;
            padding: 12px 10px;
            font-size: 8.5pt;
            font-weight: bold;
            border-bottom: 2px solid #e2e8f0;
        }
        .data-table tbody td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9pt;
            vertical-align: middle;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        /* Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 7.5pt;
            font-weight: bold;
            display: inline-block;
        }
        .badge-lulus {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-tidak {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        /* Tugas indicators */
        .task-summary {
            font-size: 7.5pt;
            color: #64748b;
        }
        .task-valid { color: #10b981; font-weight: bold; }
        .task-invalid { color: #ef4444; font-weight: bold; }

        /* Footer / Signature */
        .footer-table {
            width: 100%;
            margin-top: 20px;
        }
        .signature-box {
            width: 250px;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #334155;
            padding-top: 8px;
        }
        .admin-name {
            font-size: 10pt;
            font-weight: bold;
            color: #1e293b;
            text-decoration: underline;
        }
        .admin-title {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(99, 102, 241, 0.05);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <div class="watermark">MERSIFLAB REPORT</div>

    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td width="200">
                    @if(file_exists(public_path('img/logo-mersfilab.png')))
                        <img src="{{ public_path('img/logo-mersfilab.png') }}" style="height: 50px; width: auto;">
                    @else
                        <div class="logo-box">M</div>
                    @endif
                </td>
                <td class="report-title">
                    <h1>Laporan Peserta Pelatihan</h1>
                    <div class="report-date">Dicetak pada: {{ $tanggal }}</div>
                </td>
            </tr>
        </table>

        <!-- Info Pelatihan -->
        <table class="info-table" style="border-collapse: collapse;">
            <tr>
                <td width="65%" style="vertical-align: top; padding-bottom: 15px;">
                    <div class="info-label">Program Pelatihan</div>
                    <div class="info-value" style="font-size: 11pt; padding-right: 15px;">{{ $pelatihan->nama }}</div>
                </td>
                <td width="35%" style="vertical-align: top; padding-bottom: 15px;">
                    <div class="info-label">Tipe Program</div>
                    <div class="info-value">{{ $pelatihan->berbayar ? 'Berbayar' : 'Gratis' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 10px; border-top: 1px solid #f1f5f9;">
                    <table width="100%" style="border-collapse: collapse;">
                        <tr>
                            <td width="28%" style="vertical-align: top;">
                                <div class="info-label">Kategori</div>
                                <div class="info-value" style="font-size: 9pt;">{{ $pelatihan->kategori ?? '—' }}</div>
                            </td>
                            <td width="40%" style="vertical-align: top; padding-left: 10px;">
                                <div class="info-label">Mentor</div>
                                <div class="info-value" style="font-size: 9pt; padding-right: 10px;">{{ $pelatihan->mentors->pluck('name')->join(', ') ?: '—' }}</div>
                            </td>
                            <td width="32%" style="vertical-align: top; padding-left: 10px;">
                                <div class="info-label">Periode Pelaksanaan</div>
                                <div class="info-value" style="font-size: 9pt;">{{ $periode }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Stats -->
        @php
            $totalPeserta = $rows->count();
            $lulusCount   = $rows->where('lulus', true)->count();
            $tidakLulus   = $totalPeserta - $lulusCount;
            $avgPersen    = $totalPeserta > 0 ? round($rows->avg('persen')) : 0;
        @endphp
        <table class="stats-table">
            <tr>
                <td class="stats-card">
                    <div class="stats-val" style="color: #6366f1;">{{ $totalPeserta }}</div>
                    <div class="stats-lbl">Total Peserta</div>
                </td>
                <td width="2%">&nbsp;</td>
                <td class="stats-card">
                    <div class="stats-val" style="color: #10b981;">{{ $lulusCount }}</div>
                    <div class="stats-lbl">Lulus Kualifikasi</div>
                </td>
                <td width="2%">&nbsp;</td>
                <td class="stats-card">
                    <div class="stats-val" style="color: #ef4444;">{{ $tidakLulus }}</div>
                    <div class="stats-lbl">Tidak Lulus</div>
                </td>
                <td width="2%">&nbsp;</td>
                <td class="stats-card">
                    <div class="stats-val" style="color: #f59e0b;">{{ $avgPersen }}%</div>
                    <div class="stats-lbl">Rata Kehadiran</div>
                </td>
            </tr>
        </table>

        <!-- Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th width="30" style="text-align: center;">No</th>
                    <th>Nama Lengkap</th>
                    <th width="80" style="text-align: center;">Kehadiran</th>
                    <th width="70" style="text-align: center;">Sesi</th>
                    <th width="120" style="text-align: center;">Keterangan Tugas</th>
                    <th width="90" style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                <tr>
                    <td style="text-align: center; color: #94a3b8;">{{ $r['no'] }}</td>
                    <td><strong>{{ $r['nama'] }}</strong></td>
                    <td style="text-align: center;">
                        <span style="font-weight: bold; color: {{ $r['persen'] >= 80 ? '#10b981' : ($r['persen'] >= 60 ? '#f59e0b' : '#ef4444') }}">
                            {{ $r['persen'] }}%
                        </span>
                    </td>
                    <td style="text-align: center; color: #64748b;">{{ $r['hadir'] }}/{{ $r['total_sesi'] }}</td>
                    <td style="text-align: center;">
                        <div class="task-summary">
                            <span class="task-valid">{{ $r['tugas_valid'] }} Valid</span><br>
                            <span class="task-invalid">{{ $r['tugas_invalid'] }} Invalid</span><br>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        @if($r['lulus'])
                            <span class="badge badge-lulus">Lulus</span>
                        @else
                            <span class="badge badge-tidak">Tidak Lulus</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #94a3b8;">
                        Data peserta tidak ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer / Signature -->
        <table class="footer-table">
            <tr>
                <td style="font-size: 8pt; color: #94a3b8; vertical-align: top;">
                    <p><strong>Catatan:</strong></p>
                    <p>• Syarat kelulusan minimal kehadiran 80%.</p>
                    <p>• Minimal tugas harus tervalidasi 1.</p>
                    <p style="margin-top: 10px;">&copy; {{ now()->year }} MersifLab Training Center. All rights reserved.</p>
                </td>
                <td width="250" class="signature-box">
                    <p style="font-size: 9pt; color: #64748b; margin-bottom: 10px;">Karanganyar, {{ now()->translatedFormat('d F Y') }}</p>
                    <div style="height: 60px; text-align: center;">
                        @if(file_exists(public_path('img/ttd-admin.png')))
                            <img src="{{ public_path('img/ttd-admin.png') }}" style="height: 60px; margin-top: 5px;">
                        @endif
                    </div>
                    <div class="signature-line" style="margin-top: 5px;">
                        <div class="admin-name">{{ $admin_name }}</div>
                        <div class="admin-title">Administrator MersifLab</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
