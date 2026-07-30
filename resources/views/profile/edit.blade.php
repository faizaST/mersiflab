@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{ showPass: false, showDeleteModal: false }">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Profil Saya</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola informasi akun Anda</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
        <i class="fa-solid fa-circle-check shrink-0"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('success_password'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
        <i class="fa-solid fa-circle-check shrink-0"></i> {{ session('success_password') }}
    </div>
    @endif

    {{-- Avatar & Info --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-5">
            <div class="relative shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&size=80&bold=true"
                     class="w-20 h-20 rounded-2xl border-2 border-sky-100" alt="">
            </div>
            <div>
                <h3 class="font-semibold text-slate-800 text-lg">{{ $user->name }}</h3>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $user->role === 'admin' ? 'bg-red-50 text-red-700' : ($user->role === 'mentor' ? 'bg-purple-50 text-purple-700' : 'bg-sky-50 text-sky-700') }} capitalize">
                        <i class="fa-solid {{ $user->role === 'admin' ? 'fa-shield-halved' : ($user->role === 'mentor' ? 'fa-chalkboard-user' : 'fa-user') }} text-[10px]"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="text-xs text-slate-400">Bergabung {{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Profil --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-800 mb-5 flex items-center gap-2">
            <i class="fa-solid fa-user-pen text-sky-500"></i> Informasi Profil
        </h2>

        @php
            $profilRoute = match($user->role) {
                'admin'  => route('admin.profil.update'),
                'mentor' => route('mentor.profil.update'),
                default  => route('peserta.profil.update'),
            };
        @endphp

        <form action="{{ $profilRoute }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            @if($errors->hasAny(['name', 'email', 'phone', 'address']))
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                @foreach($errors->only(['name','email','phone','address']) as $err)
                    <p><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $err }}</p>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Telepon</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                       placeholder="+62 812 3456 7890"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat</label>
                <textarea name="address" rows="2" placeholder="Jl. Contoh No. 1, Jakarta"
                          class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition resize-none">{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm shadow-sky-200 transition-all">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Ganti Password --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-800 mb-5 flex items-center gap-2">
            <i class="fa-solid fa-lock text-sky-500"></i> Ganti Password
        </h2>

        @php
            $passwordRoute = match($user->role) {
                'admin'  => route('admin.profil.password'),
                'mentor' => route('mentor.profil.password'),
                default  => route('peserta.profil.password'),
            };
        @endphp

        <form action="{{ $passwordRoute }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            @if($errors->has('current_password') || $errors->has('password'))
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                @foreach($errors->only(['current_password','password']) as $err)
                    <p><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $err }}</p>
                @endforeach
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Saat Ini</label>
                <div class="relative" x-data="{ show: false }">
                    <input x-ref="pwdCurrent"
                           type="password"
                           name="current_password" required
                           placeholder="Masukkan password saat ini"
                           class="w-full px-4 py-2.5 pr-10 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                    <button type="button"
                            x-on:click="show = !show; $refs.pwdCurrent.type = show ? 'text' : 'password'"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <i x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru</label>
                    <div class="relative" x-data="{ show: false }">
                        <input x-ref="pwdNew"
                               type="password"
                               name="password" required
                               placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-2.5 pr-10 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                        <button type="button"
                                x-on:click="show = !show; $refs.pwdNew.type = show ? 'text' : 'password'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <i x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-sm"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <input x-ref="pwdConfirm"
                               type="password"
                               name="password_confirmation" required
                               placeholder="Ulangi password baru"
                               class="w-full px-4 py-2.5 pr-10 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                        <button type="button"
                                x-on:click="show = !show; $refs.pwdConfirm.type = show ? 'text' : 'password'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <i x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm shadow-sky-200 transition-all">
                    <i class="fa-solid fa-key mr-1.5"></i>Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- Hapus Akun --}}
    <div class="bg-white rounded-2xl border border-red-200 shadow-sm p-6">
        <h2 class="font-semibold text-red-700 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i> Zona Berbahaya
        </h2>
        <p class="text-sm text-slate-500 mb-4">Menghapus akun bersifat permanen dan tidak dapat dibatalkan.</p>
        <button type="button" x-on:click="showDeleteModal = true"
                class="px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl transition-colors">
            <i class="fa-solid fa-trash mr-1.5"></i>Hapus Akun
        </button>
    </div>

    {{-- Modal Konfirmasi Hapus Akun --}}
    <div x-show="showDeleteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" x-on:click="showDeleteModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Hapus Akun?</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            <p class="text-sm text-slate-600 mb-5">
                Masukkan password Anda untuk mengkonfirmasi penghapusan akun
                <span class="font-semibold text-slate-800">{{ $user->email }}</span>.
            </p>

            @php
                $destroyRoute = match($user->role) {
                    'admin'  => route('admin.profil.destroy'),
                    'mentor' => route('mentor.profil.destroy'),
                    default  => route('peserta.profil.destroy'),
                };
            @endphp

            <form action="{{ $destroyRoute }}" method="POST" class="space-y-4">
                @csrf
                @method('DELETE')
                <div class="relative" x-data="{ show: false }">
                    <input x-ref="pwdDelete"
                           type="password"
                           name="password"
                           placeholder="Password Anda"
                           class="w-full px-4 py-2.5 pr-10 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    <button type="button"
                            x-on:click="show = !show; $refs.pwdDelete.type = show ? 'text' : 'password'"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <i x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-sm"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                <div class="flex gap-3">
                    <button type="button" x-on:click="showDeleteModal = false"
                            class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                        <i class="fa-solid fa-trash mr-1.5"></i>Ya, Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
