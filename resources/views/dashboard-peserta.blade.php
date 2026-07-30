@extends('layouts.app')

@push('styles')
<style>
    #peserta-map {
        z-index: 1;
    }
</style>
@endpush

@section('content')

<div class="space-y-8">

    {{-- Welcome --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-sky-500 to-cyan-400 rounded-3xl p-8 shadow-soft">

        <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            <div>
                <p class="text-sky-100 text-sm font-medium mb-2">
                    Selamat datang kembali 👋
                </p>

                <h1 class="text-3xl font-bold text-white leading-tight">
                    Halo, {{ auth()->user()->name }}
                </h1>

                <p class="text-sky-100 mt-2 text-sm max-w-xl">
                    Tetap semangat belajar dan tingkatkan skill terbaikmu bersama MersifLab.
                </p>
            </div>

            <div class="hidden md:flex items-center justify-center">
                <div class="w-24 h-24 rounded-3xl bg-white/15 backdrop-blur-md flex items-center justify-center border border-white/20">
                    <i class="fa-solid fa-graduation-cap text-white text-4xl"></i>
                </div>
            </div>

        </div>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        {{-- Pelatihan --}}
        <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft hover:shadow-md transition-all duration-300">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-400">
                        Pelatihan Diikuti
                    </p>

                    <h2 class="text-3xl font-bold text-slate-800 mt-2">
                        {{ $pendaftarans->count() }}
                    </h2>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center">
                    <i class="fa-solid fa-chalkboard-user text-sky-500 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Terverifikasi --}}
        <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft hover:shadow-md transition-all duration-300">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-400">
                        Terverifikasi
                    </p>

                    <h2 class="text-3xl font-bold text-slate-800 mt-2">
                        {{ $pendaftarans->where('status','terverifikasi')->count() }}
                    </h2>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft hover:shadow-md transition-all duration-300">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-400">
                        Menunggu Verifikasi
                    </p>

                    <h2 class="text-3xl font-bold text-slate-800 mt-2">
                        {{ $pendaftarans->where('status','pending')->count() }}
                    </h2>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-amber-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

        {{-- Pelatihan Saya --}}
        <div class="xl:col-span-3 bg-white rounded-3xl border border-slate-200 shadow-soft overflow-hidden">

            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">

                <div>
                    <h3 class="font-bold text-slate-800 text-lg">
                        Pelatihan Saya
                    </h3>

                    <p class="text-sm text-slate-400 mt-1">
                        Daftar pelatihan yang sedang kamu ikuti
                    </p>
                </div>

                <a href="{{ route('peserta.pelatihan.index', ['terdaftar' => 1]) }}"
                   class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">

                    Lihat Semua →
                </a>
            </div>

            @if($pendaftarans->isEmpty())

                <div class="px-6 py-14 text-center">

                    <div class="w-16 h-16 rounded-3xl bg-sky-50 mx-auto flex items-center justify-center mb-4">
                        <i class="fa-solid fa-book-open text-sky-500 text-2xl"></i>
                    </div>

                    <h4 class="font-semibold text-slate-700">
                        Belum ada pelatihan
                    </h4>

                    <p class="text-sm text-slate-400 mt-1">
                        Yuk mulai daftar pelatihan pertamamu.
                    </p>

                    <a href="{{ route('peserta.pelatihan.index') }}"
                       class="inline-flex items-center px-5 py-2.5 mt-5 bg-sky-500 hover:bg-sky-600 text-white rounded-2xl text-sm font-semibold transition-all">

                        Cari Pelatihan
                    </a>
                </div>

            @else

                <div class="divide-y divide-slate-100">

                    @foreach($pendaftarans->take(3) as $daftar)

                    @php
                        $statusPelatihan   = $daftar->pelatihan->status;
                        $statusPendaftaran = $daftar->status;
                    @endphp

                    <div class="px-6 py-5 hover:bg-slate-50 transition-all duration-200">

                        <div class="flex items-start justify-between gap-4 mb-3">

                            <div class="min-w-0">

                                <h4 class="font-semibold text-slate-800 text-sm truncate">
                                    {{ $daftar->pelatihan->nama }}
                                </h4>

                                <p class="text-xs text-slate-500 mt-1">
                                    Mentor:
                                    {{ $daftar->pelatihan->mentors->pluck('name')->join(', ') ?: '—' }}
                                </p>
                            </div>

                            {{-- Badge --}}
                            @if($statusPendaftaran === 'ditolak')

                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 shrink-0">
                                    Ditolak
                                </span>

                            @elseif($statusPendaftaran === 'pending')

                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 shrink-0">
                                    Menunggu
                                </span>

                            @elseif($statusPelatihan === 'selesai')

                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 shrink-0">
                                    Selesai
                                </span>

                            @else

                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 shrink-0">
                                    Aktif
                                </span>

                            @endif
                        </div>

                        {{-- Progress --}}
                        @if($statusPendaftaran === 'terverifikasi' && $daftar->total_sesi > 0)

                        <div class="flex items-center gap-3">

                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">

                                <div class="h-2 rounded-full bg-gradient-to-r from-sky-500 to-cyan-400"
                                     style="width: {{ $daftar->progress }}%"></div>
                            </div>

                            <span class="text-xs font-medium text-slate-500">
                                {{ $daftar->progress }}%
                            </span>
                        </div>

                        @endif

                    </div>

                    @endforeach

                </div>

            @endif
        </div>

        {{-- Map --}}
        <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-soft overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">

                <h3 class="font-bold text-slate-800 text-lg">
                    Lokasi Pelatihan
                </h3>

                <p class="text-sm text-slate-400 mt-1">
                    Informasi lokasi pelatihan tersedia di sini
                </p>
            </div>

            <div id="peserta-map" class="h-[420px] w-full"></div>
        </div>
    </div>

    {{-- Sesi Berikutnya --}}
    @if($sesiBerikutnya)

    <div class="bg-white rounded-3xl border border-slate-200 shadow-soft overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-100">

            <h3 class="font-bold text-slate-800 text-lg">
                Sesi Berikutnya
            </h3>
        </div>

        <div class="p-6">

            <div class="flex flex-col md:flex-row md:items-center gap-5 bg-sky-50 border border-sky-100 rounded-3xl p-5">

                <div class="w-16 h-16 rounded-2xl bg-sky-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-sky-200">

                    <i class="fa-solid fa-calendar-day text-2xl"></i>
                </div>

                <div class="flex-1">

                    <h4 class="font-bold text-slate-800 text-lg">
                        {{ $sesiBerikutnya->topik ?? 'Sesi Pelatihan' }}
                    </h4>

                    <p class="text-sm text-slate-600 mt-1">
                        {{ $sesiBerikutnya->pelatihan->nama }}
                    </p>

                    <p class="text-sm text-sky-600 font-medium mt-2">

                        <i class="fa-solid fa-clock mr-1"></i>

                        {{ \Carbon\Carbon::parse($sesiBerikutnya->tanggal)->translatedFormat('d F Y') }}

                        @if($sesiBerikutnya->waktu_mulai)
                            · {{ substr($sesiBerikutnya->waktu_mulai,0,5) }} WIB
                        @endif
                    </p>
                </div>

                <a href="{{ route('peserta.aktivitas.index') }}"
                   class="inline-flex items-center justify-center px-5 py-3 bg-white border border-sky-200 text-sky-600 rounded-2xl font-semibold hover:bg-sky-50 transition-all">

                    Detail
                </a>

            </div>
        </div>
    </div>

    @endif

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const locations = {{ Js::from($lokasiPeta) }};

    const defaultLat = locations.length > 0 ? locations[0].lat : -6.2088;
    const defaultLng = locations.length > 0 ? locations[0].lng : 106.8456;

    const map = L.map('peserta-map').setView([defaultLat, defaultLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    locations.forEach(loc => {

        const icon = L.divIcon({
            className: '',
            html: `
                <div style="
                    background:#0ea5e9;
                    width:28px;
                    height:28px;
                    border-radius:50% 50% 50% 0;
                    transform:rotate(-45deg);
                    border:3px solid white;
                    box-shadow:0 4px 12px rgba(14,165,233,0.35)
                "></div>
            `,
            iconSize: [28, 28],
            iconAnchor: [14, 28],
        });

        L.marker([loc.lat, loc.lng], { icon }).addTo(map)
            .bindPopup(`
                <b>${loc.nama}</b><br>
                <span style="font-size:12px;color:#64748b">
                    ${loc.lokasi_nama || ''}
                </span>
            `);
    });
});
</script>
@endpush