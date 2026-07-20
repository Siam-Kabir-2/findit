import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ── Scroll-reveal observer ── */
    if (reduceMotion) {
        document.querySelectorAll('.reveal').forEach((el) => el.classList.add('visible'));
    } else {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        // Stagger siblings for grid items
                        const siblings = entry.target.parentElement?.querySelectorAll('.reveal');
                        if (siblings && siblings.length > 1) {
                            const idx = Array.from(siblings).indexOf(entry.target);
                            entry.target.style.transitionDelay = `${idx * 60}ms`;
                        }
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.08 }
        );
        document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
    }

    /* ── Mobile nav toggle ── */
    const toggle = document.querySelector('[data-nav-toggle]');
    const mobileNav = document.querySelector('[data-mobile-nav]');
    if (toggle && mobileNav) {
        toggle.addEventListener('click', () => {
            const open = mobileNav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            // Animate hamburger ↔ X
            const icon = toggle.querySelector('svg');
            if (icon) {
                icon.style.transform = open ? 'rotate(90deg)' : 'rotate(0)';
                icon.style.transition = 'transform 200ms ease';
            }
        });
    }

    /* ── Alert dismiss ── */
    document.querySelectorAll('[data-alert-dismiss]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const alert = btn.closest('[data-alert]');
            if (!alert) return;
            alert.style.transition = 'opacity 200ms ease, transform 200ms ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';
            setTimeout(() => alert.remove(), 220);
        });
    });

    /* ── Stat counter animation ── */
    if (!reduceMotion) {
        document.querySelectorAll('.stat strong, .hero-stat strong').forEach((el) => {
            const target = parseInt(el.textContent, 10);
            if (isNaN(target) || target <= 0) return;
            const duration = 800;
            const start = performance.now();
            el.textContent = '0';
            const animate = (now) => {
                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                // Ease out cubic
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(eased * target);
                if (progress < 1) requestAnimationFrame(animate);
            };
            // Only animate when visible
            const io = new IntersectionObserver(([e]) => {
                if (e.isIntersecting) {
                    requestAnimationFrame(animate);
                    io.disconnect();
                }
            }, { threshold: 0.5 });
            io.observe(el);
        });
    }

    /* ── Smooth scroll for anchor links ── */
    document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const id = a.getAttribute('href');
            const target = document.querySelector(id);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ── Audit timeline filters ── */
    const auditFilters = document.querySelector('[data-audit-filters]');
    const auditList = document.querySelector('[data-audit-list]');
    const auditEmpty = document.querySelector('[data-audit-empty]');
    if (auditFilters && auditList) {
        const events = auditList.querySelectorAll('.audit-event');
        auditFilters.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-filter]');
            if (!btn) return;
            const filter = btn.dataset.filter;
            auditFilters.querySelectorAll('.chip').forEach((chip) => chip.classList.toggle('active', chip === btn));
            let visible = 0;
            events.forEach((event) => {
                const match = filter === 'all'
                    || event.dataset.entity === filter
                    || event.dataset.action === filter;
                event.hidden = !match;
                if (match) visible += 1;
            });
            if (auditEmpty) auditEmpty.hidden = visible > 0;
        });
    }

    /* ── File upload preview (create/edit forms) ── */
    const fileInput = document.querySelector('input[type="file"][accept*="image"]');
    if (fileInput) {
        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (!file) return;
            let preview = fileInput.parentElement.querySelector('.upload-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'upload-preview';
                preview.style.cssText = 'margin-top:0.75rem;border-radius:var(--radius-sm);overflow:hidden;max-width:200px;border:1px solid var(--line);';
                fileInput.parentElement.appendChild(preview);
            }
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.cssText = 'width:100%;display:block;';
            img.onload = () => URL.revokeObjectURL(img.src);
            preview.innerHTML = '';
            preview.appendChild(img);
        });
    }

    /* ── Location select → live map preview ── */
    const locationSelect = document.querySelector('[data-location-select]');
    const locationPreview = document.querySelector('[data-location-preview]');
    if (locationSelect && locationPreview) {
        const previewMap = locationPreview.querySelector('[data-map]');
        const previewLabel = locationPreview.querySelector('[data-location-preview-label]');
        let leafletMap = null;
        let leafletMarker = null;

        const refreshPreview = () => {
            const option = locationSelect.selectedOptions[0];
            const lat = Number(option?.dataset?.lat);
            const lng = Number(option?.dataset?.lng);
            const label = option?.dataset?.label || option?.textContent?.trim() || 'Location';
            if (!Number.isFinite(lat) || !Number.isFinite(lng) || !option?.value) {
                locationPreview.hidden = true;
                return;
            }
            locationPreview.hidden = false;
            if (previewLabel) previewLabel.textContent = `${label} · KUET, Khulna`;
            if (previewMap) {
                previewMap.dataset.lat = String(lat);
                previewMap.dataset.lng = String(lng);
                previewMap.dataset.label = label;
            }
            if (leafletMap && leafletMarker && window.L) {
                leafletMarker.setLatLng([lat, lng]);
                leafletMarker.bindPopup(label);
                leafletMap.setView([lat, lng], 17);
                requestAnimationFrame(() => leafletMap.invalidateSize());
            }
        };

        locationSelect.addEventListener('change', refreshPreview);
        // Expose hook so map init can bind instances after Leaflet loads
        locationPreview._finditRefresh = refreshPreview;
        locationPreview._finditBindMap = (map, marker) => {
            leafletMap = map;
            leafletMarker = marker;
            refreshPreview();
        };
        refreshPreview();
    }

    /* ── Campus maps (Leaflet + OpenStreetMap, loaded only when needed) ── */
    const mapNodes = document.querySelectorAll('[data-map], [data-map-picker], [data-map-multi]');
    if (mapNodes.length) {
        const LEAFLET_CSS = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        const LEAFLET_JS = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';

        const loadCss = (href) => new Promise((resolve, reject) => {
            if (document.querySelector(`link[href="${href}"]`)) {
                resolve();
                return;
            }
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            link.onload = () => resolve();
            link.onerror = () => reject(new Error('Failed to load Leaflet CSS'));
            document.head.appendChild(link);
        });

        const loadScript = (src) => new Promise((resolve, reject) => {
            if (window.L) {
                resolve(window.L);
                return;
            }
            const existing = document.querySelector(`script[src="${src}"]`);
            if (existing) {
                existing.addEventListener('load', () => resolve(window.L));
                existing.addEventListener('error', () => reject(new Error('Failed to load Leaflet JS')));
                return;
            }
            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = () => resolve(window.L);
            script.onerror = () => reject(new Error('Failed to load Leaflet JS'));
            document.head.appendChild(script);
        });

        const fixDefaultIcons = (L) => {
            if (L.Icon?.Default?.prototype?._getIconUrl) {
                delete L.Icon.Default.prototype._getIconUrl;
            }
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            });
        };

        const addTiles = (L, map) => {
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);
        };

        const parseNum = (value, fallback) => {
            const n = Number(value);
            return Number.isFinite(n) ? n : fallback;
        };

        loadCss(LEAFLET_CSS)
            .then(() => loadScript(LEAFLET_JS))
            .then((L) => {
                if (!L) throw new Error('Leaflet unavailable');
                fixDefaultIcons(L);

                document.querySelectorAll('[data-map]').forEach((el) => {
                    const lat = parseNum(el.dataset.lat, 22.8997);
                    const lng = parseNum(el.dataset.lng, 89.5026);
                    const zoom = parseNum(el.dataset.zoom, 17);
                    const label = el.dataset.label || 'Location';
                    const map = L.map(el, { scrollWheelZoom: false }).setView([lat, lng], zoom);
                    addTiles(L, map);
                    const marker = L.marker([lat, lng]).addTo(map).bindPopup(label);
                    const previewRoot = el.closest('[data-location-preview]');
                    if (previewRoot && typeof previewRoot._finditBindMap === 'function') {
                        previewRoot._finditBindMap(map, marker);
                    }
                    requestAnimationFrame(() => map.invalidateSize());
                });

                document.querySelectorAll('[data-map-picker]').forEach((el) => {
                    const latInput = document.querySelector(el.dataset.latInput || '#latitude');
                    const lngInput = document.querySelector(el.dataset.lngInput || '#longitude');
                    const hasPreset = Boolean(latInput?.value) && Boolean(lngInput?.value);
                    let lat = parseNum(latInput?.value || el.dataset.lat, 22.8997);
                    let lng = parseNum(lngInput?.value || el.dataset.lng, 89.5026);
                    const zoom = parseNum(el.dataset.zoom, 16);
                    const map = L.map(el, { scrollWheelZoom: false }).setView([lat, lng], zoom);
                    addTiles(L, map);
                    const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

                    const syncInputs = (nextLat, nextLng) => {
                        if (latInput) latInput.value = nextLat.toFixed(7);
                        if (lngInput) lngInput.value = nextLng.toFixed(7);
                    };

                    // Only prefill inputs when values already exist — leave empty for auto-geocode.
                    if (hasPreset) {
                        syncInputs(lat, lng);
                    }

                    map.on('click', (e) => {
                        marker.setLatLng(e.latlng);
                        syncInputs(e.latlng.lat, e.latlng.lng);
                    });

                    marker.on('dragend', () => {
                        const pos = marker.getLatLng();
                        syncInputs(pos.lat, pos.lng);
                    });

                    const onInputChange = () => {
                        if (!latInput?.value || !lngInput?.value) return;
                        const nextLat = parseNum(latInput.value, lat);
                        const nextLng = parseNum(lngInput.value, lng);
                        marker.setLatLng([nextLat, nextLng]);
                        map.setView([nextLat, nextLng], map.getZoom());
                    };
                    latInput?.addEventListener('change', onInputChange);
                    lngInput?.addEventListener('change', onInputChange);

                    requestAnimationFrame(() => map.invalidateSize());
                });

                document.querySelectorAll('[data-map-multi]').forEach((el) => {
                    let points = [];
                    try {
                        points = JSON.parse(el.dataset.points || '[]');
                    } catch {
                        points = [];
                    }
                    if (!Array.isArray(points) || !points.length) {
                        el.innerHTML = '<p class="meta" style="padding:1rem;">No map pins yet.</p>';
                        return;
                    }

                    const map = L.map(el, { scrollWheelZoom: false });
                    addTiles(L, map);
                    const bounds = [];
                    points.forEach((point) => {
                        const lat = parseNum(point.lat, null);
                        const lng = parseNum(point.lng, null);
                        if (lat === null || lng === null) return;
                        L.marker([lat, lng]).addTo(map).bindPopup(point.label || 'Location');
                        bounds.push([lat, lng]);
                    });
                    if (bounds.length === 1) {
                        map.setView(bounds[0], parseNum(el.dataset.zoom, 16));
                    } else if (bounds.length > 1) {
                        map.fitBounds(bounds, { padding: [28, 28] });
                    }
                    requestAnimationFrame(() => map.invalidateSize());
                });
            })
            .catch(() => {
                mapNodes.forEach((el) => {
                    el.innerHTML = '<p class="meta" style="padding:1rem;">Map could not load. Check your internet connection.</p>';
                });
            });
    }
});
