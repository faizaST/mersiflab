@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-800">Riwayat & Sertifikat</h1>
        <p class="text-sm text-slate-500 mt-0.5">Rekap pelatihan dan sertifikat Anda</p>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-chalkboard-user text-sky-600"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                <p class="text-xs text-slate-500">Total Pelatihan</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-graduation-cap text-emerald-600"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800">{{ $stats['lulus'] }}</p>
                <p class="text-xs text-slate-500">Lulus</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-certificate text-amber-600"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800">{{ $stats['sertifikat'] }}</p>
                <p class="text-xs text-slate-500">Sertifikat Diperoleh</p>
            </div>
        </div>
    </div>

    {{-- Riwayat --}}
    @if($riwayat->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center text-slate-400">
        <i class="fa-solid fa-graduation-cap text-4xl mb-3"></i>
        <p class="font-medium text-slate-600">Belum ada riwayat pelatihan</p>
        <a href="{{ route('peserta.pelatihan.index') }}" class="text-sm text-sky-600 hover:underline mt-1 block">Daftar pelatihan sekarang</a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($riwayat as $r)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-purple-600 flex items-center justify-center text-white shrink-0">
                    <i class="fa-solid fa-chalkboard-user text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h3 class="font-semibold text-slate-800">{{ $r['pelatihan']->nama }}</h3>
                        @if($r['daftar']->status === 'terverifikasi')
                            @if($r['lulus'])
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-graduation-cap text-[10px]"></i> Lulus
                                </span>
                            @elseif($r['total_sesi'] > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                    <i class="fa-solid fa-xmark text-[10px]"></i> Tidak Lulus
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Dalam Proses
                                </span>
                            @endif
                        @elseif($r['daftar']->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu Verifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                Ditolak
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500">
                        Mentor: {{ $r['pelatihan']->mentors->pluck('name')->join(', ') ?: '—' }}
                        @if($r['pelatihan']->kategori)
                            @php
                                $katWarna = $r['pelatihan']->kategoriPelatihan?->warna ?? '#64748b';
                            @endphp
                            · <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-sm border border-black/5" style="background-color: {{ $katWarna }}">{{ $r['pelatihan']->kategori }}</span>
                        @endif
                    </p>
                    @if($r['total_sesi'] > 0)
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-32 bg-slate-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $r['persen'] >= 80 ? 'bg-emerald-500' : ($r['persen'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                                 @style(['width' => $r['persen'] . '%'])></div>
                        </div>
                        <span class="text-xs {{ $r['persen'] >= 80 ? 'text-emerald-600' : 'text-amber-600' }} font-semibold">
                            {{ $r['persen'] }}% kehadiran ({{ $r['hadir'] }}/{{ $r['total_sesi'] }} sesi)
                        </span>
                    </div>
                    @endif
                </div>
                <div class="shrink-0">
                    @if($r['sertifikat'])
                        <a href="{{ route('peserta.riwayat.sertifikat.download', $r['sertifikat']->id) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-sky-600 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-xl transition-colors">
                            <i class="fa-solid fa-download"></i> Download Sertifikat
                        </a>
                    @elseif($r['lulus'])
                        <span class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-500 bg-slate-100 rounded-xl">
                            <i class="fa-solid fa-clock"></i> Menunggu Generate
                        </span>
                    @elseif($r['daftar']->status === 'terverifikasi' && $r['total_sesi'] > 0)
                        <span class="text-xs text-slate-400 block text-right">Tidak ada sertifikat</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
