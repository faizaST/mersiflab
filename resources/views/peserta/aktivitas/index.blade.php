@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'presensi' }">

    <div>
        <h1 class="text-2xl font-bold text-slate-800">Aktivitas Saya</h1>
        <p class="text-sm text-slate-500 mt-0.5">Riwayat presensi dan tugas per pelatihan</p>
    </div>

    {{-- Pilih Pelatihan --}}
    @if($pendaftarans->count() > 1)
    <form method="GET" action="{{ route('peserta.aktivitas.index') }}" class="flex items-center gap-3">
        <input type="hidden" name="tab" :value="tab">
        <label class="text-sm font-medium text-slate-700">Pelatihan:</label>
        <select name="pelatihan_id" onchange="this.form.submit()"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 bg-white shadow-sm">
            @foreach($pendaftarans as $d)
                <option value="{{ $d->pelatihan_id }}" {{ $d->pelatihan_id == $selectedPelatihanId ? 'selected' : '' }}>
                    {{ $d->pelatihan->nama }}
                </option>
            @endforeach
        </select>
    </form>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
        <button @click="tab = 'presensi'"
                :class="tab === 'presensi' ? 'bg-white shadow-sm text-slate-800 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                class="px-4 py-2 text-sm rounded-lg transition-all">
            <i class="fa-solid fa-calendar-check mr-1.5"></i>Presensi
        </button>
    </div>

    {{-- ── PRESENSI TAB ── --}}
    <div x-show="tab === 'presensi'" x-transition>
        @if(!$selectedDaftar)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center text-slate-400">
            <i class="fa-solid fa-chalkboard-user text-4xl mb-3"></i>
            <p class="font-medium text-slate-600">Belum ada pelatihan yang diikuti</p>
            <a href="{{ route('peserta.pelatihan.index') }}" class="text-sm text-sky-600 hover:underline mt-1 block">Daftar pelatihan</a>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">{{ $selectedDaftar->pelatihan->nama }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Mentor: {{ $selectedDaftar->pelatihan->mentors->pluck('name')->join(', ') ?: '—' }}
                </p>
            </div>

            {{-- Progress --}}
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-700">Total Kehadiran</span>
                    <span class="text-sm font-bold {{ $persenHadir >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $hadirCount }}/{{ $totalSesi }} sesi ({{ $persenHadir }}%)
                    </span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full transition-all {{ $persenHadir >= 80 ? 'bg-emerald-500' : ($persenHadir >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                         @style(['width' => $persenHadir . '%'])></div>
                </div>
                <div class="mt-2">
                    @if($persenHadir >= 80)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                            <i class="fa-solid fa-check text-[10px]"></i> Memenuhi Syarat Kehadiran (≥80%)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                            <i class="fa-solid fa-clock text-[10px]"></i> Belum Memenuhi Syarat (butuh ≥80%)
                        </span>
                    @endif
                </div>
            </div>

            @if($presensiData->isEmpty())
            <div class="px-6 py-10 text-center text-slate-400">
                <i class="fa-solid fa-calendar-xmark text-3xl mb-2"></i>
                <p class="text-sm">Belum ada sesi yang dijadwalkan</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                            <th class="px-6 py-3.5 text-left font-semibold">Sesi</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Topik</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Tanggal</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Kehadiran</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Tugas</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Catatan Mentor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($presensiData as $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-700">Sesi {{ $row['no'] }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $row['sesi']->topik ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                {{ $row['sesi']->tanggal ? \Carbon\Carbon::parse($row['sesi']->tanggal)->format('d M Y') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($row['hadir'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        <i class="fa-solid fa-check text-[10px]"></i> Hadir
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                        <i class="fa-solid fa-xmark text-[10px]"></i> Tidak Hadir
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($row['status_tugas'] === 'valid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        <i class="fa-solid fa-check text-[10px]"></i> Tugas Valid
                                    </span>
                                @elseif($row['status_tugas'] === 'tidak_valid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                        <i class="fa-solid fa-xmark text-[10px]"></i> Tugas Tidak Valid
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">{{ $row['catatan'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- ── BUKTI BAYAR TAB ── --}}
    <div x-show="tab === 'pembayaran'" x-transition
         x-data="{ modalOpen: false, buktiUrl: '', imgLoading: true, imgError: false }">
        <div class="space-y-4">
            @forelse($pendaftarans as $daftar)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-semibold text-slate-800">{{ $daftar->pelatihan->nama }}</h4>
                        <p class="text-sm text-slate-500 mt-0.5">
                            Biaya:
                            <span class="font-semibold text-slate-700">
                                {{ $daftar->pelatihan->berbayar ? 'Rp ' . number_format($daftar->pelatihan->biaya, 0, ',', '.') : 'Gratis' }}
                            </span>
                        </p>
                    </div>
                    @if($daftar->status === 'terverifikasi')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                            <i class="fa-solid fa-check text-[10px]"></i> Terverifikasi
                        </span>
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

                @if($daftar->pelatihan->berbayar)
                    @if($daftar->bukti_bayar)
                    @php $urlBukti = Storage::url($daftar->bukti_bayar); @endphp
                    {{-- Sudah upload — tampilkan tombol lihat seperti admin --}}
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-image text-sky-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800">Bukti pembayaran sudah diupload</p>
                            <p class="text-xs text-slate-400 mt-0.5">Klik untuk melihat file</p>
                        </div>
                        <button
                            x-on:click="buktiUrl = '{{ $urlBukti }}'; imgLoading = true; imgError = false; modalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-sky-600 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-lg transition-colors shrink-0">
                            <i class="fa-solid fa-eye"></i> Lihat Bukti
                        </button>
                    </div>
                    @if($daftar->status !== 'terverifikasi')
                    <p class="text-xs text-slate-500 mb-3">Ganti bukti jika diperlukan:</p>
                    @endif
                    @else
                    {{-- Belum upload --}}
                    @if($daftar->status === 'ditolak')
                    <div class="p-3 bg-red-50 rounded-xl border border-red-200 text-sm text-red-700 mb-3">
                        <i class="fa-solid fa-circle-exclamation mr-1.5"></i>
                        Pendaftaran ditolak. Upload ulang bukti pembayaran yang valid.
                    </div>
                    @else
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-sm text-amber-700 mb-3">
                        <i class="fa-solid fa-circle-info mr-1.5"></i>
                        Silakan upload bukti pembayaran untuk diverifikasi admin.
                    </div>
                    @endif
                    @endif

                    @if($daftar->status !== 'terverifikasi')
                    <form action="{{ route('peserta.aktivitas.upload-bukti', $daftar->id) }}" method="POST"
                          enctype="multipart/form-data" x-data="{ fileName: '' }">
                        @csrf
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-5 text-center hover:border-sky-300 transition-colors cursor-pointer mb-3"
                             x-on:click="$refs.fileInput_{{ $daftar->id }}.click()">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-2"></i>
                            <p class="text-sm font-medium text-slate-600" x-text="fileName || 'Klik untuk pilih file'"></p>
                            <p class="text-xs text-slate-400 mt-0.5">JPG, PNG, PDF (maks. 5MB)</p>
                            <input type="file" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf"
                                   x-ref="fileInput_{{ $daftar->id }}" class="hidden"
                                   x-on:change="fileName = $event.target.files[0]?.name || ''">
                        </div>
                        <button type="submit"
                                class="w-full py-2.5 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-colors shadow-sm shadow-sky-200">
                            <i class="fa-solid fa-upload mr-1.5"></i>Upload Bukti Pembayaran
                        </button>
                    </form>
                    @endif
                @else
                <div class="p-3 bg-emerald-50 rounded-xl text-sm text-emerald-700 text-center">
                    <i class="fa-solid fa-circle-check mr-1.5"></i>
                    Pelatihan ini gratis — tidak perlu bukti pembayaran
                </div>
                @endif
            </div>
            @empty
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center text-slate-400">
                <i class="fa-solid fa-receipt text-4xl mb-3"></i>
                <p class="font-medium text-slate-600">Belum ada pendaftaran</p>
            </div>
            @endforelse
        </div>

        {{-- Modal Preview Bukti (sama persis seperti admin) --}}
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

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-semibold text-slate-800">Bukti Pembayaran</h3>
                        <p class="text-xs text-slate-500 mt-0.5">File yang sudah Anda upload</p>
                    </div>
                    <button x-on:click="modalOpen = false"
                            class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Image area --}}
                <div class="p-4">
                    <div class="relative w-full min-h-48 bg-slate-100 rounded-xl overflow-hidden border border-slate-200 flex items-center justify-center">

                        {{-- Loading --}}
                        <div x-show="imgLoading"
                             class="absolute inset-0 flex items-center justify-center bg-slate-100">
                            <i class="fa-solid fa-spinner fa-spin text-2xl text-slate-400"></i>
                        </div>

                        {{-- Gambar --}}
                        <img x-bind:src="buktiUrl"
                             x-show="!imgError"
                             x-on:load="imgLoading = false"
                             x-on:error="imgLoading = false; imgError = true"
                             class="w-full max-h-96 object-contain"
                             alt="Bukti Pembayaran">

                        {{-- Fallback PDF / error --}}
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

                {{-- Footer --}}
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

    </div>

</div>
@endsection
