@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="editPage()">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pelatihan.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Pelatihan</h1>
                <p class="text-sm text-slate-500 mt-0.5">{{ $pelatihan->nama }}</p>
            </div>
        </div>
        {{-- Tombol hapus TERPISAH dari form edit --}}
        <button type="button" @click="showDeleteModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl transition-colors">
            <i class="fa-solid fa-trash"></i> Hapus
        </button>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- FORM EDIT --}}
    <form id="editForm" action="{{ route('admin.pelatihan.update', $pelatihan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Informasi Dasar --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-sky-500"></i> Informasi Dasar
            </h2>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Pelatihan <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $pelatihan->nama) }}" required
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition resize-none">{{ old('deskripsi', $pelatihan->deskripsi) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Banner Pelatihan (Gambar)</label>
                <div class="flex items-center gap-4" x-data="{ preview: '{{ $pelatihan->gambar ? Storage::url($pelatihan->gambar) : '' }}' }">
                    <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden bg-slate-50">
                        <template x-if="preview">
                            <img :src="preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!preview">
                            <i class="fa-solid fa-image text-slate-300 text-xl"></i>
                        </template>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="gambar" accept="image/*"
                               @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }"
                               class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 transition">
                        <p class="text-[10px] text-slate-400 mt-1.5">Kosongkan jika tidak ingin mengubah. Max 5MB.</p>
                    </div>
                </div>
            </div>



            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Kuota <span class="text-red-500">*</span></label>
                        <input type="number" name="kuota" value="{{ old('kuota', $pelatihan->kuota) }}" min="1" required
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                    </div>
                    <div x-data="{ showModal: false, newKategori: '', loading: false, errorMsg: '' }" class="relative">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-medium text-slate-700">Kategori</label>
                            <button type="button" @click="showModal = true" class="text-xs text-sky-600 hover:text-sky-700 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-circle-plus"></i> Tambah Baru
                            </button>
                        </div>
                        <select id="kategori-select" name="kategori_id" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 transition">
                            <option value="">Pilih kategori</option>
                            @foreach($kategoris as $id => $nama)
                                <option value="{{ $id }}" {{ old('kategori_id', $pelatihan->kategori_id) == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>

                        {{-- Quick Add Modal Backdrop --}}
                        <div x-show="showModal" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fade-in"
                             style="display: none;">
                            
                            {{-- Modal Content --}}
                            <div @click.outside="showModal = false" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="bg-white rounded-2xl border border-slate-100 shadow-xl max-w-sm w-full p-6 space-y-4">
                                
                                <div>
                                    <h3 class="text-base font-bold text-slate-800">Tambah Kategori Baru</h3>
                                    <p class="text-xs text-slate-500 mt-1">Kategori baru akan tersimpan ke database dan otomatis terpilih.</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Kategori</label>
                                    <input type="text" x-model="newKategori" placeholder="Contoh: Machine Learning" 
                                           @keydown.enter.prevent="
                                                if (newKategori.trim() !== '') {
                                                    loading = true;
                                                    errorMsg = '';
                                                    fetch('{{ route('admin.kategori.storeAjax') }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({ nama: newKategori })
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        loading = false;
                                                        if (data.success) {
                                                            const select = document.getElementById('kategori-select');
                                                            const opt = document.createElement('option');
                                                            opt.value = data.kategori.id;
                                                            opt.textContent = data.kategori.nama;
                                                            opt.selected = true;
                                                            select.appendChild(opt);
                                                            showModal = false;
                                                            newKategori = '';
                                                        } else {
                                                            errorMsg = data.message || 'Gagal menambahkan kategori';
                                                        }
                                                    })
                                                    .catch(err => {
                                                        loading = false;
                                                        errorMsg = 'Terjadi kesalahan sistem atau nama kategori ganda.';
                                                    });
                                                }
                                           "
                                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 transition">
                                    <template x-if="errorMsg">
                                        <p class="text-[10px] text-red-500 mt-1.5" x-text="errorMsg"></p>
                                    </template>
                                </div>

                                <div class="flex items-center gap-2 pt-2">
                                    <button type="button" 
                                            @click="
                                                if (newKategori.trim() === '') return;
                                                loading = true;
                                                errorMsg = '';
                                                fetch('{{ route('admin.kategori.storeAjax') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({ nama: newKategori })
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    loading = false;
                                                    if (data.success) {
                                                        const select = document.getElementById('kategori-select');
                                                        const opt = document.createElement('option');
                                                        opt.value = data.kategori.nama;
                                                        opt.textContent = data.kategori.nama;
                                                        opt.selected = true;
                                                        select.appendChild(opt);
                                                        showModal = false;
                                                        newKategori = '';
                                                    } else {
                                                        errorMsg = data.message || 'Gagal menambahkan kategori';
                                                    }
                                                })
                                                .catch(err => {
                                                    loading = false;
                                                    errorMsg = 'Terjadi kesalahan sistem atau nama kategori ganda.';
                                                });
                                            "
                                            :disabled="loading"
                                            class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 disabled:bg-sky-400 text-white text-xs font-semibold rounded-xl shadow-sm transition-all font-medium">
                                        <i class="fa-solid fa-spinner animate-spin" x-show="loading"></i>
                                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Kategori'"></span>
                                    </button>
                                    <button type="button" @click="showModal = false; newKategori = ''; errorMsg = ''" 
                                            class="px-4 py-2 border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-xs font-medium rounded-xl transition-all">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                        <select name="status" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 transition">
                            @foreach(['aktif' => 'Aktif', 'draft' => 'Draft', 'selesai' => 'Selesai'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('status', $pelatihan->status) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tenggat Daftar</label>
                        <input type="datetime-local" name="tenggat_pendaftaran" 
                               value="{{ old('tenggat_pendaftaran', $pelatihan->tenggat_pendaftaran ? $pelatihan->tenggat_pendaftaran->format('Y-m-d\TH:i') : '') }}"
                               min="{{ date('Y-m-d\TH:i') }}"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- Assign Mentor --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-chalkboard-user text-sky-500"></i> Assign Mentor
                <span class="text-xs font-normal text-slate-400">(bisa pilih lebih dari satu)</span>
            </h2>
            @if($mentors->isEmpty())
                <p class="text-sm text-slate-400">Belum ada mentor. <a href="{{ route('admin.users.index') }}" class="text-sky-600 hover:underline">Tambah mentor dulu</a>.</p>
            @else
            @php $assignedIds = $pelatihan->mentors->pluck('id')->toArray(); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($mentors as $mentor)
                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all
                              {{ in_array($mentor->id, $assignedIds) ? 'border-sky-300 bg-sky-50/40' : 'border-slate-200 hover:border-sky-300 hover:bg-sky-50/30' }}">
                    <input type="checkbox" name="mentor_ids[]" value="{{ $mentor->id }}"
                           {{ in_array($mentor->id, $assignedIds) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-sky-600 border-slate-300 focus:ring-sky-300">
                    <div class="flex items-center gap-2 min-w-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($mentor->name) }}&background=a855f7&color=fff&size=24&bold=true"
                             class="w-6 h-6 rounded-full shrink-0" alt="">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $mentor->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $mentor->email }}</p>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Biaya --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5"
             x-data="{ berbayar: {{ $pelatihan->berbayar ? 'true' : 'false' }} }">
            <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-tag text-sky-500"></i> Biaya Pelatihan
            </h2>
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                <div>
                    <p class="text-sm font-medium text-slate-700">Pelatihan Berbayar</p>
                    <p class="text-xs text-slate-500 mt-0.5">Aktifkan jika peserta dikenakan biaya</p>
                </div>
                <button type="button" @click="berbayar = !berbayar"
                        :class="berbayar ? 'bg-sky-600' : 'bg-slate-200'"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none">
                    <span :class="berbayar ? 'translate-x-6' : 'translate-x-1'"
                          class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"></span>
                </button>
            </div>
            <input type="hidden" name="berbayar" :value="berbayar ? '1' : ''">
            <div x-show="berbayar" x-transition class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Biaya (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-500 font-medium">Rp</span>
                        <input type="number" name="biaya" value="{{ old('biaya', $pelatihan->biaya) }}" min="0"
                               class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <i class="fa-solid fa-building-columns mr-1.5 text-sky-500"></i>Info Rekening Tujuan Pembayaran
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama Bank</label>
                            <input type="text" name="nama_bank" value="{{ old('nama_bank', $pelatihan->nama_bank) }}"
                                   placeholder="BCA, BRI, Mandiri..."
                                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening', $pelatihan->nomor_rekening) }}"
                                   placeholder="1234567890"
                                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-300 transition font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Atas Nama</label>
                            <input type="text" name="atas_nama" value="{{ old('atas_nama', $pelatihan->atas_nama) }}"
                                   placeholder="Nama pemilik rekening"
                                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Jadwal Sesi --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4"
             x-data="sesiManager({{ $pelatihan->sesi->toJson() }})">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-sky-500"></i> Jadwal Sesi
                    <span class="text-xs font-normal text-slate-400 ml-1">(<span x-text="sesi.length"></span> sesi)</span>
                </h2>
                <button type="button" @click="tambah()"
                        class="inline-flex items-center gap-1.5 text-sm text-sky-600 hover:text-sky-700 font-medium">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Sesi
                </button>
            </div>

            <template x-for="(s, i) in sesi" :key="s._key">
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    {{-- Hidden id untuk sesi yang sudah ada --}}
                    <input type="hidden" :name="'sesi[' + i + '][id]'" :value="s.id || ''">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-700" x-text="'Sesi ' + (i + 1)"></span>
                        <button type="button" @click="hapus(i)"
                                class="text-red-400 hover:text-red-600 transition-colors p-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal</label>
                            <input type="date" x-model="s.tanggal" :name="'sesi[' + i + '][tanggal]'"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                            @error('sesi.*.tanggal')
                                <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Waktu Mulai</label>
                            <input type="time" x-model="s.waktu_mulai" :name="'sesi[' + i + '][waktu_mulai]'"
                                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Waktu Selesai</label>
                            <input type="time" x-model="s.waktu_selesai" :name="'sesi[' + i + '][waktu_selesai]'"
                                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Topik</label>
                        <input type="text" x-model="s.topik" :name="'sesi[' + i + '][topik]'"
                               placeholder="Contoh: Pengenalan HTML & CSS"
                               class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                    </div>
                </div>
            </template>

            <div x-show="sesi.length === 0" class="text-center py-6 text-slate-400 text-sm">
                Belum ada sesi. Klik "Tambah Sesi" untuk menambahkan.
            </div>
        </div>

        {{-- Catatan (NB) --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-note-sticky text-sky-500"></i> Catatan Penting (NB)
            </h2>
            <textarea name="catatan" rows="3" placeholder="Contoh: Peserta wajib membawa laptop sendiri..."
                      class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 focus:border-sky-400 transition resize-none">{{ old('catatan', $pelatihan->catatan) }}</textarea>
        </div>

        {{-- Lokasi --}}
        @include('admin.pelatihan.partials.lokasi-picker', [
            'lokasiNama'   => old('lokasi_nama', $pelatihan->lokasi_nama),
            'lokasiAlamat' => old('lokasi_alamat', $pelatihan->lokasi_alamat),
            'latValue'     => old('lat', $pelatihan->lat),
            'lngValue'     => old('lng', $pelatihan->lng),
            'initLat'      => $pelatihan->lat  ?? -6.2088,
            'initLng'      => $pelatihan->lng  ?? 106.8456,
            'initZoom'     => $pelatihan->lat  ? 15 : 12,
        ])

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pb-6">
            <a href="{{ route('admin.pelatihan.index') }}"
               class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm shadow-sky-200 transition-all">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- Modal Konfirmasi Hapus di luar form edit) --}}
    <div x-show="showDeleteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Hapus Pelatihan?</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <p class="text-sm text-slate-600 mb-2">
                Anda akan menghapus: <span class="font-semibold text-slate-800">{{ $pelatihan->nama }}</span>
            </p>
            <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg p-3 mb-5">
                <i class="fa-solid fa-circle-exclamation mr-1"></i>
                Semua data sesi, pendaftaran, dan presensi terkait juga akan dihapus permanen.
            </p>
            <div class="flex gap-3">
                <button type="button" @click="showDeleteModal = false"
                        class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Batal
                </button>
                {{-- Form hapus berdiri sendiri, tidak di dalam form edit --}}
                <form action="{{ route('admin.pelatihan.destroy', $pelatihan->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                        <i class="fa-solid fa-trash mr-1.5"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function editPage() {
    return { showDeleteModal: false };
}

function sesiManager(existing) {
    return {
        sesi: existing.map(s => ({ ...s, _key: s.id || Math.random() })),
        tambah() {
            this.sesi.push({ id: null, _key: Math.random(), topik: '', tanggal: '', waktu_mulai: '', waktu_selesai: '' });
        },
        hapus(i) {
            this.sesi.splice(i, 1);
        }
    };
}
</script>
@endpush
