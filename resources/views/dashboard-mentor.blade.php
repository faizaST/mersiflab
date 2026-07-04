@extends('layouts.app')

@section('content')

<div class="space-y-8">

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-500 to-cyan-400 p-8 shadow-soft">

        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            <div>
                <p class="text-sky-100 text-sm font-medium mb-2">
                    Dashboard Mentor
                </p>

                <h1 class="text-3xl font-bold text-white">
                    Selamat datang, {{ auth()->user()->name }} 👋
                </h1>

                <p class="text-sky-100 mt-2 text-sm max-w-xl">
                    Kelola pelatihan, pantau kehadiran peserta, dan lihat jadwal sesi hari ini.
                </p>
            </div>

            <div class="hidden md:flex items-center justify-center">
                <div class="w-24 h-24 rounded-3xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center">
                    <i class="fa-solid fa-chalkboard-user text-white text-4xl"></i>
                </div>
            </div>

        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        {{-- Pelatihan --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-soft p-5 hover:shadow-md transition-all duration-300">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-400 font-medium">
                        Pelatihan Diampu
                    </p>

                    <h2 class="text-3xl font-bold text-slate-800 mt-2">
                        {{ $pelatihans->count() }}
                    </h2>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center">
                    <i class="fa-solid fa-chalkboard-user text-sky-500 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Peserta Hadir --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-soft p-5 hover:shadow-md transition-all duration-300">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-400 font-medium">
                        Peserta Hadir Hari Ini
                    </p>

                    <h2 class="text-3xl font-bold text-slate-800 mt-2">
                        {{ $hadirHariIni }}
                    </h2>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
                    <i class="fa-solid fa-user-check text-emerald-500 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Sesi Hari Ini --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-soft p-5 hover:shadow-md transition-all duration-300">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-400 font-medium">
                        Sesi Hari Ini
                    </p>

                    <h2 class="text-3xl font-bold text-slate-800 mt-2">
                        {{ $sesiHariIni->count() }}
                    </h2>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-amber-500 text-xl"></i>
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
                        Daftar pelatihan yang sedang Anda ampu
                    </p>
                </div>

                <a href="{{ route('mentor.presensi.index') }}"
                   class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">

                    Kelola →
                </a>
            </div>

            @if($pelatihans->isEmpty())

            <div class="px-6 py-14 text-center">

                <div class="w-16 h-16 rounded-3xl bg-sky-50 mx-auto flex items-center justify-center mb-4">
                    <i class="fa-solid fa-chalkboard-user text-sky-500 text-2xl"></i>
                </div>

                <h4 class="font-semibold text-slate-700">
                    Belum ada pelatihan
                </h4>

                <p class="text-sm text-slate-400 mt-1">
                    Saat ini belum ada pelatihan yang diassign.
                </p>
            </div>

            @else

            <div class="divide-y divide-slate-100">

                @foreach($pelatihans as $p)

                <div class="px-6 py-5 hover:bg-slate-50 transition-all duration-200">

                    <div class="flex items-center gap-4">

                        <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-book-open text-sky-500"></i>
                        </div>

                        <div class="flex-1 min-w-0">

                            <h4 class="font-semibold text-slate-800 text-sm truncate">
                                {{ $p->nama }}
                            </h4>

                            <p class="text-xs text-slate-500 mt-1">
                                {{ $p->kuota }} kuota · {{ $p->sesi->count() }} sesi
                            </p>
                        </div>

                        {{-- Status --}}
                        @if($p->status === 'aktif')

                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Aktif
                        </span>

                        @else

                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 shrink-0">
                            {{ ucfirst($p->status) }}
                        </span>

                        @endif
                    </div>

                </div>

                @endforeach

            </div>

            @endif
        </div>

        {{-- Jadwal Hari Ini --}}
        <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-soft overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">

                <h3 class="font-bold text-slate-800 text-lg">
                    Jadwal Hari Ini
                </h3>

                <p class="text-sm text-slate-400 mt-1">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            @if($sesiHariIni->isEmpty())

            <div class="px-6 py-14 text-center">

                <div class="w-16 h-16 rounded-3xl bg-slate-100 mx-auto flex items-center justify-center mb-4">
                    <i class="fa-solid fa-calendar-xmark text-slate-400 text-2xl"></i>
                </div>

                <h4 class="font-semibold text-slate-700">
                    Tidak ada jadwal
                </h4>

                <p class="text-sm text-slate-400 mt-1">
                    Tidak ada sesi pelatihan hari ini.
                </p>
            </div>

            @else

            <div class="divide-y divide-slate-100">

                @foreach($sesiHariIni as $sesi)

                <div class="px-6 py-5 hover:bg-slate-50 transition-all duration-200">

                    <div class="flex gap-4">

                        {{-- Time Badge --}}
                        <div class="shrink-0">

                            <div class="bg-sky-50 text-sky-600 text-xs font-bold px-3 py-2 rounded-2xl whitespace-nowrap">
                                {{ substr($sesi->waktu_mulai,0,5) }}
                                —
                                {{ substr($sesi->waktu_selesai,0,5) }}
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1">

                            <h4 class="font-semibold text-slate-800 text-sm">
                                {{ $sesi->pelatihan->nama }}
                            </h4>

                            <p class="text-xs text-slate-500 mt-1">
                                {{ $sesi->topik ?? 'Sesi ' . $loop->iteration }}
                            </p>

                            <a href="{{ route('mentor.presensi.index') }}"
                               class="inline-flex items-center gap-2 mt-3 text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">

                                <i class="fa-solid fa-clipboard-check text-xs"></i>

                                Buka Presensi
                            </a>
                        </div>

                    </div>

                </div>

                @endforeach

            </div>

            @endif
        </div>
    </div>

</div>

@endsection