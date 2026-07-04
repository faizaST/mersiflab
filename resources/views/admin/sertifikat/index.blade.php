@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Sertifikat & Laporan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Generate sertifikat dan unduh laporan pelatihan</p>
        </div>
        @if($selectedId)
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sertifikat.laporan-pdf', ['pelatihan_id' => $selectedId]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 shadow-sm transition-all">
                <i class="fa-solid fa-eye text-sky-500"></i> Lihat
            </a>
            <a href="{{ route('admin.sertifikat.laporan-pdf', ['pelatihan_id' => $selectedId, 'download' => 1]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow-sm transition-all"
               style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 14px rgba(56,189,248,0.35);">
                <i class="fa-solid fa-download"></i> Unduh Laporan
            </a>
        </div>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-graduation-cap text-emerald-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['lulus'] }}</p>
                <p class="text-xs text-slate-500">Peserta Lulus</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-certificate text-sky-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['diterbitkan'] }}</p>
                <p class="text-xs text-slate-500">Sertifikat Diterbitkan</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-clock text-amber-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['menunggu'] }}</p>
                <p class="text-xs text-slate-500">Menunggu Generate</p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.sertifikat.index') }}"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-center">
        <select name="pelatihan_id" onchange="this.form.submit()"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-600">
            <option value="">Semua Pelatihan</option>
            @foreach($pelatihans as $p)
                <option value="{{ $p->id }}" {{ $selectedId == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
        @if($selectedId)
        <a href="{{ route('admin.sertifikat.index') }}"
           class="text-sm text-slate-500 hover:text-slate-700 px-3 py-2 bg-slate-100 rounded-xl transition-colors">
            <i class="fa-solid fa-xmark mr-1"></i>Reset
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Daftar Peserta Lulus</h3>
        </div>
        @if($data->where('lulus', true)->isEmpty())
        <div class="px-6 py-16 text-center text-slate-400">
            <i class="fa-solid fa-graduation-cap text-4xl mb-3"></i>
            <p class="font-medium text-slate-600">Belum ada peserta yang lulus</p>
            <p class="text-sm mt-1">Peserta lulus jika kehadiran ≥80% dan semua tugas valid</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-3.5 text-left font-semibold">Peserta</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Pelatihan</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Kehadiran</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Sertifikat</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($data->where('lulus', true) as $d)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($d['peserta']->name) }}&background=6366f1&color=fff&size=32&bold=true"
                                     class="w-8 h-8 rounded-full" alt="">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $d['peserta']->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $d['peserta']->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-xs">{{ $d['pelatihan']->nama }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-slate-100 rounded-full h-1.5">
                                    <div class="bg-emerald-500 h-1.5 rounded-full" @style(['width' => $d['persen'] . '%'])></div>
                                </div>
                                <span class="text-xs font-semibold text-emerald-600">{{ $d['persen'] }}%</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $d['hadir'] }}/{{ $d['total_sesi'] }} sesi</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($d['sertifikat'])
                                <div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        <i class="fa-solid fa-check text-[10px]"></i> Diterbitkan
                                    </span>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $d['sertifikat']->nomor_sertifikat }}</p>
                                    <p class="text-xs text-slate-400">{{ $d['sertifikat']->diterbitkan_at?->format('d M Y') }}</p>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Belum Generate
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if(!$d['sertifikat']) 
                                <form action="{{ route('admin.sertifikat.generate', $d['daftar']->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition-all"
                                            style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 3px 10px rgba(56,189,248,0.30);">
                                        <i class="fa-solid fa-certificate"></i> Generate PDF
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('admin.sertifikat.download', $d['sertifikat']->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-download"></i> Unduh PDF
                                </a>
                                <form action="{{ route('admin.sertifikat.generate', $d['daftar']->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"
                                            title="Generate ulang">
                                        <i class="fa-solid fa-rotate"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
