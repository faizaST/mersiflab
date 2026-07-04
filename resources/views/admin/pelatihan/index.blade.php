@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pelatihan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola semua program pelatihan ({{ $pelatihans->count() }} total)</p>
        </div>
        <a href="{{ route('admin.pelatihan.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl transition-all"
           style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 14px rgba(56,189,248,0.35);">
            <i class="fa-solid fa-plus"></i> Tambah Pelatihan
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
        <i class="fa-solid fa-circle-check shrink-0"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.pelatihan.index') }}"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-center">

        {{-- Search --}}
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama pelatihan..."
                   class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
        </div>

        {{-- Filter Status --}}
        <select name="status"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-600">
            <option value="semua" {{ request('status','semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
            <option value="aktif"   {{ request('status') === 'aktif'   ? 'selected' : '' }}>Aktif</option>
            <option value="draft"   {{ request('status') === 'draft'   ? 'selected' : '' }}>Draft</option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>

        {{-- Filter Tipe --}}
        <select name="tipe"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-600">
            <option value=""        {{ !request('tipe')              ? 'selected' : '' }}>Semua Tipe</option>
            <option value="gratis"  {{ request('tipe') === 'gratis'  ? 'selected' : '' }}>Gratis</option>
            <option value="berbayar"{{ request('tipe') === 'berbayar'? 'selected' : '' }}>Berbayar</option>
        </select>

        {{-- Filter Kategori --}}
        <select name="kategori_id"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-600">
            <option value="semua" {{ request('kategori','semua') === 'semua' ? 'selected' : '' }}>Semua Kategori</option>
            @foreach($kategoris as $id => $nama)
                <option value="{{ $id }}" {{ request('kategori') == $id ? 'selected' : '' }}>{{ $nama }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="px-4 py-2 text-sm font-semibold text-white rounded-xl transition-all"
                style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 12px rgba(56,189,248,0.30);">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>

        @if(request()->hasAny(['search','status','tipe','kategori']))
        <a href="{{ route('admin.pelatihan.index') }}"
           class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
            <i class="fa-solid fa-xmark mr-1"></i> Reset
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-3.5 text-left font-semibold">Nama Pelatihan</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Mentor</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Tipe</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Kuota</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Kategori</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Tanggal</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Status</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pelatihans as $p)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">{{ $p->nama }}</div>
                            @if($p->deskripsi)
                                <div class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">{{ $p->deskripsi }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($p->mentors->isEmpty())
                                <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Belum diassign</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach($p->mentors as $m)
                                        <span class="text-xs bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full font-medium">{{ $m->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if(!$p->berbayar)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Gratis</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700">Berbayar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-sm">{{ $p->kuota }} orang</td>
                        <td class="px-6 py-4">
                            @if($p->kategoriPelatihan)
                                @php
                                    $katWarna = $p->kategoriPelatihan?->warna ?? '#64748b';
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold text-white shadow-sm border border-black/5"
                                      style="background-color: {{ $katWarna }}">
                                    {{ $p->kategoriPelatihan?->nama }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $sesiTerurut = $p->sesi->whereNotNull('tanggal')->sortBy('tanggal');
                                $tglMulai    = $sesiTerurut->first()?->tanggal;
                                $tglSelesai  = $sesiTerurut->last()?->tanggal;
                            @endphp
                            @if($tglMulai)
                                <div class="text-xs text-slate-700 font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($tglMulai)->format('d M Y') }}
                                </div>
                                @if($tglSelesai && $tglSelesai !== $tglMulai)
                                <div class="text-xs text-slate-400 mt-0.5 whitespace-nowrap">
                                    s/d {{ \Carbon\Carbon::parse($tglSelesai)->format('d M Y') }}
                                </div>
                                @endif
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{-- Dropdown toggle status langsung --}}
                            <form action="{{ route('admin.pelatihan.status', $p->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()"
                                        class="text-xs font-semibold rounded-full px-2.5 py-1 border-0 cursor-pointer focus:outline-none focus:ring-2 focus:ring-sky-300 transition-colors
                                        {{ $p->status === 'aktif'   ? 'bg-emerald-50 text-emerald-700' :
                                           ($p->status === 'draft'  ? 'bg-amber-50 text-amber-700' :
                                                                       'bg-slate-100 text-slate-600') }}">
                                    <option value="aktif"   {{ $p->status === 'aktif'   ? 'selected' : '' }}>● Aktif</option>
                                    <option value="selesai" {{ $p->status === 'selesai' ? 'selected' : '' }}>● Selesai</option>
                                    <option value="draft"   {{ $p->status === 'draft'   ? 'selected' : '' }}>● Draft</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.pelatihan.show', $p->id) }}"
                                   class="p-1.5 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors" title="Detail">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.pelatihan.edit', $p->id) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                {{-- Form hapus terpisah, tidak bersarang --}}
                                <form action="{{ route('admin.pelatihan.destroy', $p->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete(this.closest('form'), '{{ addslashes($p->nama) }}')"
                                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-slate-400">
                                <i class="fa-solid fa-chalkboard-user text-4xl"></i>
                                <p class="font-medium text-slate-600">
                                    @if(request()->hasAny(['search','status','tipe','kategori']))
                                        Tidak ada pelatihan yang sesuai filter
                                    @else
                                        Belum ada pelatihan
                                    @endif
                                </p>
                                @if(request()->hasAny(['search','status','tipe','kategori']))
                                    <a href="{{ route('admin.pelatihan.index') }}" class="text-sm text-sky-600 hover:underline">Reset filter</a>
                                @else
                                    <a href="{{ route('admin.pelatihan.create') }}" class="text-sm text-sky-600 hover:underline">Tambah pelatihan pertama</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500">
            <span>Menampilkan {{ $pelatihans->count() }} pelatihan</span>
            @if(request()->hasAny(['search','status','tipe','kategori']))
                <a href="{{ route('admin.pelatihan.index') }}" class="text-xs text-sky-600 hover:underline">
                    <i class="fa-solid fa-xmark mr-1"></i>Hapus semua filter
                </a>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function confirmDelete(form, nama) {
    if (confirm('Hapus pelatihan "' + nama + '"?\n\nSemua data pendaftaran dan sesi terkait juga akan dihapus.')) {
        form.submit();
    }
}
</script>
@endpush
