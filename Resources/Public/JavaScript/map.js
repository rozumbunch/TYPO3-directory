(function () {
    'use strict';

    const KIND_LABELS = {
        organisations: 'organisations',
        locations: 'locations',
        contacts: 'contacts',
        persons: 'persons'
    };

    const init = function () {
        document.querySelectorAll('[data-directory-map]').forEach(setupRoot);
    };

    const setupRoot = function (root) {
        const configNode = root.querySelector('.directory-map__config');
        if (!(configNode instanceof HTMLElement)) {
            return;
        }

        let config;
        try {
            config = JSON.parse(configNode.textContent || '{}');
        } catch (error) {
            return;
        }

        if (!Array.isArray(config.markers) || config.markers.length === 0) {
            return;
        }

        const loadMap = function () {
            if (root.dataset.directoryMapReady === '1') {
                return;
            }
            root.dataset.directoryMapReady = '1';
            const consent = root.querySelector('[data-directory-map-consent]');
            const canvas = root.querySelector('[data-directory-map-canvas]');
            if (consent instanceof HTMLElement) {
                consent.hidden = true;
            }
            if (canvas instanceof HTMLElement) {
                canvas.classList.remove('directory-map__canvas--pending');
            }
            createMap(canvas, config);
        };

        if (root.getAttribute('data-directory-map') === 'consent') {
            const button = root.querySelector('[data-directory-map-load]');
            if (button instanceof HTMLElement) {
                button.addEventListener('click', loadMap);
            }
            return;
        }

        loadMap();
    };

    const createMap = function (canvas, config) {
        if (!(canvas instanceof HTMLElement)) {
            return;
        }
        if (typeof window.L === 'undefined') {
            window.addEventListener('load', function () {
                createMap(canvas, config);
            }, { once: true });
            return;
        }

        const map = window.L.map(canvas, {
            scrollWheelZoom: false
        });
        window.L.tileLayer(config.tileUrl, {
            attribution: config.tileAttribution,
            maxZoom: 19
        }).addTo(map);

        const bounds = [];
        config.markers.forEach(function (marker) {
            const latLng = [marker.lat, marker.lng];
            bounds.push(latLng);
            window.L.marker(latLng, {
                icon: window.L.divIcon({
                    className: 'directory-map-marker directory-map-marker--' + sanitizeKind(marker.kind),
                    iconSize: [28, 36],
                    iconAnchor: [14, 34],
                    popupAnchor: [0, -28],
                    html: '<span class="directory-map-marker__pin"></span>'
                })
            }).bindPopup(popupHtml(marker, config.labels || {}), {
                maxWidth: 320
            }).addTo(map);
        });

        if (bounds.length === 1) {
            map.setView(bounds[0], 14);
        } else if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [28, 28], maxZoom: 14 });
        } else {
            map.setView([51.1657, 10.4515], 6);
        }

        window.setTimeout(function () {
            map.invalidateSize();
        }, 50);
    };

    const popupHtml = function (marker, labels) {
        return (marker.items || []).map(function (item) {
            const facts = [];
            if (item.phone) {
                facts.push(link(item.phone, item.phoneLabel || item.phone));
            }
            if (item.email) {
                facts.push(link(item.email, item.emailLabel || item.email));
            }
            if (item.website) {
                facts.push(link(item.website, item.websiteLabel || item.website, true));
            }
            if (item.url) {
                facts.push(link(item.url, labels.details || 'Details'));
            }
            facts.push(link(
                'https://www.openstreetmap.org/directions?from=&to=' + encodeURIComponent(marker.lat + ',' + marker.lng),
                labels.route || 'Route',
                true
            ));

            return ''
                + '<article class="directory-map-card">'
                + '<p class="directory-map-card__kind">' + escapeHtml(kindLabel(item.kind, labels)) + '</p>'
                + '<h3 class="directory-map-card__title">' + escapeHtml(item.title || '') + '</h3>'
                + optionalBlock('directory-map-card__subtitle', item.subtitle)
                + optionalBlock('directory-map-card__related', item.related)
                + optionalBlock('directory-map-card__address', item.address)
                + optionalBlock('directory-map-card__summary', item.summary)
                + '<ul class="directory-map-card__facts"><li>' + facts.join('</li><li>') + '</li></ul>'
                + '</article>';
        }).join('');
    };

    const kindLabel = function (kind, labels) {
        if (labels['kind.' + kind]) {
            return labels['kind.' + kind];
        }
        return KIND_LABELS[kind] || kind;
    };

    const optionalBlock = function (className, value) {
        if (!value) {
            return '';
        }
        return '<p class="' + className + '">' + escapeHtml(value) + '</p>';
    };

    const link = function (href, label, external) {
        const rel = external ? ' rel="noopener noreferrer"' : '';
        return '<a href="' + escapeHtml(href) + '"' + rel + '>' + escapeHtml(label) + '</a>';
    };

    const sanitizeKind = function (kind) {
        return Object.prototype.hasOwnProperty.call(KIND_LABELS, kind) ? kind : 'locations';
    };

    const escapeHtml = function (value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
