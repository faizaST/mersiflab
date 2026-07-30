@extends('layouts.app')

@section('content')
<div class="space-y-6"
     x-data="{
        tab: 'mentor',
        showAddMentor: false,
        showAssign: false,
        assignMentorId: '',
        assignMentorName: '',
        showResetPassword: false,
        resetPesertaId: '',
        resetPesertaName: '',
        searchMentor: '',
        searchPeserta: ''
     }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen User</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola mentor dan peserta</p>
        </div>
        <button x-on:click="showAddMentor = true; tab = 'mentor'"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl transition-all"
                style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 14px rgba(56,189,248,0.35);">
            <i class="fa-solid fa-user-plus"></i> Tambah Mentor
        </button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
        <i class="fa-solid fa-circle-check shrink-0"></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <i class="fa-solid fa-circle-exclamation shrink-0"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
        <button x-on:click="tab = 'mentor'"
                :class="tab === 'mentor' ? 'bg-white shadow-sm text-slate-800 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                class="px-4 py-2 text-sm rounded-lg transition-all">
            <i class="fa-solid fa-chalkboard-user mr-1.5"></i>Mentor ({{ $mentors->count() }})
        </button>
        <button x-on:click="tab = 'peserta'"
                :class="tab === 'peserta' ? 'bg-white shadow-sm text-slate-800 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                class="px-4 py-2 text-sm rounded-lg transition-all">
            <i class="fa-solid fa-users mr-1.5"></i>Peserta ({{ $peserta->count() }})
        </button>
    </div>

    {{-- MENTOR TABLE --}}
    <div x-show="tab === 'mentor'" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center justify-between flex-1">
                <h3 class="font-semibold text-slate-800">Daftar Mentor</h3>
                <span class="text-xs text-slate-400">{{ $mentors->count() }} mentor terdaftar</span>
            </div>
            {{-- Search Mentor --}}
            <div class="relative sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" x-model="searchMentor"
                       placeholder="Cari nama, email, WA..."
                       class="w-full pl-8 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition bg-slate-50">
                <button x-show="searchMentor !== ''"
                        x-on:click="searchMentor = ''"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        </div>

        @if($mentors->isEmpty())
        <div class="px-6 py-12 text-center text-slate-400">
            <i class="fa-solid fa-chalkboard-user text-3xl mb-2"></i>
            <p class="text-sm font-medium text-slate-600">Belum ada mentor terdaftar</p>
            <button x-on:click="showAddMentor = true"
                    class="mt-2 text-sm text-sky-600 hover:underline">Tambah mentor pertama</button>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-3.5 text-left font-semibold">Mentor</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Email</th>
                        <th class="px-6 py-3.5 text-left font-semibold">WhatsApp</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Pelatihan Diampu</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Bergabung</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($mentors as $mentor)
                    <tr class="hover:bg-slate-50 transition-colors"
                        x-show="searchMentor === '' || '{{ strtolower($mentor->name) }}'.includes(searchMentor.toLowerCase()) || '{{ strtolower($mentor->email) }}'.includes(searchMentor.toLowerCase()) || '{{ strtolower($mentor->phone ?? '') }}'.includes(searchMentor.toLowerCase())"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($mentor->name) }}&background=a855f7&color=fff&size=32&bold=true"
                                     class="w-8 h-8 rounded-full shrink-0" alt="">
                                <span class="font-medium text-slate-800">{{ $mentor->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $mentor->email }}</td>
                        <td class="px-6 py-4">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mentor->phone) }}" target="_blank" class="text-sky-600 hover:underline text-xs font-medium">
                                <i class="fa-brands fa-whatsapp mr-1"></i>{{ $mentor->phone ?: '-' }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            @if($mentor->pelatihans->isEmpty())
                                <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Belum diassign</span>
                            @else
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($mentor->pelatihans as $mp)
                                    <span class="inline-flex items-center gap-1 text-xs bg-sky-50 text-sky-700 pl-2 pr-1 py-0.5 rounded-full font-medium">
                                        {{ $mp->nama }}
                                        <form action="{{ route('admin.users.mentor.unassign') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="mentor_id"    value="{{ $mentor->id }}">
                                            <input type="hidden" name="pelatihan_id" value="{{ $mp->id }}">
                                            <button type="submit" title="Lepas assignment"
                                                    class="ml-0.5 text-sky-400 hover:text-red-500 transition-colors">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            </button>
                                        </form>
                                    </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $mentor->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button
                                    x-on:click="assignMentorId = '{{ $mentor->id }}'; assignMentorName = '{{ addslashes($mentor->name) }}'; showAssign = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-link"></i> Assign
                                </button>
                                <form action="{{ route('admin.users.mentor.destroy', $mentor->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus mentor {{ addslashes($mentor->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    {{-- Baris kosong jika hasil pencarian 0 --}}
                    <tr x-show="searchMentor !== '' && document.querySelectorAll('#mentor-tbody tr[style*=\'display: none\']').length === {{ $mentors->count() }}">
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-2xl mb-2 block"></i>
                            <span class="text-sm">Tidak ada mentor yang cocok dengan pencarian</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- PESERTA TABLE --}}
    <div x-show="tab === 'peserta'" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center justify-between flex-1">
                <h3 class="font-semibold text-slate-800">Daftar Peserta</h3>
                <span class="text-xs text-slate-400">{{ $peserta->count() }} peserta terdaftar</span>
            </div>
            {{-- Search Peserta --}}
            <div class="relative sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" x-model="searchPeserta"
                       placeholder="Cari nama, email, WA..."
                       class="w-full pl-8 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 transition bg-slate-50">
                <button x-show="searchPeserta !== ''"
                        x-on:click="searchPeserta = ''"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        </div>
        @if($peserta->isEmpty())
        <div class="px-6 py-12 text-center text-slate-400">
            <i class="fa-solid fa-users text-3xl mb-2"></i>
            <p class="text-sm font-medium text-slate-600">Belum ada peserta terdaftar</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-3.5 text-left font-semibold">Peserta</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Email</th>
                        <th class="px-6 py-3.5 text-left font-semibold">WhatsApp</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Bergabung</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($peserta as $p)
                    <tr class="hover:bg-slate-50 transition-colors"
                        x-show="searchPeserta === '' || '{{ strtolower($p->name) }}'.includes(searchPeserta.toLowerCase()) || '{{ strtolower($p->email) }}'.includes(searchPeserta.toLowerCase()) || '{{ strtolower($p->phone ?? '') }}'.includes(searchPeserta.toLowerCase())"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($p->name) }}&background=6366f1&color=fff&size=32&bold=true"
                                     class="w-8 h-8 rounded-full" alt="">
                                <span class="font-medium text-slate-800">{{ $p->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $p->email }}</td>
                        <td class="px-6 py-4">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->phone) }}" target="_blank" class="text-sky-600 hover:underline text-xs font-medium">
                                <i class="fa-brands fa-whatsapp mr-1"></i>{{ $p->phone ?: '-' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $p->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <button
                                x-on:click="resetPesertaId = '{{ $p->id }}'; resetPesertaName = '{{ addslashes($p->name) }}'; showResetPassword = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                <i class="fa-solid fa-key"></i> Reset Password
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    {{-- Baris kosong jika hasil pencarian 0 --}}
                    <tr x-show="searchPeserta !== '' && {{ $peserta->count() }} > 0"
                        x-data="{ visible: false }"
                        x-init="$watch('searchPeserta', val => {
                            visible = val !== '' && document.querySelectorAll('#peserta-tbody tr:not([style*=\'display: none\']):not(:last-child)').length === 0
                        })" :class="visible ? '' : 'hidden'">
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-2xl mb-2 block"></i>
                            <span class="text-sm">Tidak ada peserta yang cocok dengan pencarian</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- MODAL: Tambah Mentor --}}
    <div x-show="showAddMentor"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             x-on:click="showAddMentor = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-semibold text-slate-800">Tambah Mentor Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Buat akun mentor untuk sistem</p>
                </div>
                <button x-on:click="showAddMentor = false"
                        class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.mentor.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                    @foreach($errors->all() as $err)
                        <p><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $err }}</p>
                    @endforeach
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Contoh: Budi Santoso"
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="mentor@example.com"
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nomor Telepon / WhatsApp
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           placeholder="08123456789"
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password"
                               placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-2.5 pr-11 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                        <button type="button" x-on:click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation"
                               placeholder="Ulangi password"
                               class="w-full px-4 py-2.5 pr-11 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                        <button type="button" x-on:click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" x-on:click="showAddMentor = false"
                            class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 text-sm font-semibold text-white rounded-xl transition-all"
                            style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 12px rgba(56,189,248,0.30);">
                        <i class="fa-solid fa-user-plus mr-1.5"></i>Tambah Mentor
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Assign Pelatihan --}}
    <div x-show="showAssign"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             x-on:click="showAssign = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-semibold text-slate-800">Assign Pelatihan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Mentor: <span class="font-medium text-slate-700" x-text="assignMentorName"></span>
                    </p>
                </div>
                <button x-on:click="showAssign = false"
                        class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.mentor.assign') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="mentor_id" x-bind:value="assignMentorId">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Pilih Pelatihan <span class="text-red-500">*</span>
                    </label>
                    <select name="pelatihan_id"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 text-slate-700 transition">
                        <option value="">-- Pilih pelatihan --</option>
                        @foreach($pelatihans as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->nama }}
                                @if($p->mentors->isNotEmpty())
                                    (mentor: {{ $p->mentors->pluck('name')->join(', ') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" x-on:click="showAssign = false"
                            class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 text-sm font-semibold text-white rounded-xl transition-all"
                            style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 12px rgba(56,189,248,0.30);">
                        <i class="fa-solid fa-link mr-1.5"></i>Assign
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- MODAL: Reset Password Peserta --}}
    <div x-show="showResetPassword"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             x-on:click="showResetPassword = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-semibold text-slate-800">Reset Password Peserta</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Peserta: <span class="font-medium text-slate-700" x-text="resetPesertaName"></span>
                    </p>
                </div>
                <button x-on:click="showResetPassword = false"
                        class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="" method="POST" class="p-6 space-y-4"
                  x-bind:action="`/admin/users/peserta/${resetPesertaId}/reset-password`">
                @csrf

                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                        <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
                        Password baru yang kamu set akan langsung aktif. Beritahu peserta password barunya.
                    </div>

                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password"
                                   placeholder="Minimal 6 karakter"
                                   class="w-full px-4 py-2.5 pr-11 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-300 transition">
                            <button type="button" x-on:click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                                <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password_confirmation"
                                   placeholder="Ulangi password baru"
                                   class="w-full px-4 py-2.5 pr-11 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-300 transition">
                            <button type="button" x-on:click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                                <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" x-on:click="showResetPassword = false"
                                class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 text-sm font-semibold text-white rounded-xl transition-all"
                                style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); box-shadow: 0 4px 12px rgba(245,158,11,0.30);">
                            <i class="fa-solid fa-key mr-1.5"></i>Reset Password
                        </button>
                    </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    {{-- Buka modal tambah mentor otomatis jika ada error validasi --}}
    @if($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.querySelector('[x-data]').__x;
        if (el) el.$data.showAddMentor = true;
    });
    @endif
</script>
@endpush
