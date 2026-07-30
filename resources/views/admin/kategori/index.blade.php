@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ isEdit: false, editId: null, editNama: '', editWarna: '#6366f1', actionUrl: '{{ route('admin.kategori.store') }}' }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Kategori Pelatihan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola kategori untuk klasifikasi program pelatihan ({{ $kategoris->count() }} total)</p>
        </div>
    </div>

    {{-- Flash & Validation Errors --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
        <i class="fa-solid fa-circle-check shrink-0"></i> {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
        <div class="flex items-center gap-2 font-semibold">
            <i class="fa-solid fa-circle-exclamation shrink-0"></i> Terjadi kesalahan:
        </div>
        <ul class="list-disc list-inside pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Categories Table (col-span-2) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-base">Daftar Kategori</h3>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                            <th class="px-6 py-3.5 text-left font-semibold w-16">No</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Nama Kategori</th>
                            <th class="px-6 py-3.5 text-left font-semibold w-32">Warna</th>
                            <th class="px-6 py-3.5 text-left font-semibold w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kategoris as $index => $kat)
                        <tr class="hover:bg-slate-50 transition-colors" :class="editId === {{ $kat->id }} ? 'bg-sky-50/30' : ''">
                            <td class="px-6 py-4 font-medium text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800 text-sm">{{ $kat->nama }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full border border-slate-200 shrink-0 shadow-sm" style="background:{{ $kat->warna }}"></span>
                                    <code class="text-xs text-slate-600 font-mono font-semibold">{{ strtoupper($kat->warna) }}</code>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    {{-- Edit Trigger --}}
                                    <button type="button" 
                                            @click="isEdit = true; editId = {{ $kat->id }}; editNama = '{{ addslashes($kat->nama) }}'; editWarna = '{{ $kat->warna }}'; actionUrl = '{{ route('admin.kategori.update', $kat->id) }}'"
                                            class="p-1.5 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors" 
                                            title="Edit Kategori">
                                        <i class="fa-solid fa-pen text-sm"></i>
                                    </button>
                                    
                                    {{-- Delete Form --}}
                                    <form action="{{ route('admin.kategori.destroy', $kat->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="confirmDelete(this.closest('form'), '{{ addslashes($kat->nama) }}')"
                                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                                title="Hapus Kategori">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa-solid fa-tags text-3xl"></i>
                                    <p class="font-medium text-slate-600">Belum ada kategori pelatihan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 text-xs text-slate-500">
                Menampilkan total {{ $kategoris->count() }} kategori
            </div>
        </div>

        {{-- Right: Form Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 self-start">
            <h3 class="font-bold text-slate-800 text-base mb-4" x-text="isEdit ? 'Edit Kategori' : 'Tambah Kategori Baru'"></h3>
            
            <form :action="actionUrl" method="POST">
                @csrf
                {{-- method for PUT request --}}
                <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kategori</label>
                        <input type="text" 
                               name="nama" 
                               required 
                               placeholder="Contoh: Cloud Computing, IoT" 
                               x-model="editNama"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 transition">
                        <p class="text-xs text-slate-400 mt-1.5">Nama kategori harus unik dan deskriptif.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Warna Kategori</label>
                        <div class="flex items-center gap-3">
                            <input type="color" 
                                   name="warna" 
                                   required 
                                   x-model="editWarna"
                                   class="w-12 h-10 border border-slate-200 rounded-xl cursor-pointer p-1 bg-white">
                            <input type="text" 
                                   x-model="editWarna"
                                   required
                                   class="flex-1 px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 transition font-mono uppercase"
                                   placeholder="#6366F1">
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Pilih warna representatif kategori untuk grafik dashboard.</p>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <button type="submit" 
                                class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow-sm transition-all"
                                style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 14px rgba(56,189,248,0.30);">
                            <i class="fa-solid" :class="isEdit ? 'fa-save' : 'fa-plus'"></i>
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Tambah Kategori'"></span>
                        </button>
                        
                        {{-- Cancel button only shown in Edit Mode --}}
                        <button type="button" 
                                x-show="isEdit"
                                @click="isEdit = false; editId = null; editNama = ''; editWarna = '#6366f1'; actionUrl = '{{ route('admin.kategori.store') }}'"
                                class="px-4 py-2.5 border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-xl transition-all">
                            Batal
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(form, nama) {
    if (confirm('Hapus kategori "' + nama + '"?\n\nKategori ini tidak akan lagi tersedia untuk pelatihan baru, tetapi pelatihan yang sudah terdaftar dengan kategori ini tidak akan berubah.')) {
        form.submit();
    }
}
</script>
@endpush
