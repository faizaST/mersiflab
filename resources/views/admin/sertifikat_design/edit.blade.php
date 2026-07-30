@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumbs & Header --}}
    <div class="space-y-2">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
            <a href="{{ route('admin.desain-sertifikat.index') }}" class="hover:text-sky-600 transition">Desain Sertifikat</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600">Sesuaikan Desain</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Kelola Desain Sertifikat</h1>
            <p class="text-sm text-slate-500 mt-0.5">Program Pelatihan: <span class="font-bold text-slate-700">{{ $pelatihan->nama }}</span></p>
        </div>
    </div>

    {{-- Validation Errors --}}
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

    {{-- Main Editor Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ 
        previewTemplate: '{{ $pelatihan->sertifikat_template ? Storage::url($pelatihan->sertifikat_template) : '' }}',
        previewLogoKiri: '{{ $pelatihan->sertifikat_logo_kiri ? Storage::url($pelatihan->sertifikat_logo_kiri) : '' }}',
        previewLogoKanan: '{{ $pelatihan->sertifikat_logo_kanan ? Storage::url($pelatihan->sertifikat_logo_kanan) : '' }}',
        hapusTemplate: false,
        hapusLogoKiri: false,
        hapusLogoKanan: false
    }">

        {{-- Left Form Panel (col-span-2) --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('admin.desain-sertifikat.update', $pelatihan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Card Background Template --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-start gap-3 border-b border-slate-100 pb-3">
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                            <i class="fa-solid fa-file-invoice text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">Template Background</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Unggah gambar desain sertifikat Anda sendiri untuk menggantikan template latar belakang default.</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <div class="w-32 h-20 rounded-xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden bg-slate-50 shrink-0">
                            <template x-if="previewTemplate && !hapusTemplate">
                                <img :src="previewTemplate" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previewTemplate || hapusTemplate">
                                <i class="fa-solid fa-file-invoice text-slate-300 text-2xl"></i>
                            </template>
                        </div>
                        <div class="flex-1 w-full space-y-2">
                            <input type="file" name="sertifikat_template" accept="image/*"
                                   @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { previewTemplate = e.target.result; hapusTemplate = false; }; reader.readAsDataURL(file); }"
                                   class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition">
                            <p class="text-[10px] text-slate-400">Rekomendasi rasio landscape A4, Max 5MB (JPG, PNG). Desain harus menyisakan ruang kosong di tengah untuk nama peserta.</p>

                            @if($pelatihan->sertifikat_template)
                            <div class="flex items-center gap-2 pt-1">
                                <input type="checkbox" name="hapus_template" id="hapus_template" value="1" x-model="hapusTemplate"
                                       class="w-4 h-4 rounded text-red-600 border-slate-300 focus:ring-red-300">
                                <label for="hapus_template" class="text-xs font-semibold text-red-600 cursor-pointer flex items-center gap-1 select-none">
                                    <i class="fa-solid fa-trash-can"></i> Hapus template kustom & kembali ke template default
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card Header Logos --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <div class="flex items-start gap-3 border-b border-slate-100 pb-3">
                        <div class="p-2 bg-sky-50 text-sky-600 rounded-xl">
                            <i class="fa-solid fa-image text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">Logo Header Sertifikat (Opsional)</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Unggah logo kustom untuk diletakkan di bagian atas sertifikat. Kosongkan untuk menggunakan logo default.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        
                        {{-- Logo Kiri --}}
                        <div class="space-y-3 p-4 bg-slate-50/50 border border-slate-100 rounded-2xl">
                            <label class="block text-sm font-semibold text-slate-700">Logo Kiri (Header)</label>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden bg-white shrink-0">
                                    <template x-if="previewLogoKiri && !hapusLogoKiri">
                                        <img :src="previewLogoKiri" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!previewLogoKiri || hapusLogoKiri">
                                        <div class="text-[10px] font-bold text-slate-400 text-center uppercase leading-none">ENUMA</div>
                                    </template>
                                </div>
                                <div class="flex-1 w-full space-y-1.5">
                                    <input type="file" name="sertifikat_logo_kiri" accept="image/*"
                                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { previewLogoKiri = e.target.result; hapusLogoKiri = false; }; reader.readAsDataURL(file); }"
                                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 transition">
                                    <p class="text-[10px] text-slate-400 leading-tight">Gantikan logo default 'Enuma'. Max 5MB (PNG transparan disarankan).</p>

                                    @if($pelatihan->sertifikat_logo_kiri)
                                    <div class="flex items-center gap-1.5 pt-0.5">
                                        <input type="checkbox" name="hapus_logo_kiri" id="hapus_logo_kiri" value="1" x-model="hapusLogoKiri"
                                               class="w-3.5 h-3.5 rounded text-red-600 border-slate-300 focus:ring-red-300">
                                        <label for="hapus_logo_kiri" class="text-[10px] font-bold text-red-600 cursor-pointer select-none">
                                            <i class="fa-solid fa-trash-can mr-0.5"></i> Hapus & Gunakan Default
                                        </label>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Logo Kanan --}}
                        <div class="space-y-3 p-4 bg-slate-50/50 border border-slate-100 rounded-2xl">
                            <label class="block text-sm font-semibold text-slate-700">Logo Kanan (Header)</label>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden bg-white shrink-0">
                                    <template x-if="previewLogoKanan && !hapusLogoKanan">
                                        <img :src="previewLogoKanan" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!previewLogoKanan || hapusLogoKanan">
                                        <div class="text-[10px] font-bold text-slate-400 text-center uppercase leading-none">Mersif<br><span class="text-[8px] text-slate-300">Lab</span></div>
                                    </template>
                                </div>
                                <div class="flex-1 w-full space-y-1.5">
                                    <input type="file" name="sertifikat_logo_kanan" accept="image/*"
                                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { previewLogoKanan = e.target.result; hapusLogoKanan = false; }; reader.readAsDataURL(file); }"
                                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 transition">
                                    <p class="text-[10px] text-slate-400 leading-tight">Gantikan logo 'MersifLab'. Max 5MB (PNG transparan disarankan).</p>

                                    @if($pelatihan->sertifikat_logo_kanan)
                                    <div class="flex items-center gap-1.5 pt-0.5">
                                        <input type="checkbox" name="hapus_logo_kanan" id="hapus_logo_kanan" value="1" x-model="hapusLogoKanan"
                                               class="w-3.5 h-3.5 rounded text-red-600 border-slate-300 focus:ring-red-300">
                                        <label for="hapus_logo_kanan" class="text-[10px] font-bold text-red-600 cursor-pointer select-none">
                                            <i class="fa-solid fa-trash-can mr-0.5"></i> Hapus & Gunakan Default
                                        </label>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-3 text-white font-bold rounded-xl shadow-md transition-all"
                            style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 14px rgba(56,189,248,0.35);">
                        <i class="fa-solid fa-save"></i>
                        Simpan Pengaturan Sertifikat
                    </button>
                    <a href="{{ route('admin.desain-sertifikat.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-3 border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 font-semibold rounded-xl transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Right Live Mockup Panel --}}
        <div class="space-y-6">
            
            {{-- Live Mockup Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <h3 class="font-bold text-slate-800 text-base">Visualisasi Sertifikat</h3>
                
                {{-- Certificate Canvas Box --}}
                <div class="w-full aspect-[1.414/1] rounded-xl border border-slate-300 relative overflow-hidden bg-white shadow-inner flex flex-col p-3">
                    
                    {{-- Dynamic Background & Decorative Frames --}}
                    <div class="absolute inset-0 z-0 select-none pointer-events-none">
                        <template x-if="previewTemplate && !hapusTemplate">
                            <img :src="previewTemplate" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!previewTemplate || hapusTemplate">
                            <div class="w-full h-full bg-white relative">
                                {{-- Background Circuit Lines Sim --}}
                                <div class="absolute inset-0 opacity-[0.02]" style="background-image: url('data:image/svg+xml,%3Csvg width=&apos;20&apos; height=&apos;20&apos; viewBox=&apos;0 0 60 60&apos; xmlns=&apos;http://www.w3.org/2000/svg&apos;%3E%3Cg fill=&apos;none&apos; fill-rule=&apos;evenodd&apos;%3E%3Cg fill=&apos;%230f4c75&apos; fill-opacity=&apos;0.5&apos;%3E%3Cpath d=&apos;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2v-4h4v-2H6z&apos;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                                
                                {{-- Outer Frame Border (Navy) --}}
                                <div class="absolute top-[2%] left-[2%] right-[2%] bottom-[2%] border-[0.75px] border-[#0f4c75]"></div>
                                
                                {{-- Inner Frame Border (Gold) --}}
                                <div class="absolute top-[3%] left-[3%] right-[3%] bottom-[3%] border-[0.5px] border-[#d4af37]"></div>
                                
                                {{-- Corner Decorations --}}
                                <div class="absolute top-0 left-0 w-12 h-12 opacity-10" style="background: linear-gradient(135deg, #0f4c75 12%, transparent 12%) 0 0, linear-gradient(135deg, #d4af37 8%, transparent 8%) 0 0;"></div>
                                <div class="absolute top-[4%] right-[4%] w-6 h-6 border-t-[0.5px] border-r-[0.5px] border-[#d4af37] opacity-35"></div>
                                <div class="absolute bottom-0 right-0 w-12 h-12 opacity-10" style="background: linear-gradient(315deg, #0f4c75 12%, transparent 12%) 0 0, linear-gradient(315deg, #d4af37 8%, transparent 8%) 0 0;"></div>
                                <div class="absolute bottom-[4%] left-[4%] w-6 h-6 border-b-[0.5px] border-l-[0.5px] border-[#d4af37] opacity-35"></div>

                                {{-- Watermark --}}
                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rotate-[-28deg] text-3xl font-extrabold text-[#0f4c75]/[0.02] tracking-wider uppercase">MERSIFLAB</div>
                            </div>
                        </template>
                    </div>

                    {{-- Foreground Elements --}}
                    <div class="relative z-10 w-full h-full flex flex-col justify-between select-none p-[2%]">
                        
                        {{-- Mockup Header Logos --}}
                        <div class="text-center w-full leading-none pt-[1%] select-none">
                            <div class="inline-flex items-center justify-center">
                                <div class="flex items-center justify-center" style="height: 12px;">
                                    <template x-if="previewLogoKiri && !hapusLogoKiri">
                                        <img :src="previewLogoKiri" class="max-h-full w-auto object-contain">
                                    </template>
                                    <template x-if="!previewLogoKiri || hapusLogoKiri">
                                        <img src="{{ asset('img/logo-enuma.png') }}" class="max-h-full w-auto object-contain">
                                    </template>
                                </div>
                                <span class="inline-block" style="width: 12px;"></span>
                                <div class="flex items-center justify-center" style="height: 12px;">
                                    <template x-if="previewLogoKanan && !hapusLogoKanan">
                                        <img :src="previewLogoKanan" class="max-h-full w-auto object-contain">
                                    </template>
                                    <template x-if="!previewLogoKanan || hapusLogoKanan">
                                        <img src="{{ asset('img/logo-mersfilab.png') }}" class="max-h-full w-auto object-contain">
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Mockup Main Content --}}
                        <div class="text-center space-y-0.5">
                            <div class="text-[10px] font-extrabold text-[#0c2340] tracking-[1.5px] uppercase leading-none">Sertifikat</div>
                            <div class="text-[4px] text-slate-400 font-semibold leading-none tracking-[0.5px] mt-[1px]">No. CERT-0001/05/2026</div>
                            
                            <div class="text-[4px] text-slate-400 font-bold uppercase tracking-[1px] pt-1 leading-none">Diberikan kepada:</div>
                            
                            <div class="inline-block border-b-[0.75px] border-[#d4af37] pb-[1px] min-w-[60%] mx-auto mt-[1px]">
                                <div class="text-[9px] font-bold text-[#0c2340] font-serif uppercase tracking-[0.2px] leading-none">Nama Peserta Pelatihan</div>
                            </div>
                            
                            <div class="text-[4px] text-slate-400 font-semibold leading-none pt-0.5">Atas kelulusannya pada program pelatihan:</div>
                            
                            <div class="inline-block border border-[#c5a880] p-[0.5px] rounded-full mt-[1px]">
                                <div class="bg-[#fbfbf8] border border-dashed border-[#d4af37] px-3 py-[1px] rounded-full text-[4.5px] font-extrabold text-[#0f4c75] tracking-[0.2px] uppercase leading-none">
                                    {{ Str::limit($pelatihan->nama, 35) }}
                                </div>
                            </div>
                        </div>

                        {{-- Mockup Bottom Signatures --}}
                        <div class="w-full px-[8%] pb-[2%] select-none">
                            <div class="flex items-end justify-between text-center">
                                
                                {{-- Left: Mentor Utama --}}
                                <div class="w-[45%] flex flex-col items-center">
                                    <span class="text-[4.5px] font-bold text-slate-400 uppercase tracking-[0.2px] leading-none mb-[2px]">Mentor Utama</span>
                                    <div class="w-full relative flex items-center justify-center my-[1px]" style="height: 18px;">
                                        <img src="{{ asset('img/ttd-mentor.png') }}" class="opacity-80 max-h-full w-auto object-contain">
                                    </div>
                                    <div class="w-[70%] border-t-[0.5px] border-slate-300 pt-[1px] mt-[1px]">
                                        <span class="text-[4.5px] font-bold text-[#0c2340] leading-none block">Mentor Pengampu</span>
                                    </div>
                                </div>

                                {{-- Right: Direktur MersifLab --}}
                                <div class="w-[45%] flex flex-col items-center">
                                    <span class="text-[4.5px] font-bold text-slate-400 uppercase tracking-[0.2px] leading-none mb-[2px]">Direktur MersifLab</span>
                                    <div class="w-full relative flex items-center justify-center my-[1px]" style="height: 18px;">
                                        {{-- Signature --}}
                                        <img src="{{ asset('img/ttd-direktur.png') }}" class="opacity-80 max-h-full w-auto object-contain relative z-10">
                                        {{-- Stamp cap-enuma on top of signature, offset to left --}}
                                        <img src="{{ asset('img/cap-enuma.png') }}" class="absolute opacity-75 z-20" style="height: 22px; top: -3px; left: 24%; width: auto; object-fit: contain;">
                                    </div>
                                    <div class="w-[75%] border-t-[0.5px] border-slate-300 pt-[1px] mt-[1px]">
                                        <span class="text-[4.5px] font-bold text-[#0c2340] leading-none block">Andreas Wegiq Adia H., M.T.</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="text-xs text-slate-400 bg-slate-50 p-3.5 rounded-xl border border-slate-100 leading-relaxed">
                    <i class="fa-solid fa-info-circle text-sky-500 mr-0.5"></i>
                    <strong>Panduan Visual:</strong> Kotak di atas menyimulasikan layout sertifikat A4 lanskap Anda. Mengunggah latar belakang khusus atau logo khusus di formulir sebelah kiri akan memperbarui tampilan simulasi secara real-time.
                </div>
            </div>

            {{-- Helper Tips Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-lightbulb text-amber-500 text-base"></i>
                    Tips Pengaturan Desain
                </h3>
                <ul class="text-xs text-slate-500 space-y-2.5 list-disc list-inside">
                    <li><strong>Format Berkas:</strong> Selalu gunakan berkas gambar dengan format JPG atau PNG berkualitas tinggi untuk background.</li>
                    <li><strong>Logo Transparan:</strong> Disarankan menggunakan logo dengan latar belakang transparan (berkas <strong>PNG</strong>) agar menyatu dengan latar belakang sertifikat.</li>
                    <li><strong>Tata Letak Teks:</strong> Template latar belakang kustom sebaiknya tidak mengandung tulisan dinamis seperti nama peserta, nomor sertifikat, atau tanda tangan, karena bagian tersebut akan ditulis oleh sistem.</li>
                </ul>
            </div>

        </div>

    </div>

</div>
@endsection
