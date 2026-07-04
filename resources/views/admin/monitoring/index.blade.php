@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Monitoring</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pantau kehadiran dan status kelulusan peserta</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
            <i class="fa-solid fa-circle-info text-sky-500"></i>
            Lulus jika kehadiran ≥ 80% & minimal satu tugas valid
        </div>
    </div>

    {{-- Filter --}}
    <form action="{{ route('admin.monitoring.index') }}" method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" placeholder="Cari peserta..." value="{{ request('search') }}"
                   class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
        </div>
        <select name="pelatihan_id" onchange="this.form.submit()"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-600">
            <option value="">Semua Pelatihan</option>
            @foreach($pelatihans as $p)
                <option value="{{ $p->id }}" {{ $selectedId == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-3.5 text-left font-semibold">Peserta</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Pelatihan</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Kehadiran</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Tugas</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Syarat</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Status Kelulusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($monitoring as $m)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($m['peserta']->name) }}&background=6366f1&color=fff&size=32&bold=true"
                                     class="w-8 h-8 rounded-full" alt="">
                                <span class="font-medium text-slate-800">{{ $m['peserta']->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-xs">{{ $m['pelatihan']->nama }}</td>
                        <td class="px-6 py-4 w-48">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $m['persen_hadir'] >= 80 ? 'bg-emerald-500' : ($m['persen_hadir'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}" @style(['width' => $m['persen_hadir'] . '%'])></div>
                                </div>
                                <span class="text-xs font-semibold w-10 text-right {{ $m['persen_hadir'] >= 80 ? 'text-emerald-600' : ($m['persen_hadir'] >= 60 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $m['persen_hadir'] }}%
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $m['hadir'] }}/{{ $m['total_sesi'] }} sesi</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium {{ $m['tugas_tidak_valid'] === 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $m['tugas_valid'] }} valid
                            </span>
                            @if($m['tugas_tidak_valid'] > 0)
                                <span class="text-xs text-red-500 ml-1">/ {{ $m['tugas_tidak_valid'] }} tidak valid</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($m['memenuhi_syarat'])
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-check text-[10px]"></i> Memenuhi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                    <i class="fa-solid fa-xmark text-[10px]"></i> Belum
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($m['lulus'])
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-graduation-cap text-[10px]"></i> Lulus
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Belum Lulus
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-2 text-slate-400">
                                <i class="fa-solid fa-chart-line text-4xl"></i>
                                <p class="font-medium text-slate-600">Belum ada data monitoring</p>
                                <p class="text-sm">Data akan muncul setelah peserta terverifikasi dan presensi dicatat</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
