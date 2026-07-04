@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Desain Sertifikat Pelatihan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola template latar belakang, logo kiri, dan logo kanan sertifikat untuk masing-masing program pelatihan</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
        <i class="fa-solid fa-circle-check shrink-0"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Search Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.desain-sertifikat.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama pelatihan..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 transition">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" 
                        class="flex-1 sm:flex-initial inline-flex justify-center items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition"
                        style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 12px rgba(56,189,248,0.30);">
                    Filter
                </button>
                @if(request('search'))
                <a href="{{ route('admin.desain-sertifikat.index') }}" 
                   class="flex-1 sm:flex-initial inline-flex justify-center items-center gap-2 px-4 py-2.5 border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Training Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($pelatihans as $pelatihan)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition group">
            
            {{-- Image / Header --}}
            <div class="h-44 bg-slate-100 relative overflow-hidden shrink-0">
                @if($pelatihan->sertifikat_template)
                    <img src="{{ Storage::url($pelatihan->sertifikat_template) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-300">
                        <i class="fa-solid fa-certificate text-5xl"></i>
                    </div>
                @endif
                
                {{-- Status Badge --}}
                <div class="absolute top-3 right-3">
                    @if($pelatihan->status == 'aktif')
                        <span class="text-xs font-semibold bg-emerald-500 text-white px-2.5 py-1 rounded-full shadow-sm">Aktif</span>
                    @elseif($pelatihan->status == 'draft')
                        <span class="text-xs font-semibold bg-amber-500 text-white px-2.5 py-1 rounded-full shadow-sm">Draft</span>
                    @else
                        <span class="text-xs font-semibold bg-slate-500 text-white px-2.5 py-1 rounded-full shadow-sm">Selesai</span>
                    @endif
                </div>
            </div>

            {{-- Body Info --}}
            <div class="p-5 flex-1 flex flex-col">
                <div class="mb-4">
                    <h3 class="font-bold text-slate-800 text-base line-clamp-1 mb-1" title="{{ $pelatihan->nama }}">{{ $pelatihan->nama }}</h3>
                    @if($pelatihan->kategori)
                        @php
                            $katWarna = $pelatihan->kategoriPelatihan?->warna ?? '#64748b';
                        @endphp
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-sm border border-black/5" style="background-color: {{ $katWarna }}">
                            {{ $pelatihan->kategori }}
                        </span>
                    @else
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold text-slate-500 bg-slate-100 shadow-sm border border-black/5">
                            Umum
                        </span>
                    @endif
                </div>

                {{-- Customization Status --}}
                <div class="space-y-2.5 border-t border-slate-100 pt-4 flex-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400 font-medium">Background Sertifikat</span>
                        @if($pelatihan->sertifikat_template)
                            <span class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Kustom Gambar
                            </span>
                        @else
                            <span class="font-semibold text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                Default (MersifLab)
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400 font-medium">Logo Kiri (Header)</span>
                        @if($pelatihan->sertifikat_logo_kiri)
                            <span class="font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-100 flex items-center gap-1">
                                <i class="fa-solid fa-image"></i> Logo Kustom
                            </span>
                        @else
                            <span class="font-semibold text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                Default (Enuma)
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400 font-medium">Logo Kanan (Header)</span>
                        @if($pelatihan->sertifikat_logo_kanan)
                            <span class="font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-100 flex items-center gap-1">
                                <i class="fa-solid fa-image"></i> Logo Kustom
                            </span>
                        @else
                            <span class="font-semibold text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                Default (MersifLab)
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Action --}}
                <div class="mt-5 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.desain-sertifikat.edit', $pelatihan->id) }}" 
                       class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-slate-50 hover:bg-sky-50 hover:text-sky-700 border border-slate-200 hover:border-sky-200 text-slate-700 text-xs font-bold rounded-xl transition-all shadow-sm">
                        <i class="fa-solid fa-palette text-sm"></i>
                        Kelola Desain Sertifikat
                    </a>
                </div>

            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
            <div class="flex flex-col items-center gap-3">
                <i class="fa-solid fa-award text-5xl text-slate-300"></i>
                <p class="font-medium text-slate-600 text-base">Tidak ada program pelatihan yang ditemukan.</p>
                <p class="text-xs text-slate-400">Silakan tambahkan program pelatihan baru atau sesuaikan kata kunci pencarian Anda.</p>
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection
