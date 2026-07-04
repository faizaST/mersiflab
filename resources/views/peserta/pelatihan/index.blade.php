@extends('layouts.app')

@section('content')
<div class="space-y-6"
     x-data="{
        filterStatus: 'semua',
        modalDaftar: false,
        modalBukti: false,
        modalLihat: false,
        selectedId: null,
        selectedNama: '',
        selectedBerbayar: false,
        selectedBank: '',
        selectedRekening: '',
        selectedAtasNama: '',
        selectedBiaya: '',
        selectedPendaftaranId: null,
        buktiUrl: '',
        imgLoading: true,
        imgError: false,
        fileName: '',
        openDaftar(id, nama, berbayar, bank, rek, atasNama, biaya) {
            this.selectedId = id;
            this.selectedNama = nama;
            this.selectedBerbayar = berbayar;
            this.selectedBank = bank;
            this.selectedRekening = rek;
            this.selectedAtasNama = atasNama;
            this.selectedBiaya = biaya;
            this.modalDaftar = true;
        },        openUpload(pendaftaranId, nama, bank, rek, atasNama, biaya) {
            this.selectedPendaftaranId = pendaftaranId;
            this.selectedNama = nama;
            this.selectedBank = bank;
            this.selectedRekening = rek;
            this.selectedAtasNama = atasNama;
            this.selectedBiaya = biaya;
            this.fileName = '';
            this.modalBukti = true;
        },
        openLihat(url) {
            this.buktiUrl = url;
            this.imgLoading = true;
            this.imgError = false;
            this.modalLihat = true;
        },
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
            <h1 class="text-2xl font-bold text-slate-800">
                {{ $hanyaTerdaftar ? 'Pelatihan Saya' : 'Pelatihan' }}
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ $hanyaTerdaftar ? 'Pelatihan yang sudah kamu daftarkan' : 'Temukan dan daftar program pelatihan' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($hanyaTerdaftar)
            <a href="{{ route('peserta.pelatihan.index') }}"
               class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition-colors">
                <i class="fa-solid fa-grid-2 text-xs"></i> Semua Pelatihan
            </a>
            @endif
            {{-- Filter chips --}}
            <div class="flex gap-1 bg-slate-100 p-1 rounded-xl">
            <button x-on:click="filterStatus = 'semua'"
                    :class="filterStatus === 'semua' ? 'bg-white shadow-sm text-slate-800 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                    class="px-3 py-1.5 text-xs rounded-lg transition-all">Semua</button>
            <button x-on:click="filterStatus = 'aktif'"
                    :class="filterStatus === 'aktif' ? 'bg-white shadow-sm text-slate-800 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                    class="px-3 py-1.5 text-xs rounded-lg transition-all">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>Aktif
            </button>
            <button x-on:click="filterStatus = 'selesai'"
                    :class="filterStatus === 'selesai' ? 'bg-white shadow-sm text-slate-800 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                    class="px-3 py-1.5 text-xs rounded-lg transition-all">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block mr-1"></span>Selesai
            </button>
        </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
        <i class="fa-solid fa-circle-check shrink-0"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <i class="fa-solid fa-circle-exclamation shrink-0"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Pelatihan Tersedia --}}
    @if($pelatihans->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center text-slate-400">
        <i class="fa-solid fa-chalkboard-user text-4xl mb-3"></i>
        <p class="font-medium text-slate-600">Belum ada pelatihan tersedia</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($pelatihans as $p)
        @php
            $daftar      = $pendaftarans->get($p->id);
            $sudahDaftar = (bool) $daftar;
            $pNama       = e($p->nama);
            $pBank       = e($p->nama_bank ?? '');
            $pRekening   = e($p->nomor_rekening ?? '');
            $pAtasNama   = e($p->atas_nama ?? '');
            $pBiaya      = $p->berbayar ? 'Rp ' . number_format($p->biaya, 0, ',', '.') : 'Gratis';
            $urlBukti    = $daftar && $daftar->bukti_bayar ? Storage::url($daftar->bukti_bayar) : '';
        @endphp
        <div class="bg-white rounded-2xl border shadow-sm hover:shadow-md transition-all duration-200 flex flex-col overflow-hidden
                    {{ $p->status === 'selesai' ? 'border-slate-300 opacity-80' : 'border-slate-200' }}"
             x-show="filterStatus === 'semua' || filterStatus === '{{ $p->status }}'"
             x-transition>
            <div class="h-2 {{ $p->status === 'selesai' ? 'bg-gradient-to-r from-slate-400 to-slate-500' : 'bg-gradient-to-r from-sky-500 to-purple-500' }}"></div>
            @if($p->gambar)
                <div class="h-40 w-full overflow-hidden">
                    <img src="{{ Storage::url($p->gambar) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $p->nama }}">
                </div>
            @endif
            <div class="p-5 flex-1 flex flex-col gap-3">

                {{-- Badges --}}
                <div class="flex items-start justify-between gap-2">
                    @if($p->kategori)
                        @php
                            $katWarna = $p->kategoriPelatihan?->warna ?? '#64748b';
                        @endphp
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full text-white shadow-sm border border-black/5"
                              style="background-color: {{ $katWarna }}">
                            {{ $p->kategori }}
                        </span>
                    @else
                        <span></span>
                    @endif
                    <div class="flex items-center gap-1.5 shrink-0">
                        {{-- Status pelatihan --}}
                        @if($p->status === 'selesai')
                            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">Selesai</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Aktif
                            </span>
                        @endif
                        {{-- Tipe biaya --}}
                        @if(!$p->berbayar)
                            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">Gratis</span>
                        @else
                            <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-full">Berbayar</span>
                        @endif
                    </div>
                </div>

                {{-- Info --}}
                <div>
                    <h3 class="font-semibold text-slate-800 text-base leading-snug">{{ $p->nama }}</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        <i class="fa-solid fa-user-tie mr-1"></i>{{ $p->mentors->pluck('name')->join(', ') ?: 'Belum ada mentor' }}
                    </p>
                </div>

                @if($p->deskripsi)
                <p class="text-xs text-slate-500 line-clamp-2">{{ $p->deskripsi }}</p>
                @endif

                <div class="space-y-1 text-xs text-slate-500">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-users w-3.5 text-slate-400"></i>
                        <span class="{{ $p->peserta_count >= $p->kuota ? 'text-red-500 font-semibold' : '' }}">
                            Kuota: {{ $p->kuota }} peserta
                        </span>
                    </div>
                    @if($p->lokasi_nama)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-map-pin w-3.5 text-slate-400"></i>
                        <span>{{ $p->lokasi_nama }}</span>
                    </div>
                    @endif

                    @if($p->tenggat_pendaftaran)
                    @php
                        $deadline = $p->tenggat_pendaftaran;
                        $isPassed = $p->isDeadlinePassed();
                        
                        $diffText = '';
                        $isClose = false;

                        if (!$isPassed) {
                            $diffText = \Carbon\Carbon::parse($deadline)->diffForHumans(null, false, false, 1);
                            $isClose = now()->diffInHours($deadline) < 48;
                        }
                    @endphp
                    <div class="flex items-center gap-2 {{ $isClose ? 'text-amber-600 font-semibold' : ($isPassed ? 'text-red-600 font-semibold' : '') }}">
                        <i class="fa-solid fa-clock w-3.5 {{ $isClose ? 'text-amber-500' : ($isPassed ? 'text-red-500' : 'text-slate-400') }}"></i>
                        <span>
                            @if($isPassed)
                                Pendaftaran Berakhir
                            @else
                                Batas Daftar: {{ $p->tenggat_pendaftaran->format('d M Y, H:i') }}
                                ({{ $diffText }})
                            @endif
                        </span>
                    </div>
                    @endif
                </div>

                @if($p->catatan)
                <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl">
                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-1">NB / Catatan:</p>
                    <p class="text-[11px] text-amber-700 leading-relaxed">{{ $p->catatan }}</p>
                </div>
                @endif

                {{-- Status Pendaftaran --}}
                @if($sudahDaftar)
                <div class="border-t border-slate-100 pt-3 space-y-2">
                    {{-- Status badge --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">Status Pendaftaran</span>
                        @if($daftar->status === 'terverifikasi')
                            <div class="flex flex-col items-end gap-1">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-check text-[10px]"></i> Terverifikasi
                                </span>
                                @if($p->berbayar)
                                <button x-on:click="openNota({
                                    id: '{{ str_pad($daftar->id, 5, '0', STR_PAD_LEFT) }}',
                                    nama: '{{ $pNama }}',
                                    kategori: '{{ $p->kategori }}',
                                    biaya: '{{ number_format($p->biaya, 0, ',', '.') }}',
                                    tanggal: '{{ $daftar->updated_at->format('d F Y') }}',
                                    peserta: '{{ Auth::user()->name }}',
                                    email: '{{ Auth::user()->email }}'
                                })" class="text-[10px] text-emerald-600 hover:underline font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-file-invoice"></i> Lihat Nota Konfirmasi
                                </button>
                                @endif
                            </div>
                        @elseif($daftar->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu Verifikasi
                            </span>
                        @elseif($daftar->status === 'ditolak')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                <i class="fa-solid fa-xmark text-[10px]"></i> Ditolak
                            </span>
                        @endif
                    </div>
                    @if($daftar->status === 'ditolak' && $daftar->catatan_admin)
                    <div class="flex items-start gap-2 p-2.5 bg-red-50 border border-red-100 rounded-xl">
                        <i class="fa-solid fa-circle-exclamation text-red-400 text-xs mt-0.5 shrink-0"></i>
                        <p class="text-xs text-red-700">{{ $daftar->catatan_admin }}</p>
                    </div>
                    @endif

                    {{-- Bukti Bayar --}}
                    @if($p->berbayar)
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">Bukti Bayar</span>
                        @if($daftar->bukti_bayar)
                            @php $urlBukti = Storage::url($daftar->bukti_bayar); @endphp
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-emerald-600 font-medium">
                                    <i class="fa-solid fa-file-image mr-1"></i>Sudah upload
                                </span>
                                <button x-on:click="openLihat('{{ $urlBukti }}')"
                                        class="text-xs text-sky-600 hover:underline font-medium">
                                    Lihat
                                </button>
                                @if($daftar->status !== 'terverifikasi')
                                <button x-on:click="openUpload({{ $daftar->id }}, '{{ $pNama }}', '{{ $pBank }}', '{{ $pRekening }}', '{{ $pAtasNama }}', '{{ $pBiaya }}')"
                                        class="text-xs text-amber-600 hover:underline font-medium">
                                    Ganti
                                </button>
                                @endif
                            </div>
                        @else
                            <button x-on:click="openUpload({{ $daftar->id }}, '{{ $pNama }}', '{{ $pBank }}', '{{ $pRekening }}', '{{ $pAtasNama }}', '{{ $pBiaya }}')"
                                    class="inline-flex items-center gap-1 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 px-2.5 py-1 rounded-lg transition-colors">
                                <i class="fa-solid fa-upload text-[10px]"></i> Upload Bukti
                            </button>
                        @endif
                    </div>
                    @endif
                </div>
                @else
                {{-- Belum daftar --}}
                <div class="mt-auto pt-3 border-t border-slate-100">
                    @if($p->status === 'selesai')
                        <div class="w-full py-2 text-sm font-semibold text-slate-400 bg-slate-50 border border-slate-200 rounded-xl text-center cursor-not-allowed">
                            <i class="fa-solid fa-lock mr-1.5"></i>Pendaftaran Ditutup
                        </div>
                    @elseif($p->peserta_count >= $p->kuota)
                        <div class="w-full py-2 text-sm font-semibold text-red-500 bg-red-50 border border-red-200 rounded-xl text-center cursor-not-allowed">
                            <i class="fa-solid fa-users-slash mr-1.5"></i>Kuota Penuh
                        </div>
                    @elseif($p->isDeadlinePassed())
                        <div class="w-full py-2 text-sm font-semibold text-red-500 bg-red-50 border border-red-200 rounded-xl text-center cursor-not-allowed">
                            <i class="fa-solid fa-hourglass-end mr-1.5"></i>Pendaftaran Berakhir
                        </div>
                    @else
                        <button x-on:click="openDaftar({{ $p->id }}, '{{ $pNama }}', {{ $p->berbayar ? 'true' : 'false' }}, '{{ $pBank }}', '{{ $pRekening }}', '{{ $pAtasNama }}', '{{ $pBiaya }}')"
                                class="w-full py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-colors shadow-sm shadow-sky-200">
                            <i class="fa-solid fa-plus mr-1.5"></i>Daftar Sekarang
                        </button>
                    @endif
                </div>
                @endif

            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════
         MODAL 1: Konfirmasi Daftar
    ══════════════════════════════════════ --}}
    <div x-show="modalDaftar" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" x-on:click="modalDaftar = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Konfirmasi Pendaftaran</h3>
                <button x-on:click="modalDaftar = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <p class="text-sm text-slate-600">
                    Anda akan mendaftar ke:
                    <span class="font-semibold text-slate-800" x-text="selectedNama"></span>
                </p>

                {{-- Info rekening jika berbayar --}}
                <div x-show="selectedBerbayar" class="space-y-3">
                    <div class="p-4 bg-sky-50 rounded-xl border border-sky-200">
                        <p class="text-xs font-bold text-sky-700 uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-building-columns mr-1.5"></i>Informasi Pembayaran
                        </p>
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Biaya</span>
                                <span class="font-bold text-slate-800" x-text="selectedBiaya"></span>
                            </div>
                            <div x-show="selectedBank" class="flex justify-between text-sm">
                                <span class="text-slate-500">Bank</span>
                                <span class="font-semibold text-slate-800" x-text="selectedBank"></span>
                            </div>
                            <div x-show="selectedRekening" class="flex justify-between text-sm">
                                <span class="text-slate-500">No. Rekening</span>
                                <span class="font-bold text-sky-700 font-mono" x-text="selectedRekening"></span>
                            </div>
                            <div x-show="selectedAtasNama" class="flex justify-between text-sm">
                                <span class="text-slate-500">Atas Nama</span>
                                <span class="font-semibold text-slate-800" x-text="selectedAtasNama"></span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">
                        <i class="fa-solid fa-circle-info mr-1.5"></i>
                        Setelah mendaftar, Anda perlu upload bukti transfer untuk diverifikasi admin.
                    </p>
                </div>

                <div x-show="!selectedBerbayar" class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-sm text-emerald-700">
                    <i class="fa-solid fa-circle-check mr-1.5"></i>
                    Pelatihan ini gratis — langsung daftar tanpa pembayaran.
                </div>

                <form action="{{ route('peserta.pelatihan.daftar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pelatihan_id" x-bind:value="selectedId">
                    <div class="flex gap-3 pt-2">
                        <button type="button" x-on:click="modalDaftar = false"
                                class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-colors shadow-sm shadow-sky-200">
                            <i class="fa-solid fa-check mr-1.5"></i>Daftar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL 2: Upload Bukti Bayar
    ══════════════════════════════════════ --}}
    <div x-show="modalBukti" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" x-on:click="modalBukti = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-semibold text-slate-800">Upload Bukti Pembayaran</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="selectedNama"></p>
                </div>
                <button x-on:click="modalBukti = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                {{-- Info rekening --}}
                <div class="p-4 bg-sky-50 rounded-xl border border-sky-200">
                    <p class="text-xs font-bold text-sky-700 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-building-columns mr-1.5"></i>Transfer ke Rekening
                    </p>
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Jumlah</span>
                            <span class="font-bold text-slate-800" x-text="selectedBiaya"></span>
                        </div>
                        <div x-show="selectedBank" class="flex justify-between text-sm">
                            <span class="text-slate-500">Bank</span>
                            <span class="font-semibold text-slate-800" x-text="selectedBank"></span>
                        </div>
                        <div x-show="selectedRekening" class="flex justify-between text-sm">
                            <span class="text-slate-500">No. Rekening</span>
                            <span class="font-bold text-sky-700 font-mono text-base" x-text="selectedRekening"></span>
                        </div>
                        <div x-show="selectedAtasNama" class="flex justify-between text-sm">
                            <span class="text-slate-500">Atas Nama</span>
                            <span class="font-semibold text-slate-800" x-text="selectedAtasNama"></span>
                        </div>
                    </div>
                </div>

                {{-- Form upload --}}
                <form x-bind:action="'{{ url('peserta/pelatihan/bukti') }}/' + selectedPendaftaranId"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-5 text-center hover:border-sky-300 transition-colors cursor-pointer mb-3"
                         x-on:click="$refs.buktiFile.click()">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-2"></i>
                        <p class="text-sm font-medium text-slate-600" x-text="fileName || 'Klik untuk pilih file bukti transfer'"></p>
                        <p class="text-xs text-slate-400 mt-0.5">JPG, PNG, PDF (maks. 5MB)</p>
                        <input type="file" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf"
                               x-ref="buktiFile" class="hidden"
                               x-on:change="fileName = $event.target.files[0]?.name || ''">
                    </div>
                    <div class="flex gap-3">
                        <button type="button" x-on:click="modalBukti = false"
                                class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-colors shadow-sm shadow-sky-200">
                            <i class="fa-solid fa-upload mr-1.5"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL 3: Lihat Bukti
    ══════════════════════════════════════ --}}
    <div x-show="modalLihat" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" x-on:click="modalLihat = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Bukti Pembayaran</h3>
                <button x-on:click="modalLihat = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="relative w-full min-h-48 bg-slate-100 rounded-xl overflow-hidden border border-slate-200 flex items-center justify-center">
                    <div x-show="imgLoading" class="absolute inset-0 flex items-center justify-center bg-slate-100">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-slate-400"></i>
                    </div>
                    <img x-bind:src="buktiUrl" x-show="!imgError"
                         x-on:load="imgLoading = false"
                         x-on:error="imgLoading = false; imgError = true"
                         class="w-full max-h-96 object-contain" alt="Bukti">
                    <div x-show="imgError" class="text-center text-slate-400 p-8">
                        <i class="fa-solid fa-file-pdf text-4xl text-red-400 mb-2"></i>
                        <p class="text-sm font-medium text-slate-600">File PDF</p>
                        <a x-bind:href="buktiUrl" target="_blank"
                           class="mt-2 inline-flex items-center gap-1.5 text-sm text-sky-600 hover:underline">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka di tab baru
                        </a>
                    </div>
                </div>
            </div>
            <div class="px-6 pb-5 flex justify-between items-center">
                <a x-bind:href="buktiUrl" target="_blank"
                   class="text-sm text-slate-600 hover:text-slate-800 font-medium inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Buka di tab baru
                </a>
                <button x-on:click="modalLihat = false"
                        class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL 4: Lihat Nota Konfirmasi
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
            <div class="px-8 py-6 text-white relative"
                 style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%)">
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

                <div class="text-center pt-4">
                    <div class="inline-flex items-center gap-2 text-[11px] text-slate-400 bg-slate-50 px-4 py-2 rounded-full italic">
                        <i class="fa-solid fa-shield-halved"></i> Verified by MersifLab System
                    </div>
                </div>

                <button x-on:click="modalNota = false"
                        class="w-full py-3.5 text-sm font-bold text-white bg-slate-800 hover:bg-slate-900 rounded-2xl transition-all shadow-xl shadow-slate-200">
                    Tutup Nota
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
