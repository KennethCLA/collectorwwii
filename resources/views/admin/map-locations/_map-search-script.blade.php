<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    (() => {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const initialLat = parseFloat(latInput.value);
        const initialLng = parseFloat(lngInput.value);
        const hasInitial = Number.isFinite(initialLat) && Number.isFinite(initialLng);
        const defaultCenter = hasInitial ? [initialLat, initialLng] : [50.8503, 4.3517];

        const map = L.map('map-picker').setView(defaultCenter, hasInitial ? 8 : 5);
        const searchInput = document.getElementById('map-search');
        const searchBtn = document.getElementById('map-search-btn');
        const searchResults = document.getElementById('map-search-results');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        let marker = hasInitial ? L.marker(defaultCenter).addTo(map) : null;

        function setMarker(lat, lng) {
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);

            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }
        }

        map.on('click', (e) => {
            setMarker(e.latlng.lat, e.latlng.lng);
        });

        [latInput, lngInput].forEach((input) => {
            input.addEventListener('change', () => {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                setMarker(lat, lng);
                map.panTo([lat, lng]);
            });
        });

        let searchTimer = null;
        let searchController = null;
        let lastSearchTime = 0;
        const MIN_INTERVAL = 1100; // Nominatim: max 1 request per second

        function normalizeNominatim(rows) {
            return (Array.isArray(rows) ? rows : []).map((row) => ({
                lat: parseFloat(row.lat),
                lng: parseFloat(row.lon),
                label: row.display_name,
            })).filter((row) => Number.isFinite(row.lat) && Number.isFinite(row.lng) && row.label);
        }

        function normalizePhoton(rows) {
            return (Array.isArray(rows) ? rows : []).map((row) => {
                const coords = row?.geometry?.coordinates ?? [];
                const props = row?.properties ?? {};
                const labelParts = [props.name, props.city, props.country].filter(Boolean);
                return {
                    lat: parseFloat(coords[1]),
                    lng: parseFloat(coords[0]),
                    label: labelParts.join(', ') || props.name || '',
                };
            }).filter((row) => Number.isFinite(row.lat) && Number.isFinite(row.lng) && row.label);
        }

        async function fetchSuggestions(q) {
            if (searchController) {
                searchController.abort();
            }

            searchController = new AbortController();
            const signal = searchController.signal;

            // Rate-limit: wait if needed
            const now = Date.now();
            const elapsed = now - lastSearchTime;
            if (elapsed < MIN_INTERVAL) {
                await new Promise((resolve) => setTimeout(resolve, MIN_INTERVAL - elapsed));
            }
            lastSearchTime = Date.now();

            let nominatimRes;
            try {
                nominatimRes = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=jsonv2&limit=6&q=${encodeURIComponent(q)}`, {
                        signal
                    }
                );
            } catch (e) {
                if (e.name === 'AbortError') throw e;
                throw new Error('offline');
            }

            if (nominatimRes.status === 429) {
                throw new Error('rate_limited');
            }

            if (!nominatimRes.ok) {
                throw new Error('service_unavailable');
            }

            const nominatim = normalizeNominatim(await nominatimRes.json());
            if (nominatim.length > 0) {
                return nominatim;
            }

            let photonRes;
            try {
                photonRes = await fetch(
                    `https://photon.komoot.io/api/?limit=6&q=${encodeURIComponent(q)}`, {
                        signal
                    }
                );
            } catch (e) {
                if (e.name === 'AbortError') throw e;
                return []; // Photon failed but Nominatim returned 0 — still "no results"
            }

            if (!photonRes.ok) {
                return [];
            }

            const photonJson = await photonRes.json();
            return normalizePhoton(photonJson?.features ?? []);
        }

        function renderSuggestions(data) {
            searchResults.classList.remove('hidden');
            if (!Array.isArray(data) || data.length === 0) {
                searchResults.innerHTML = '<p class="px-2 py-1 text-xs text-white/70">No results found. Try a different term or place the pin manually.</p>';
                return;
            }

            searchResults.innerHTML = data.map((row) => `
                <button type="button"
                    class="block w-full rounded px-2 py-2 text-left text-xs text-white/90 hover:bg-white/10"
                    data-lat="${row.lat}" data-lng="${row.lng}">
                    ${row.label}
                </button>
            `).join('');

            searchResults.querySelectorAll('button[data-lat]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const lat = parseFloat(btn.dataset.lat);
                    const lng = parseFloat(btn.dataset.lng);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                    setMarker(lat, lng);
                    map.setView([lat, lng], 12, {
                        animate: true
                    });
                    searchResults.classList.add('hidden');
                });
            });
        }

        function errorMessage(e) {
            if (e.message === 'rate_limited') {
                return 'Too many requests. Please wait a moment and try again.';
            }
            if (e.message === 'offline' || !navigator.onLine) {
                return 'You appear to be offline. Check your connection and try again.';
            }
            if (e.message === 'service_unavailable') {
                return 'Search service is temporarily unavailable. Try again later.';
            }
            return 'Search failed. Try again.';
        }

        async function searchLocation() {
            const q = searchInput.value.trim();
            if (!q) return;

            searchBtn.disabled = true;
            searchBtn.textContent = '...';
            searchResults.classList.remove('hidden');
            searchResults.innerHTML = '<p class="px-2 py-1 text-xs text-white/70">Searching...</p>';

            try {
                const data = await fetchSuggestions(q);
                renderSuggestions(data);
            } catch (e) {
                if (e.name !== 'AbortError') {
                    searchResults.innerHTML = `<p class="px-2 py-1 text-xs text-red-200">${errorMessage(e)}</p>`;
                }
            } finally {
                searchBtn.disabled = false;
                searchBtn.textContent = 'Search';
            }
        }

        searchBtn.addEventListener('click', searchLocation);
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchLocation();
            }
        });

        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim();
            clearTimeout(searchTimer);

            if (q.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }

            // Show throttle hint if typing too fast
            const elapsed = Date.now() - lastSearchTime;
            if (elapsed < MIN_INTERVAL && lastSearchTime > 0) {
                searchResults.classList.remove('hidden');
                searchResults.innerHTML = '<p class="px-2 py-1 text-xs text-white/50">Please wait...</p>';
            }

            searchTimer = setTimeout(async () => {
                try {
                    const data = await fetchSuggestions(q);
                    renderSuggestions(data);
                } catch (e) {
                    if (e.name !== 'AbortError') {
                        searchResults.classList.remove('hidden');
                        searchResults.innerHTML = `<p class="px-2 py-1 text-xs text-red-200">${errorMessage(e)}</p>`;
                    }
                }
            }, 1100);
        });
    })();
</script>
