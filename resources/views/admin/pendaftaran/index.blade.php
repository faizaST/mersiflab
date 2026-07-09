@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ 
    modalOpen: false, 
    selectedPeserta: '', 
    buktiUrl: '', 
    imgLoading: true, 
    imgError: false,
    modalNota: false,
    notaData: {},
    openNota(data) {
        this.notaData = data;
        this.modalNota = true;
    }
}">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pendaftaran</h1>
            <p class="text-sm text-slate-500 mt-0.5">Verifikasi dan kelola pendaftaran peserta</p>
        </div>
        @if($stats['pending'] > 0)
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full border border-amber-200">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            {{ $stats['pending'] }} Menunggu Verifikasi
        </span>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['terverifikasi'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">Terverifikasi</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $stats['ditolak'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">Ditolak</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.pendaftaran.index') }}"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama peserta..."
                   class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
        </div>
        <select name="pelatihan_id"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-600">
            <option value="">Semua Pelatihan</option>
            @foreach($pelatihans as $p)
                <option value="{{ $p->id }}" {{ request('pelatihan_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->nama }}
                </option>
            @endforeach
        </select>
        <select name="status"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-600">
            <option value="">Semua Status</option>
            <option value="pending"       {{ request('status') === 'pending'       ? 'selected' : '' }}>Pending</option>
            <option value="terverifikasi" {{ request('status') === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
            <option value="ditolak"       {{ request('status') === 'ditolak'       ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit"
                class="px-4 py-2 text-sm font-semibold text-white rounded-xl transition-all"
                style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 12px rgba(56,189,248,0.30);">
            <i class="fa-solid fa-filter mr-1"></i>Filter
        </button>
        @if(request()->hasAny(['search','pelatihan_id','status']))
        <a href="{{ route('admin.pendaftaran.index') }}"
           class="px-4 py-2 text-sm font-medium text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
            <i class="fa-solid fa-xmark mr-1"></i>Reset
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-3.5 text-left font-semibold">Peserta</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Pelatihan</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Tanggal Daftar</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Bukti Bayar</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Status</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendaftarans as $daftar)
                    @php
                        $namaPeserta  = $daftar->peserta->name;
                        $buktiRoute   = $daftar->bukti_bayar
                                        ? route('admin.pendaftaran.bukti', $daftar->id)
                                        : '';
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">

                        {{-- Peserta --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($namaPeserta) }}&background=6366f1&color=fff&size=32&bold=true"
                                     class="w-8 h-8 rounded-full" alt="">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $namaPeserta }}</p>
                                    <p class="text-xs text-slate-400">{{ $daftar->peserta->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Pelatihan --}}
                        <td class="px-6 py-4 text-slate-600 text-xs">{{ $daftar->pelatihan->nama }}</td>

                        {{-- Tanggal --}}
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $daftar->created_at->format('d M Y') }}</td>

                        {{-- Bukti Bayar --}}
                        <td class="px-6 py-4">
                            @if($daftar->pelatihan->berbayar)
                                @if($daftar->bukti_bayar)
                                    <button
                                        x-on:click="selectedPeserta = '{{ e($namaPeserta) }}'; buktiUrl = '{{ $buktiRoute }}'; imgLoading = true; imgError = false; modalOpen = true"
                                        class="inline-flex items-center gap-1.5 text-xs text-sky-600 hover:text-sky-700 font-medium hover:underline">
                                        <i class="fa-solid fa-image"></i> Lihat Bukti
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                                        <i class="fa-solid fa-clock text-[10px]"></i> Belum upload
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full font-medium">
                                    <i class="fa-solid fa-check text-[10px]"></i> Gratis
                                </span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            @if($daftar->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Pending
                                </span>
                            @elseif($daftar->status === 'terverifikasi')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Ditolak
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">
                            @if($daftar->status === 'pending')
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.pendaftaran.verifikasi', $daftar->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                                            <i class="fa-solid fa-check mr-1"></i>Verifikasi
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.pendaftaran.tolak', $daftar->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 text-xs font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                                            <i class="fa-solid fa-xmark mr-1"></i>Tolak
                                        </button>
                                    </form>
                                </div>
                            @elseif($daftar->status === 'ditolak')
                                <form action="{{ route('admin.pendaftaran.verifikasi', $daftar->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-rotate-left mr-1"></i>Verifikasi Ulang
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center gap-2">
                                    @if($daftar->pelatihan->berbayar)
                                    <button x-on:click="openNota({
                                        id: '{{ str_pad($daftar->id, 5, '0', STR_PAD_LEFT) }}',
                                        nama: '{{ e($daftar->pelatihan->nama) }}',
                                        kategori: '{{ $daftar->pelatihan->kategori }}',
                                        biaya: '{{ number_format($daftar->pelatihan->biaya, 0, ',', '.') }}',
                                        tanggal: '{{ $daftar->updated_at->format('d F Y') }}',
                                        peserta: '{{ e($namaPeserta) }}',
                                        email: '{{ $daftar->peserta->email }}'
                                    })" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors uppercase">
                                        <i class="fa-solid fa-file-invoice"></i> Lihat Nota
                                    </button>
                                    @endif
                                    <form action="{{ route('admin.pendaftaran.batal-verifikasi', $daftar->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan verifikasi pendaftaran untuk {{ e($namaPeserta) }}? Semua sertifikat dan presensi terkait pelatihan ini juga akan dihapus.')"
                                          class="inline-block">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                                            <i class="fa-solid fa-rotate-left"></i> Batal Verifikasi
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-2 text-slate-400">
                                <i class="fa-solid fa-clipboard-list text-4xl"></i>
                                <p class="font-medium text-slate-600">
                                    @if(request()->hasAny(['search','pelatihan_id','status']))
                                        Tidak ada data sesuai filter
                                    @else
                                        Belum ada pendaftaran
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-slate-100 text-xs text-slate-400">
            Menampilkan {{ $pendaftarans->count() }} pendaftaran
        </div>
    </div>

    {{-- Modal Preview Bukti --}}
    <div x-show="modalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
             x-on:click="modalOpen = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-semibold text-slate-800">Bukti Pembayaran</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Peserta: <span class="font-medium text-slate-700" x-text="selectedPeserta"></span>
                    </p>
                </div>
                <button x-on:click="modalOpen = false"
                        class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Image area --}}
            <div class="p-4">
                <div class="relative w-full min-h-48 bg-slate-100 rounded-xl overflow-hidden border border-slate-200 flex items-center justify-center">

                    <div x-show="imgLoading"
                         class="absolute inset-0 flex items-center justify-center bg-slate-100">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-slate-400"></i>
                    </div>

                    <img x-bind:src="buktiUrl"
                         x-show="!imgError"
                         x-on:load="imgLoading = false"
                         x-on:error="imgLoading = false; imgError = true"
                         class="w-full max-h-96 object-contain"
                         alt="Bukti Pembayaran">

                    <div x-show="imgError" class="text-center text-slate-400 p-8">
                        <i class="fa-solid fa-file-pdf text-4xl text-red-400 mb-2"></i>
                        <p class="text-sm font-medium text-slate-600">File tidak dapat ditampilkan sebagai gambar</p>
                        <a x-bind:href="buktiUrl" target="_blank"
                           class="mt-3 inline-flex items-center gap-1.5 text-sm text-sky-600 hover:underline font-medium">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka di tab baru
                        </a>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 pb-5 flex items-center justify-between gap-3">
                <a x-bind:href="buktiUrl" target="_blank"
                   class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-slate-800 font-medium">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Buka di tab baru
                </a>
                <button x-on:click="modalOpen = false"
                        class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════
         MODAL 2: Lihat Nota Konfirmasi
    ══════════════════════════════════════ --}}
    <div x-show="modalNota" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" x-on:click="modalNota = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg z-10 overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            {{-- Header Nota --}}
            <div class="bg-sky-600 px-8 py-6 text-white relative"
                 style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%) !important;">
                <button x-on:click="modalNota = false" class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                        <i class="fa-solid fa-receipt text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Nota Konfirmasi</h3>
                        <p class="text-sky-100 text-xs">MersifLab Official Receipt</p>
                    </div>
                </div>
                <div class="absolute -bottom-4 right-8 bg-emerald-500 text-white text-[10px] font-bold px-4 py-1.5 rounded-full shadow-lg uppercase tracking-wider">
                    Lunas / Terverifikasi
                </div>
            </div>

            <div class="px-8 pt-10 pb-8 space-y-6">
                {{-- Info Utama --}}
                <div class="grid grid-cols-2 gap-4 pb-6 border-b border-slate-100">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">No. Transaksi</p>
                        <p class="text-sm font-bold text-slate-800" x-text="'#TRX-' + notaData.id"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">Tanggal Verifikasi</p>
                        <p class="text-sm font-bold text-slate-800" x-text="notaData.tanggal"></p>
                    </div>
                </div>

                {{-- Detail Peserta --}}
                <div class="space-y-3">
                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Detail Peserta</p>
                    <div class="bg-slate-50 rounded-2xl p-4 flex items-center gap-3 border border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center text-sky-600 font-bold" x-text="notaData.peserta ? notaData.peserta[0] : ''"></div>
                        <div>
                            <p class="text-sm font-bold text-slate-800" x-text="notaData.peserta"></p>
                            <p class="text-xs text-slate-500" x-text="notaData.email"></p>
                        </div>
                    </div>
                </div>

                {{-- Detail Pelatihan --}}
                <div class="space-y-3">
                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Detail Pelatihan</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600" x-text="notaData.nama"></span>
                            <span class="text-sm font-bold text-slate-800" x-text="'Rp ' + notaData.biaya"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-400">
                            <span x-text="notaData.kategori"></span>
                            <span>Sudah Terbayar</span>
                        </div>
                    </div>
                </div>

                {{-- Total --}}
                <div class="pt-4 border-t-2 border-dashed border-slate-100 flex justify-between items-center">
                    <span class="text-base font-bold text-slate-800">Total Pembayaran</span>
                    <span class="text-2xl font-black text-sky-600" x-text="'Rp ' + notaData.biaya"></span>
                </div>

                {{-- Footer / Download Option --}}
                <div class="flex items-center justify-between gap-3 pt-4">
                    <a :href="'{{ url('admin/nota') }}/' + parseInt(notaData.id) + '/download'"
                       class="flex-1 inline-flex items-center justify-center gap-2 py-3 text-sm font-bold text-sky-600 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-2xl transition-all">
                        <i class="fa-solid fa-download"></i> Download PDF
                    </a>
                    <button x-on:click="modalNota = false"
                            class="flex-1 py-3 text-sm font-bold text-white bg-slate-800 hover:bg-slate-900 rounded-2xl transition-all shadow-lg shadow-slate-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
