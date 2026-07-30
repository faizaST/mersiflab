{{--
    Partial: Lokasi Picker dengan Search Nominatim
--}}
@php
    $initLat      = $initLat      ?? -6.2088;
    $initLng      = $initLng      ?? 106.8456;
    $initZoom     = $initZoom     ?? 12;
    $lokasiNama   = $lokasiNama   ?? '';
    $lokasiAlamat = $lokasiAlamat ?? '';
    $latValue     = $latValue     ?? '';
    $lngValue     = $lngValue     ?? '';
    $mapId        = 'map-picker-' . uniqid();
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
    <h2 class="font-semibold text-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-map-location-dot text-sky-500"></i> Lokasi Pelatihan
    </h2>

    {{-- Semua nilai PHP disimpan di data-* attribute — tidak ada Blade di dalam <script> --}}
    <div x-data="lokasiSearch('{{ $mapId }}')"
         data-init-lat="{{ $initLat }}"
         data-init-lng="{{ $initLng }}"
         data-init-zoom="{{ $initZoom }}"
         data-has-coord="{{ $latValue && $lngValue ? 'true' : 'false' }}"
         data-lat-value="{{ $latValue }}"
         data-lng-value="{{ $lngValue }}"
         data-lokasi-nama="{{ addslashes($lokasiNama ?: 'Lokasi pelatihan') }}"
         class="space-y-4">

        {{-- Search --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Cari Lokasi
                <span class="text-xs text-slate-400 font-normal ml-1">— ketik nama tempat, pilih dari hasil</span>
            </label>
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text"
                       x-model="query"
                       x-on:input.debounce.500ms="search()"
                       x-on:keydown.escape="results = []"
                       placeholder="Contoh: SMK Negeri 1 Jakarta, Gedung Sate Bandung..."
                       class="w-full pl-9 pr-10 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
                <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <i class="fa-solid fa-spinner fa-spin text-slate-400 text-sm"></i>
                </div>
                <button type="button" x-show="query && !loading"
                        x-on:click="query = ''; results = []"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Dropdown hasil --}}
            <div x-show="results.length > 0" x-cloak
                 class="mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50 relative">
                <template x-for="(r, i) in results" :key="i">
                    <button type="button"
                            x-on:click="pilih(r)"
                            class="w-full text-left px-4 py-3 text-sm hover:bg-sky-50 transition-colors border-b border-slate-100 last:border-0 flex items-start gap-3">
                        <i class="fa-solid fa-map-pin text-sky-400 mt-0.5 shrink-0"></i>
                        <div class="min-w-0">
                            <p class="font-medium text-slate-800 truncate" x-text="r.display_name.split(',')[0]"></p>
                            <p class="text-xs text-slate-400 truncate" x-text="r.display_name"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Field manual --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lokasi / Gedung</label>
                <input type="text" name="lokasi_nama" id="lokasi_nama"
                       value="{{ old('lokasi_nama', $lokasiNama) }}"
                       placeholder="Gedung A, Lantai 3"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Lengkap</label>
                <input type="text" name="lokasi_alamat" id="lokasi_alamat"
                       value="{{ old('lokasi_alamat', $lokasiAlamat) }}"
                       placeholder="Jl. Sudirman No. 1, Jakarta"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Latitude</label>
                <input type="text" name="lat" id="lat"
                       value="{{ old('lat', $latValue) }}"
                       placeholder="-6.208800"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition font-mono">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Longitude</label>
                <input type="text" name="lng" id="lng"
                       value="{{ old('lng', $lngValue) }}"
                       placeholder="106.845600"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 transition font-mono">
            </div>
        </div>

        {{-- Peta --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Peta
                <span class="text-xs text-slate-400 font-normal ml-1">— atau klik langsung di peta untuk pin manual</span>
            </label>
            <div id="{{ $mapId }}" class="w-full h-72 rounded-xl border border-slate-200 overflow-hidden"></div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
/**
 * Lokasi picker dengan Nominatim search.
 * Semua nilai PHP dibaca dari data-* attribute — tidak ada Blade di dalam JS.
 */
function lokasiSearch(mapId) {
    return {
        query:    '',
        results:  [],
        loading:  false,
        searched: false,
        map:      null,
        marker:   null,

        init() {
            this.$nextTick(() => {
                // Baca konfigurasi dari data-* attribute
                const el       = this.$el;
                const initLat  = parseFloat(el.dataset.initLat)  || -6.2088;
                const initLng  = parseFloat(el.dataset.initLng)  || 106.8456;
                const initZoom = parseInt(el.dataset.initZoom)   || 12;
                const hasCoord = el.dataset.hasCoord === 'true';
                const latVal   = parseFloat(el.dataset.latValue) || 0;
                const lngVal   = parseFloat(el.dataset.lngValue) || 0;
                const namaPop  = el.dataset.lokasiNama || 'Lokasi pelatihan';

                // Inisialisasi peta
                this.map = L.map(mapId).setView([initLat, initLng], initZoom);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(this.map);

                // Pasang marker jika sudah ada koordinat
                if (hasCoord && latVal && lngVal) {
                    this.marker = L.marker([latVal, lngVal])
                        .addTo(this.map)
                        .bindPopup('<b>' + namaPop + '</b>')
                        .openPopup();
                }

                // Klik peta = pin manual
                this.map.on('click', (e) => {
                    const lat = parseFloat(e.latlng.lat.toFixed(6));
                    const lng = parseFloat(e.latlng.lng.toFixed(6));
                    this.setPin(lat, lng, null);
                });
            });
        },

        async search() {
            const q = this.query.trim();
            if (q.length < 3) { this.results = []; return; }

            this.loading  = true;
            this.searched = false;

            try {
                const url = 'https://nominatim.openstreetmap.org/search?format=json&q='
                    + encodeURIComponent(q)
                    + '&limit=6&addressdetails=1&accept-language=id';
                const res = await fetch(url, { headers: { 'Accept-Language': 'id' } });
                this.results  = await res.json();
                this.searched = true;
            } catch (_e) {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        pilih(r) {
            const lat   = parseFloat(parseFloat(r.lat).toFixed(6));
            const lng   = parseFloat(parseFloat(r.lon).toFixed(6));
            const nama  = r.display_name.split(',')[0].trim();
            const alamat = r.display_name;

            document.getElementById('lokasi_nama').value   = nama;
            document.getElementById('lokasi_alamat').value = alamat;

            this.setPin(lat, lng, nama);
            this.results = [];
            this.query   = nama;
        },

        setPin(lat, lng, label) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;

            if (this.marker) { this.marker.remove(); }

            const popup = label
                ? '<b>' + label + '</b><br>' + lat + ', ' + lng
                : lat + ', ' + lng;

            this.marker = L.marker([lat, lng])
                .addTo(this.map)
                .bindPopup(popup)
                .openPopup();

            this.map.setView([lat, lng], 16);
        }
    };
}
</script>
@endpush
@endonce
