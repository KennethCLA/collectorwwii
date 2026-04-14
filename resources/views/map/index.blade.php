<x-layout :mainClass="'w-full px-4 py-6 sm:px-6 lg:px-8'">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <div class="mx-auto w-full max-w-7xl space-y-4 pt-6">
        <div class="rounded-2xl bg-black/20 p-4 ring-1 ring-black/30 sm:p-6 noise-texture">
            <p class="font-stencil text-xs tracking-[0.4em] text-khaki/60 uppercase mb-1">Kriegsschauplatz · Operationskarte</p>
            <h1 class="font-stencil text-3xl font-black tracking-[0.2em] text-white uppercase">LAGEBERICHT</h1>
            <p class="font-mono text-[10px] tracking-[0.25em] text-white/40 mt-1 uppercase">Feldkarte · WK II Standorte · Klicken für Details</p>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="relative rounded-2xl bg-black/20 p-3 ring-1 ring-black/30 sm:p-4">
                <div id="visit-map" class="h-[65vh] min-h-[420px] w-full rounded-xl"></div>
                <div class="pointer-events-none absolute inset-3 sm:inset-4 rounded-xl"
                     style="background-image:
                         repeating-linear-gradient(0deg, rgba(194,178,128,0.06) 0px, transparent 1px, transparent 60px, rgba(194,178,128,0.06) 60px),
                         repeating-linear-gradient(90deg, rgba(194,178,128,0.06) 0px, transparent 1px, transparent 60px, rgba(194,178,128,0.06) 60px);
                     background-size: 60px 60px;">
                </div>
            </div>

            <aside class="rounded-2xl bg-black/20 p-4 ring-1 ring-black/30">
                <h2 class="font-stencil text-[11px] uppercase tracking-[0.25em] text-khaki/70">STANDORTE</h2>
                <div class="mt-3 space-y-2 max-h-[65vh] overflow-y-auto pr-1">
                    @forelse($locations as $loc)
                    <button
                        type="button"
                        class="w-full rounded-lg bg-white/10 px-3 py-2 text-left text-white transition hover:bg-white/20"
                        onclick="focusMarker({{ $loc['id'] }})">
                        <div class="text-sm font-semibold">{{ $loc['name'] }}</div>
                        <div class="text-xs text-white/70">{{ $loc['coordinates'] }}</div>
                    </button>
                    @empty
                    <p class="text-sm text-white/60">No map locations yet.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        const locations = @json($locations);
        const map = L.map('visit-map', {
            zoomControl: true,
            minZoom: 2,
        }).setView([50.8503, 4.3517], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = {};
        const bounds = [];

        const popupHtml = (loc) => {
            const desc = (loc.description || '').replace(/\n/g, '<br>');
            const imageThumbs = (loc.images || []).slice(0, 4).map((url, idx) =>
                `<a href="${url}" data-fancybox="map-${loc.id}" class="block overflow-hidden rounded-md ring-1 ring-white/20">
                    <img src="${url}" alt="${loc.name} photo ${idx + 1}" class="h-16 w-24 object-cover" />
                </a>`
            ).join('');

            const hiddenLinks = (loc.images || []).slice(4).map((url) =>
                `<a href="${url}" data-fancybox="map-${loc.id}" class="hidden">Photo</a>`
            ).join('');

            return `
                <div class="space-y-2" style="min-width: 250px; max-width: 300px;">
                    <div>
                        <div style="font-weight:700; font-size:14px;">${loc.name}</div>
                        <div style="font-size:12px; color:#4b5563;">${loc.coordinates}</div>
                    </div>
                    ${desc ? `<div style="font-size:13px; line-height:1.4;">${desc}</div>` : ''}
                    ${(loc.images || []).length ? `<div class="grid grid-cols-2 gap-1">${imageThumbs}</div>` : ''}
                    ${hiddenLinks}
                </div>`;
        };

        locations.forEach((loc) => {
            const marker = L.marker([loc.lat, loc.lng]).addTo(map);
            marker.bindPopup(popupHtml(loc));
            markers[loc.id] = marker;
            bounds.push([loc.lat, loc.lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, {
                padding: [40, 40]
            });
        }

        map.on('popupopen', function(e) {
            const popupEl = e.popup.getElement();
            if (!popupEl) return;
            popupEl.querySelectorAll('[data-fancybox]').forEach(function(el) {
                el.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    const gallery = el.dataset.fancybox;
                    const anchors = Array.from(popupEl.querySelectorAll('[data-fancybox="' + gallery + '"]'));
                    const items = anchors.map(function(a) { return { src: a.href, type: 'image' }; });
                    const idx = anchors.indexOf(el);
                    window.Fancybox.show(items, { startIndex: Math.max(0, idx) });
                });
            });
        });

        window.focusMarker = (id) => {
            const marker = markers[id];
            if (!marker) return;
            map.setView(marker.getLatLng(), Math.max(map.getZoom(), 8), {
                animate: true
            });
            marker.openPopup();
        };
    </script>
</x-layout>
