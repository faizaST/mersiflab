@extends('layouts.app')

@section('content')
@php
    $today    = now()->toDateString();
    $sisaKuota = $pelatihan->kuota - $pelatihan->pendaftarans->where('status', 'terverifikasi')->count();
@endphp

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pelatihan.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $pelatihan->nama }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">Detail program pelatihan</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.pelatihan.edit', $pelatihan->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 shadow-sm transition-all">
                <i class="fa-solid fa-pen text-amber-500"></i> Edit
            </a>
            <form action="{{ route('admin.pelatihan.destroy', $pelatihan->id) }}" method="POST"
                  onsubmit="return confirm('Hapus pelatihan ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-red-200 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-50 shadow-sm transition-all">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-4">
        @foreach([
            [$pelatihan->kuota, 'Kuota', 'fa-users', 'sky'],
            [$sisaKuota, 'Sisa Kuota', 'fa-user-plus', 'purple'],
        ] as [$val, $label, $icon, $color])
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                @if($color === 'sky') bg-sky-50 @else bg-purple-50 @endif">
                <i class="fa-solid {{ $icon }} text-sm
                    @if($color === 'sky') text-sky-600 @else text-purple-600 @endif"></i>
            </div>
            <div>
                <p class="text-xl font-extrabold text-slate-800 leading-none">{{ $val }}</p>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">{{ $label }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri (2/3) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Deskripsi & Badge --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex flex-wrap gap-2 mb-4">
    @if($pelatihan->kategoriPelatihan)
        @php
            $katWarna = $pelatihan->kategoriPelatihan?->warna ?? '#64748b';
        @endphp
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white shadow-sm border border-black/5"
              style="background-color: {{ $katWarna }}">
            <i class="fa-solid fa-tag mr-1"></i>{{ $pelatihan->kategoriPelatihan?->nama }}
        </span>
    @endif
                    @if($pelatihan->berbayar)
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-700">
                            <i class="fa-solid fa-money-bill mr-1"></i>Berbayar · Rp {{ number_format($pelatihan->biaya, 0, ',', '.') }}
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                            <i class="fa-solid fa-gift mr-1"></i>Gratis
                        </span>
                    @endif
                    @if($pelatihan->status === 'aktif')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Aktif
                        </span>
                    @elseif($pelatihan->status === 'draft')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Draft
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Selesai
                        </span>
                    @endif
                </div>
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Deskripsi</h3>
                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                    {{ $pelatihan->deskripsi ?? 'Tidak ada deskripsi.' }}
                </p>
                @if($pelatihan->catatan)
                <div class="mt-4 p-3 bg-amber-50 border border-amber-100 rounded-xl">
                    <p class="text-xs font-semibold text-amber-700 mb-1"><i class="fa-solid fa-note-sticky mr-1"></i>Catatan</p>
                    <p class="text-sm text-amber-800 leading-relaxed">{{ $pelatihan->catatan }}</p>
                </div>
                @endif
            </div>

            {{-- Gambar + Lokasi & Peta (digabung) --}}
            @if($pelatihan->gambar || $pelatihan->lokasi_nama || ($pelatihan->lat && $pelatihan->lng))
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                {{-- Gambar --}}
                @if($pelatihan->gambar)
                <div class="border-b border-slate-100">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="font-semibold text-slate-800">Gambar Pelatihan</h3>
                    </div>
                    <img src="{{ Storage::url($pelatihan->gambar) }}"
                         alt="{{ $pelatihan->nama }}"
                         class="w-full max-h-72 object-cover">
                </div>
                @endif

                {{-- Lokasi teks --}}
                @if($pelatihan->lokasi_nama)
                <div class="px-6 py-4 flex items-start gap-3 {{ ($pelatihan->lat && $pelatihan->lng) ? 'border-b border-slate-100' : '' }}">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-location-dot text-emerald-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-0.5">Lokasi</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $pelatihan->lokasi_nama }}</p>
                        @if($pelatihan->lokasi_alamat)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $pelatihan->lokasi_alamat }}</p>
                        @endif
                        @if($pelatihan->lat && $pelatihan->lng)
                            <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ $pelatihan->lat }}, {{ $pelatihan->lng }}</p>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Peta --}}
                @if($pelatihan->lat && $pelatihan->lng)
                <div id="detail-map" class="h-56 w-full"></div>
                @endif

            </div>
            @endif

        </div>{{-- end kolom kiri --}}

        {{-- Kolom Kanan (1/3) --}}
        <div class="space-y-5">

            {{-- Informasi Umum --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <h3 class="font-semibold text-slate-800">Informasi Umum</h3>

                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-users text-sky-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Kuota Peserta</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $pelatihan->kuota }} orang</p>
                        <p class="text-xs text-slate-400">{{ $pelatihan->kuota - $sisaKuota }} terisi · {{ $sisaKuota }} sisa</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-chalkboard-user text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Mentor</p>
                        @if($pelatihan->mentors->isEmpty())
                            <p class="text-sm font-semibold text-slate-400">Belum diassign</p>
                        @else
                            <div class="flex flex-wrap gap-1 mt-0.5">
                                @foreach($pelatihan->mentors as $m)
                                    <span class="text-xs bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full font-medium">{{ $m->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-calendar text-amber-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-slate-500 mb-1.5">Jadwal Sesi</p>
                        @if($pelatihan->sesi->isEmpty())
                            <p class="text-sm text-slate-400">Belum ada jadwal</p>
                        @else
                        <div class="space-y-1.5">
                            @foreach($pelatihan->sesi->sortBy('tanggal') as $sesi)
                            <div class="flex items-start gap-2">
                                <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">
                                    {{ $loop->iteration }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-slate-700 leading-snug">{{ $sesi->topik ?? 'Sesi ' . $loop->iteration }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $sesi->tanggal ? \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('d M Y') : '—' }}
                                        @if($sesi->waktu_mulai)
                                            · {{ substr($sesi->waktu_mulai, 0, 5) }}–{{ substr($sesi->waktu_selesai, 0, 5) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                @if($pelatihan->tenggat_pendaftaran)
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-hourglass-end text-red-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Tenggat Pendaftaran</p>
                        <p class="text-sm font-semibold text-slate-800">
                            {{ $pelatihan->tenggat_pendaftaran->format('d M Y') }}
                        </p>
                        <p class="text-xs {{ $pelatihan->isDeadlinePassed() ? 'text-red-500' : 'text-emerald-600' }}">
                            {{ $pelatihan->isDeadlinePassed() ? 'Sudah lewat' : 'Masih terbuka' }}
                        </p>
                    </div>
                </div>
                @endif

                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-clock text-slate-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Dibuat</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $pelatihan->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-slate-400">Diperbarui {{ $pelatihan->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Pembayaran --}}
            @if($pelatihan->berbayar)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
                <h3 class="font-semibold text-slate-800">Info Pembayaran</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Biaya</span>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($pelatihan->biaya, 0, ',', '.') }}</span>
                    </div>
                    @if($pelatihan->nama_bank)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Bank</span>
                        <span class="font-semibold text-slate-800">{{ $pelatihan->nama_bank }}</span>
                    </div>
                    @endif
                    @if($pelatihan->nomor_rekening)
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. Rekening</span>
                        <span class="font-semibold text-slate-800 font-mono">{{ $pelatihan->nomor_rekening }}</span>
                    </div>
                    @endif
                    @if($pelatihan->atas_nama)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Atas Nama</span>
                        <span class="font-semibold text-slate-800">{{ $pelatihan->atas_nama }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Jadwal Sesi --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Jadwal Sesi</h3>
                    <span class="text-xs text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full font-medium">
                        {{ $pelatihan->sesi->count() }} sesi
                    </span>
                </div>
                @if($pelatihan->sesi->isEmpty())
                    <div class="px-5 py-8 text-center text-slate-400">
                        <i class="fa-solid fa-calendar-xmark text-2xl mb-2"></i>
                        <p class="text-sm">Belum ada jadwal sesi</p>
                    </div>
                @else
                <div class="divide-y divide-slate-100">
                    @foreach($pelatihan->sesi->sortBy('tanggal') as $sesi)
                    <div class="px-5 py-3 flex items-start gap-3 hover:bg-slate-50 transition-colors">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">
                            {{ $loop->iteration }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 leading-snug">{{ $sesi->topik ?? 'Sesi ' . $loop->iteration }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $sesi->tanggal ? \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('d F Y') : '—' }}
                                @if($sesi->waktu_mulai)
                                    · {{ substr($sesi->waktu_mulai, 0, 5) }}–{{ substr($sesi->waktu_selesai, 0, 5) }}
                                @endif
                            </p>
                        </div>
                        @if($sesi->tanggal && $sesi->tanggal < $today)
                            <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full shrink-0">Selesai</span>
                        @elseif($sesi->tanggal && $sesi->tanggal == $today)
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Hari Ini
                            </span>
                        @else
                            <span class="text-[10px] font-semibold text-sky-600 bg-sky-50 px-2 py-0.5 rounded-full shrink-0">Mendatang</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Sertifikat --}}
            @if($pelatihan->sertifikat_template || $pelatihan->sertifikat_logo_kiri || $pelatihan->sertifikat_logo_kanan)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
                <h3 class="font-semibold text-slate-800">Sertifikat</h3>
                <div class="space-y-2 text-sm">
                    @if($pelatihan->sertifikat_template)
                    <div class="flex items-center gap-2 text-emerald-700">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        <span>Template tersedia</span>
                    </div>
                    @endif
                    @if($pelatihan->sertifikat_logo_kiri)
                    <div class="flex items-center gap-2 text-slate-600">
                        <i class="fa-solid fa-image text-xs text-slate-400"></i>
                        <span>Logo kiri tersedia</span>
                    </div>
                    @endif
                    @if($pelatihan->sertifikat_logo_kanan)
                    <div class="flex items-center gap-2 text-slate-600">
                        <i class="fa-solid fa-image text-xs text-slate-400"></i>
                        <span>Logo kanan tersedia</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection

@push('scripts')
@if($pelatihan->lat && $pelatihan->lng)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lat = {{ $pelatihan->lat }};
    const lng = {{ $pelatihan->lng }};
    const map = L.map('detail-map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);
    L.marker([lat, lng])
        .addTo(map)
        .bindPopup('<b>{{ addslashes($pelatihan->nama) }}</b><br>{{ addslashes($pelatihan->lokasi_nama ?? '') }}')
        .openPopup();
});
</script>
@endif
@endpush
