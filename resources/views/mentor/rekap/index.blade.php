@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Rekap Peserta</h1>
            <p class="text-sm text-slate-500 mt-0.5">Ringkasan kehadiran dan status tugas</p>
        </div>
        <form method="GET" action="{{ route('mentor.rekap.index') }}">
            <select name="pelatihan_id" onchange="this.form.submit()"
                    class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-600 bg-white shadow-sm">
                @foreach($pelatihans as $p)
                    <option value="{{ $p->id }}" {{ $selectedId == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($pelatihans->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center text-slate-400">
        <i class="fa-solid fa-chalkboard-user text-4xl mb-3"></i>
        <p class="font-medium text-slate-600">Belum ada pelatihan yang diassign</p>
    </div>
    @else

    {{-- Summary Cards --}}
    @php
        $totalPeserta = $rekap->count();
        $jumlahLulus  = $rekap->filter(fn($r) => $r['lulus'])->count();
        $tidakLulus   = $rekap->filter(fn($r) => !$r['lulus'])->count();
        $hadirBaik    = $rekap->filter(fn($r) => $r['persen'] >= 80)->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-users text-sky-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 leading-none">{{ $totalPeserta }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-wider">Total Peserta</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-user-check text-emerald-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 leading-none">{{ $hadirBaik }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-wider">Kehadiran Baik</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-graduation-cap text-purple-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 leading-none">{{ $jumlahLulus }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-wider">Lulus</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-user-xmark text-red-500 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 leading-none">{{ $tidakLulus }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-wider">Tidak Lulus</p>
            </div>
        </div>
    </div>

    {{-- Rekap Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Detail Rekap — {{ $pelatihan?->nama }}</h3>
        </div>
        @if($rekap->isEmpty())
        <div class="px-6 py-12 text-center text-slate-400">
            <i class="fa-solid fa-users text-3xl mb-2"></i>
            <p class="text-sm">Belum ada peserta terverifikasi</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-3.5 text-left font-semibold w-8">#</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Peserta</th>
                        <th class="px-6 py-3.5 text-center font-semibold w-48">Kehadiran</th>
                        <th class="px-6 py-3.5 text-center font-semibold w-32">Tugas Valid</th>
                        <th class="px-6 py-3.5 text-center font-semibold w-36">Tugas Tidak Valid</th>
                        <th class="px-6 py-3.5 text-center font-semibold w-32">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rekap as $r)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-slate-400 text-xs font-medium">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($r['peserta']->name) }}&background=6366f1&color=fff&size=32&bold=true"
                                     class="w-8 h-8 rounded-full shrink-0" alt="">
                                <span class="font-medium text-slate-800">{{ $r['peserta']->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $r['persen'] >= 80 ? 'bg-emerald-500' : ($r['persen'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                                         style="width: {{ $r['persen'] }}%"></div>
                                </div>
                                <span class="text-xs font-semibold w-9 text-right shrink-0 {{ $r['persen'] >= 80 ? 'text-emerald-600' : ($r['persen'] >= 60 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $r['persen'] }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($r['valid'] > 0)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">{{ $r['valid'] }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($r['invalid'] > 0)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-50 text-red-600 text-xs font-bold">{{ $r['invalid'] }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($r['lulus'])
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-graduation-cap text-[10px]"></i> Lulus
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                    <i class="fa-solid fa-xmark text-[10px]"></i> Tidak Lulus
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

</div>
@endsection
