<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $peserta->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            width: 297mm;
            height: 210mm;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #ffffff;
            @if(!$pelatihan->sertifikat_template)
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%230f4c75' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2v-4h4v-2H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            @else
            background-image: url("{{ public_path('storage/' . $pelatihan->sertifikat_template) }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            @endif
            color: #1e293b;
            position: relative;
            overflow: hidden;
        }

        /* Outer Frame Border */
        .frame-outer {
            @if($pelatihan->sertifikat_template) display: none; @endif
            position: absolute;
            top: 5mm; left: 5mm; right: 5mm; bottom: 5mm;
            border: 2px solid #0f4c75;
            z-index: 1;
        }
        .frame-inner {
            @if($pelatihan->sertifikat_template) display: none; @endif
            position: absolute;
            top: 7.5mm; left: 7.5mm; right: 7.5mm; bottom: 7.5mm;
            border: 1.5px solid #d4af37;
            z-index: 1;
        }

        /* Subtle Corner Decorations */
        .decor-tl {
            @if($pelatihan->sertifikat_template) display: none; @endif
            position: absolute;
            top: 0; left: 0;
            width: 60mm; height: 60mm;
            background: linear-gradient(135deg, #0f4c75 12%, transparent 12%) 0 0,
                        linear-gradient(135deg, #d4af37 8%, transparent 8%) 0 0;
            opacity: 0.12;
            z-index: 2;
        }
        .decor-tr {
            @if($pelatihan->sertifikat_template) display: none; @endif
            position: absolute;
            top: 10mm; right: 10mm;
            width: 30mm; height: 30mm;
            border-top: 1.5px solid #d4af37;
            border-right: 1.5px solid #d4af37;
            opacity: 0.25;
            z-index: 2;
        }
        .decor-br {
            @if($pelatihan->sertifikat_template) display: none; @endif
            position: absolute;
            bottom: 0; right: 0;
            width: 60mm; height: 60mm;
            background: linear-gradient(315deg, #0f4c75 12%, transparent 12%) 0 0,
                        linear-gradient(315deg, #d4af37 8%, transparent 8%) 0 0;
            opacity: 0.12;
            z-index: 2;
        }
        .decor-bl {
            @if($pelatihan->sertifikat_template) display: none; @endif
            position: absolute;
            bottom: 10mm; left: 10mm;
            width: 30mm; height: 30mm;
            border-bottom: 1.5px solid #d4af37;
            border-left: 1.5px solid #d4af37;
            opacity: 0.25;
            z-index: 2;
        }
        
        .watermark {
            @if($pelatihan->sertifikat_template) display: none; @endif
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-28deg);
            font-size: 110pt;
            font-weight: bold;
            color: rgba(15, 76, 117, 0.02);
            white-space: nowrap;
            z-index: 0;
            pointer-events: none;
        }

        /* Container */
        .content {
            position: relative;
            z-index: 10;
            padding: 17mm 24mm 10mm 24mm;
            text-align: center;
        }

        /* Header Layout */
        .header-logos {
            width: 100%;
            margin-bottom: 6mm;
            text-align: center;
        }
        .logo-placeholder { 
            display: inline-block; 
            font-weight: 800; 
            font-size: 16pt; 
            color: #0f4c75;
            vertical-align: middle;
            margin: 0 8mm;
            letter-spacing: 0.5px;
        }
        .logo-placeholder span { color: #d4af37; }

        /* Certificate Heading */
        .cert-title {
            font-size: 40pt; 
            font-weight: bold;
            color: #0c2340; 
            text-transform: uppercase;
            margin-bottom: 1mm;
            letter-spacing: 4px;
        }
        .cert-no {
            font-size: 11pt;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 5mm;
            letter-spacing: 1px;
        }

        /* Recipient Section */
        .label-given {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 3.5mm;
            letter-spacing: 2px;
        }
        
        .name-container {
            display: inline-block;
            margin-bottom: 5.5mm;
            border-bottom: 3px solid #d4af37; 
            padding-bottom: 1.5mm;
            min-width: 140mm;
        }
        .name {
            font-size: 36pt; 
            font-weight: bold;
            color: #0c2340;
            text-transform: uppercase;
            font-family: 'Georgia', 'Times New Roman', serif;
            letter-spacing: 1px;
        }

        /* Training Pill Box */
        .sebagai {
            font-size: 11.5pt;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 2mm;
            letter-spacing: 0.5px;
        }
        .pill-container {
            display: inline-block;
            border: 1.5px solid #c5a880;
            padding: 2px;
            border-radius: 50px;
            margin-bottom: 5.5mm;
        }
        .pill-box {
            background-color: #fbfbf8;
            border: 1px dashed #d4af37;
            padding: 3mm 14mm;
            border-radius: 50px;
            font-size: 16pt; 
            font-weight: bold;
            color: #0f4c75;
            letter-spacing: 0.5px;
        }

        /* Description Body */
        .description {
            font-size: 11.5pt;
            color: #475569;
            line-height: 1.55;
            max-width: 230mm;
            margin: 0 auto 8mm auto;
            text-align: center;
        }

        /* Footer / Signatures Grid */
        .footer-sigs {
            width: 100%;
            margin-top: 1mm;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-box {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .stamp-area {
            position: relative;
            height: 20mm;
            margin-bottom: 1px;
        }
        .signature-img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            height: 24mm;
            z-index: 1; 
        }
        .stamp-img {
            position: absolute;
            top: 30%;
            left: 38%;
            transform: translate(-50%, -50%);
            height: 26mm;
            opacity: 0.85;
            z-index: 2; 
        }
        
        .sig-line {
            width: 65mm;
            border-top: 1.5px solid #94a3b8;
            margin: 0 auto 2mm auto;
            padding-top: 2mm;
        }
        .sig-name {
            font-size: 11.5pt;
            font-weight: bold;
            color: #0c2340;
        }
        .sig-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="frame-outer"></div>
    <div class="frame-inner"></div>
    <div class="watermark">MERSIFLAB</div>

    <div class="decor-tl"></div>
    <div class="decor-tr"></div>
    <div class="decor-br"></div>
    <div class="decor-bl"></div>

    <div class="content">
        {{-- Header Logos --}}
        <div class="header-logos">
            {{-- Logo Kiri --}}
            @if($pelatihan->sertifikat_logo_kiri && file_exists(public_path('storage/' . $pelatihan->sertifikat_logo_kiri)))
                <img src="{{ public_path('storage/' . $pelatihan->sertifikat_logo_kiri) }}" style="height: 14mm; vertical-align: middle; margin-right: 6mm;">
            @elseif(file_exists(public_path('img/logo-enuma.png')))
                <img src="{{ public_path('img/logo-enuma.png') }}" style="height: 14mm; vertical-align: middle; margin-right: 6mm;">
            @else
                <div class="logo-placeholder" style="vertical-align: middle;">ENUMA</div>
            @endif

            {{-- Spacer --}}
            <span style="display: inline-block; width: 8mm;"></span>

            {{-- Logo Kanan --}}
            @if($pelatihan->sertifikat_logo_kanan && file_exists(public_path('storage/' . $pelatihan->sertifikat_logo_kanan)))
                <img src="{{ public_path('storage/' . $pelatihan->sertifikat_logo_kanan) }}" style="height: 14mm; vertical-align: middle; margin-left: 6mm;">
            @elseif(file_exists(public_path('img/logo-mersfilab.png')))
                <img src="{{ public_path('img/logo-mersfilab.png') }}" style="height: 14mm; vertical-align: middle; margin-left: 6mm;">
            @elseif(file_exists(public_path('img/logo_mersiflab.png')))
                <img src="{{ public_path('img/logo_mersiflab.png') }}" style="height: 14mm; vertical-align: middle; margin-left: 6mm;">
            @else
                <div class="logo-placeholder" style="vertical-align: middle;">MERSIF<span>LAB</span></div>
            @endif
        </div>

        <h1 class="cert-title">SERTIFIKAT</h1>
        <p class="cert-no">No: {{ $sertifikat->nomor_sertifikat }}</p>

        <p class="label-given">Diberikan Kepada:</p>
        <div class="name-container">
            <h2 class="name">{{ $peserta->name }}</h2>
        </div>

        <p class="sebagai">Sebagai Peserta Pelatihan</p>
        <div class="pill-container">
            <div class="pill-box">
                {{ $pelatihan->nama }}
            </div>
        </div>

        <p class="description">
            Atas dedikasi dan kontribusi sebagai peserta dalam pelatihan {{ $pelatihan->nama }} yang diselenggarakan oleh MersifLab pada tanggal {{ $tanggal }}. Semoga kompetensi yang diraih dapat memberikan dampak yang positif dan bermanfaat bagi perkembangan profesional di masa mendatang.
        </p>

        {{-- Signature Block --}}
        <div class="footer-sigs">
            <table class="sig-table">
                <tr>
                    <td class="sig-box">
                        <p class="sig-title">MENTOR UTAMA</p>
                        <div class="stamp-area">
                            @if(file_exists(public_path('img/ttd-mentor.png')))
                                <img src="{{ public_path('img/ttd-mentor.png') }}" class="signature-img">
                            @endif
                        </div>
                        <div class="sig-line">
                            <p class="sig-name">{{ $pelatihan->mentors->pluck('name')->first() ?: 'Mentor Pengampu' }}</p>
                        </div>
                    </td>
                    <td class="sig-box">
                        <p class="sig-title">DIREKTUR MERSIFLAB</p>
                        <div class="stamp-area">
                            @if(file_exists(public_path('img/ttd-direktur.png')))
                                <img src="{{ public_path('img/ttd-direktur.png') }}" class="signature-img">
                            @endif
                            @if(file_exists(public_path('img/cap-enuma.png')))
                                <img src="{{ public_path('img/cap-enuma.png') }}" class="stamp-img">
                            @endif
                        </div>
                        <div class="sig-line">
                            <p class="sig-name">Andreas Wegiq Adia Hendix, S.Pd., M.T.</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
