@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="presensiPage()">

    <div>
        <h1 class="text-2xl font-bold text-slate-800">Presensi & Tugas</h1>
        <p class="text-sm text-slate-500 mt-0.5">Catat kehadiran dan validasi tugas peserta</p>
    </div>

    {{-- Selector --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Pelatihan</label>
            <select x-model="selectedPelatihan" @change="onPelatihanChange()"
                    class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 transition">
                <option value="">-- Pilih Pelatihan --</option>
                @foreach($pelatihans as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Sesi</label>
            <select x-model="selectedSesi" @change="onSesiChange()"
                    class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 transition">
                <option value="">-- Pilih Sesi --</option>
                <template x-for="s in sesiList" :key="s.id">
                    <option :value="s.id"
                            x-text="(s.topik || 'Sesi') + (s.tanggal ? ' — ' + s.tanggal : '')">
                    </option>
                </template>
            </select>
        </div>
    </div>

    {{-- Empty state --}}
    <div x-show="!selectedPelatihan || !selectedSesi" x-cloak
         class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center text-slate-400">
        <i class="fa-solid fa-clipboard-list text-4xl mb-3"></i>
        <p class="font-medium text-slate-600">Pilih pelatihan dan sesi</p>
        <p class="text-sm mt-1">untuk menampilkan daftar peserta</p>
    </div>

    {{-- Presensi Table --}}
    <div x-show="selectedPelatihan && selectedSesi" x-cloak
         class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ route('mentor.presensi.simpan') }}" method="POST">
            @csrf
            <input type="hidden" name="sesi_id" :value="selectedSesi">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-semibold text-slate-800">Daftar Peserta</h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Hadir: <span class="text-emerald-600 font-semibold" x-text="hadirCount"></span>
                        / <span x-text="pesertaList.length"></span> peserta
                        <span x-show="sudahDiabsen"
                              class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fa-solid fa-circle-check text-[9px]"></i> Sudah pernah disimpan
                        </span>
                        <template x-if="!isToday">
                            <span class="ml-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                <i class="fa-solid fa-calendar-xmark text-[9px]"></i> Bukan jadwal hari ini (Hanya Baca)
                            </span>
                        </template>
                    </p>
                </div>
                <div class="flex gap-2" x-show="isToday">
                    <button type="button" @click="checkAll(true)"
                            class="text-xs text-emerald-600 font-medium px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-check mr-1"></i>Semua Hadir
                    </button>
                    <button type="button" @click="checkAll(false)"
                            class="text-xs text-slate-500 font-medium px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        Reset
                    </button>
                </div>
            </div>

            {{-- No peserta --}}
            <div x-show="pesertaList.length === 0" class="px-6 py-12 text-center text-slate-400">
                <i class="fa-solid fa-users text-3xl mb-2"></i>
                <p class="text-sm font-medium text-slate-600">Belum ada peserta terverifikasi</p>
                <p class="text-xs mt-1">Peserta harus diverifikasi admin terlebih dahulu</p>
            </div>

            {{-- Table --}}
            <div x-show="pesertaList.length > 0" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                            <th class="px-6 py-3.5 text-left font-semibold">Peserta</th>
                            <th class="px-6 py-3.5 text-center font-semibold w-28">Hadir</th>
                            <th class="px-6 py-3.5 text-center font-semibold w-32">Tugas Valid</th>
                            <th class="px-6 py-3.5 text-center font-semibold w-32">Tugas Tidak Valid</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Catatan Mentor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(p, i) in pesertaList" :key="p.id">
                            <tr class="hover:bg-slate-50 transition-colors"
                                :class="{
                                    'opacity-50': !p.hadir,
                                    'bg-emerald-50/30': p.sudahDisimpan
                                }">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(p.nama) + '&background=6366f1&color=fff&size=32&bold=true'"
                                             class="w-8 h-8 rounded-full shrink-0" alt="">
                                        <div>
                                            <span class="font-medium text-slate-800" x-text="p.nama"></span>
                                            <span x-show="p.sudahDisimpan"
                                                  class="ml-2 inline-flex items-center gap-0.5 text-[10px] font-semibold text-emerald-600">
                                                <i class="fa-solid fa-floppy-disk text-[9px]"></i> Tersimpan
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Hadir toggle --}}
                                <td class="px-6 py-4 text-center">
                                    <button type="button"
                                            @click="isToday && (p.hadir = !p.hadir)"
                                            :disabled="!isToday"
                                            :class="p.hadir ? 'bg-emerald-500' : 'bg-slate-200'"
                                            class="relative w-10 h-5 rounded-full transition-colors focus:outline-none">
                                        <span :class="p.hadir ? 'translate-x-5' : 'translate-x-0.5'"
                                              class="absolute top-0.5 left-0 inline-block w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"></span>
                                    </button>
                                    {{-- hidden input untuk form submit --}}
                                    <input type="hidden"
                                           :name="'presensi[' + p.id + '][hadir]'"
                                           :value="p.hadir ? '1' : '0'">
                                </td>

                                {{-- Tugas Valid --}}
                                <td class="px-6 py-4 text-center">
                                    <button type="button"
                                            @click="isToday && (p.status_tugas = p.status_tugas === 'valid' ? 'belum' : 'valid')"
                                            :disabled="!isToday"
                                            :class="p.status_tugas === 'valid' ? 'bg-sky-500' : 'bg-slate-200'"
                                            class="relative w-10 h-5 rounded-full transition-colors focus:outline-none">
                                        <span :class="p.status_tugas === 'valid' ? 'translate-x-5' : 'translate-x-0.5'"
                                              class="absolute top-0.5 left-0 inline-block w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"></span>
                                    </button>
                                    <input type="hidden"
                                           :name="'presensi[' + p.id + '][status_tugas]'"
                                           :value="p.status_tugas">
                                </td>

                                {{-- Tugas Tidak Valid --}}
                                <td class="px-6 py-4 text-center">
                                    <button type="button"
                                            @click="isToday && (p.status_tugas = p.status_tugas === 'tidak_valid' ? 'belum' : 'tidak_valid')"
                                            :disabled="!isToday"
                                            :class="p.status_tugas === 'tidak_valid' ? 'bg-red-500' : 'bg-slate-200'"
                                            class="relative w-10 h-5 rounded-full transition-colors focus:outline-none">
                                        <span :class="p.status_tugas === 'tidak_valid' ? 'translate-x-5' : 'translate-x-0.5'"
                                              class="absolute top-0.5 left-0 inline-block w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"></span>
                                    </button>
                                </td>

                                {{-- Catatan --}}
                                <td class="px-6 py-4">
                                    <input type="text"
                                           x-model="p.catatan"
                                           :name="'presensi[' + p.id + '][catatan]'"
                                           placeholder="Catatan opsional..."
                                           :disabled="!isToday"
                                           class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg
                                                  focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between"
                 x-show="pesertaList.length > 0">
                <p class="text-xs text-slate-400">
                    <span x-show="!sudahDiabsen">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Klik "Simpan" untuk menyimpan presensi sesi ini
                    </span>
                    <span x-show="sudahDiabsen" class="text-amber-600">
                        <i class="fa-solid fa-pen mr-1"></i>
                        Sesi ini sudah pernah disimpan — klik "Update" untuk memperbarui
                    </span>
                </p>
                <button type="submit"
                        x-show="isToday"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700
                               text-white text-sm font-semibold rounded-xl shadow-sm shadow-sky-200 transition-all">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span x-text="sudahDiabsen ? 'Update Presensi' : 'Simpan Presensi'"></span>
                </button>
                <div x-show="!isToday" class="text-xs text-red-500 font-medium bg-red-50 px-3 py-2 rounded-lg border border-red-100">
                    <i class="fa-solid fa-lock mr-1"></i> Pengisian presensi terkunci. Hanya bisa dilakukan pada tanggal jadwal.
                </div>
            </div>
        </form>
    </div>

    {{-- Pelatihan kosong --}}
    @if($pelatihans->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center text-slate-400">
        <i class="fa-solid fa-chalkboard-user text-4xl mb-3"></i>
        <p class="font-medium text-slate-600">Belum ada pelatihan yang diassign</p>
        <p class="text-sm mt-1">Hubungi admin untuk mendapatkan assignment pelatihan</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
const SESI_DATA        = {{ Js::from($sesiPerPelatihan) }};
const PESERTA_DATA     = {{ Js::from($pesertaPerPelatihan) }};
const PRESENSI_DATA    = {{ Js::from($presensiTersimpan) }};   

function presensiPage() {
    return {
        selectedPelatihan: '',
        selectedSesi: '',
        sesiList: [],
        pesertaList: [],
        sudahDiabsen: false,   
        isToday: false,       

        get hadirCount() {
            return this.pesertaList.filter(p => p.hadir).length;
        },

        onPelatihanChange() {
            this.selectedSesi = '';
            this.pesertaList  = [];
            this.sudahDiabsen = false;
            if (!this.selectedPelatihan) { this.sesiList = []; return; }
            this.sesiList = SESI_DATA[String(this.selectedPelatihan)] || [];
        },

        onSesiChange() {
            if (!this.selectedPelatihan || !this.selectedSesi) {
                this.pesertaList  = [];
                this.sudahDiabsen = false;
                this.isToday      = false;
                return;
            }

            //Check if today matches the session date
            const sesiId = String(this.selectedSesi);
            const sesi = this.sesiList.find(s => String(s.id) === sesiId);
            const today = new Date().toISOString().split('T')[0];
            this.isToday  = sesi && sesi.tanggal === today;

            //Ambil daftar peserta untuk pelatihan ini
            const pelatihanId = String(this.selectedPelatihan);
            const basePeserta = PESERTA_DATA[pelatihanId] || [];

            //Ambil data presensi yang sudah tersimpan untuk sesi ini
            const existing = PRESENSI_DATA[sesiId] || {};
            this.sudahDiabsen = Object.keys(existing).length > 0;

            //Merge: jika ada data existing, pakai itu; jika tidak, default kosong
            this.pesertaList = basePeserta.map(p => {
                const saved = existing[String(p.id)];
                return {
                    id:           p.id,
                    nama:         p.nama,
                    hadir:        saved ? Boolean(saved.hadir) : false,
                    status_tugas: saved ? saved.status_tugas   : 'belum',
                    catatan:      saved ? (saved.catatan || '') : '',
                    sudahDisimpan: !!saved,
                };
            });
        },

        checkAll(val) {
            this.pesertaList.forEach(p => p.hadir = val);
        }
    };
}
</script>
@endpush
