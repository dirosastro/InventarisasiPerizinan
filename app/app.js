// ─── BASE MAPS ──────────────────────────────────────────────
const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
    maxZoom: 20,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    attribution: '&copy; Google Maps'
});

const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    maxZoom: 20,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    attribution: '&copy; Google Maps'
});

const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
    maxZoom: 20,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    attribution: '&copy; Google Maps'
});

const googleTerrain = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
    maxZoom: 20,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    attribution: '&copy; Google Maps'
});

const baseMaps = {
    "streets": googleStreets,
    "satellite": googleSatellite,
    "hybrid": googleHybrid,
    "terrain": googleTerrain
};

// Initialize map with Satellite as default
const map = L.map('map', {
    center: [-8.5833, 116.3333],
    zoom: 10,
    layers: [googleSatellite], // Default layer changed to Satellite
    zoomControl: false
});

// Function to switch map type
window.switchMapType = function(type) {
    if (!baseMaps[type]) return;
    
    // Remove all base layers
    Object.values(baseMaps).forEach(layer => map.removeLayer(layer));
    
    // Add requested layer
    map.addLayer(baseMaps[type]);
    
    // Update UI active state (logic in app.js or html)
    const btns = document.querySelectorAll('.map-type-btn');
    btns.forEach(btn => {
        if (btn.dataset.type === type) {
            btn.classList.add('border-accent', 'scale-110');
            btn.classList.remove('border-white/20');
        } else {
            btn.classList.remove('border-accent', 'scale-110');
            btn.classList.add('border-white/20');
        }
    });
};

// ─── UTILITIES & CONSTANTS ──────────────────────────────────
// Custom Zoom Control
L.control.zoom({
    position: 'topright'
}).addTo(map);

// Feature Groups for different layers
const layers = {
    ruas_jalan: L.featureGroup().addTo(map),
    izin: L.featureGroup().addTo(map),
    rumija: L.featureGroup().addTo(map),
    rumaja: L.featureGroup() // Not added by default
};

// Map state
let geojsonData = null;
let nationalRoadsData = null;
let allPerizinanData = null;

// UI Elements
const detailPanel = document.getElementById('detail-panel');
const closePanelBtn = document.getElementById('close-panel');
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

// Helper to format currency
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

// Helper to get status color
const getStatusColor = (status) => {
    switch (status) {
        case 'aktif': return '#48BB78'; // green
        case 'hampir_habis': return '#ECC94B'; // yellow
        case 'kadaluarsa': return '#F56565'; // red
        default: return '#A0AEC0'; // gray
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'aktif': return 'Aktif';
        case 'hampir_habis': return 'Hampir Habis';
        case 'kadaluarsa': return 'Kadaluarsa';
        default: return 'Tidak Diketahui';
    }
};

// Helper to get PPK color dynamically
const getPpkColor = (ppk) => {
    if (!ppk) return '#3B82F6';

    // Predefined colors for common PPKs
    const colors = {
        'PPK 1.1': '#3B82F6', // Blue
        'PPK 1.2': '#EF4444', // Red
        'PPK 1.3': '#10B981', // Emerald
        'PPK 1.4': '#F59E0B', // Amber
        'PPK 2.1': '#8B5CF6', // Purple
        'PPK 2.2': '#EC4899', // Pink
        'PPK 2.3': '#F97316', // Orange
        'PPK 2.4': '#14B8A6'  // Teal
    };

    if (colors[ppk]) return colors[ppk];

    // Fallback: Generate a random hex color based on the PPK string
    let hash = 0;
    for (let i = 0; i < ppk.length; i++) {
        hash = ppk.charCodeAt(i) + ((hash << 5) - hash);
    }
    const c = (hash & 0x00FFFFFF).toString(16).toUpperCase();
    return '#' + "000000".substring(0, 6 - c.length) + c;
};

// Helper to get icon by Jenis Izin
const getIconByJenisIzin = (jenis, label = '') => {
    const j = jenis ? jenis.toLowerCase() : '';
    const l = label ? label.toLowerCase() : '';

    if (j.includes('listrik')) return 'ph-lightning';
    if (j.includes('air') || j.includes('pipa')) return 'ph-drop';
    if (j.includes('optik') || j.includes('telekomunikasi') || j.includes('wifi')) return 'ph-wifi-high';
    if (j.includes('reklame') || j.includes('iklan')) return 'ph-signpost';
    if (j.includes('angkutan') || j.includes('truck')) return 'ph-truck';
    if (j.includes('akses jalan')) return 'ph-car';
    
    // Fallback based on broader categories
    if (j.includes('utilitas')) return 'ph-tools';
    
    return 'ph-map-pin';
};

// Style functions for GeoJSON
const styleRuasJalan = (feature) => {
    return {
        color: getPpkColor(feature.properties.PPK),
        weight: 4,
        opacity: 0.8
    };
};

const styleRumija = (feature) => {
    const ppkColor = getPpkColor(feature.properties.PPK);
    return {
        color: ppkColor,
        fillColor: ppkColor,
        fillOpacity: 0.2,
        weight: 2,
        dashArray: '5, 5'
    };
};

const pointToLayerIzin = (feature, latlng) => {
    const status = feature.properties.status;
    const color = getStatusColor(status);

    // Jika ini adalah penanda batas (awal/akhir)
    if (feature.properties.is_boundary) {
        const isAwal = feature.properties.label === 'Titik Awal';
        const markerColor = isAwal ? '#48BB78' : '#F56565'; // Green for Start, Red for End
        const boundaryIcon = L.divIcon({
            className: 'boundary-icon',
            html: `<div style="background-color: ${markerColor}; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; border: 2px solid white; box-shadow: 0 1px 4px rgba(0,0,0,0.4);">
                       <i class="ph-fill ${isAwal ? 'ph-play' : 'ph-stop'} text-[10px]"></i>
                   </div>`,
            iconSize: [22, 22],
            iconAnchor: [11, 11],
            popupAnchor: [0, -11]
        });
        return L.marker(latlng, { icon: boundaryIcon })
            .bindTooltip(feature.properties.label, {
                direction: 'top',
                offset: [0, -10],
                className: 'custom-tooltip'
            })
            .bindPopup(`
                <div class="p-2">
                    <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">Koordinat ${feature.properties.label}</div>
                    <div class="bg-gray-50 p-2 rounded-lg border border-gray-100 font-mono text-[11px] text-primary">
                        ${latlng.lat.toFixed(7)}, ${latlng.lng.toFixed(7)}
                    </div>
                    <div class="mt-2 text-[9px] text-gray-400 italic">*Koordinat diambil dari data spasial perizinan</div>
                </div>
            `, { className: 'boundary-popup' });
    }

    const iconClass = feature.properties.icon || getIconByJenisIzin(feature.properties.jenis_izin, feature.properties.pemohon);

    const customIcon = L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background-color: ${color}; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                   <i class="ph-fill ${iconClass} text-lg"></i>
               </div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    });

    return L.marker(latlng, { icon: customIcon });
};

// Helper to parse coordinates from string like "-8.123, 116.456"
const parseCoordinates = (str) => {
    if (!str) return null;
    // Bersihkan karakter non-numerik kecuali titik, minus, koma, dan spasi
    const cleanStr = str.replace(/[^\d\.\-\, ]/g, ' ');
    const parts = cleanStr.split(/[\s,]+/).filter(p => p.length > 0);

    if (parts.length >= 2) {
        let val1 = parseFloat(parts[0]);
        let val2 = parseFloat(parts[1]);

        if (!isNaN(val1) && !isNaN(val2)) {
            let lat, lng;
            // Deteksi mana yang Lintang (Lat: ~-8) dan mana yang Bujur (Lng: ~116) untuk area NTB
            if (Math.abs(val1) < 15 && Math.abs(val2) > 100) {
                lat = val1; lng = val2;
            } else if (Math.abs(val2) < 15 && Math.abs(val1) > 100) {
                lat = val2; lng = val1;
            } else {
                // Fallback: pertama lat, kedua lng
                lat = val1; lng = val2;
            }
            return [lat, lng];
        }
    }
    return null;
};

// Function to fetch and process Perizinan
async function fetchPerizinan() {
    try {
        const response = await fetch('http://localhost:8000/api/perizinan');
        const result = await response.json();
        if (!result.success) return;
        allPerizinanData = result.data;
        renderPerizinanOnMap();
        updateSummaryStats();
    } catch (error) {
        console.error('Error loading API Perizinan:', error);
    }
}



function renderPerizinanOnMap() {
    if (!allPerizinanData || !nationalRoadsData) return;

    let allFeatures = [];
    allPerizinanData.forEach(item => {
        // 1. Process GeoJSON if exists
        if (item.geojson) {
            try {
                const parsedGeo = typeof item.geojson === 'string' ? JSON.parse(item.geojson) : item.geojson;
                const features = parsedGeo.features || [parsedGeo];
                features.forEach(feat => {
                    const roadName = item.lokasi && item.lokasi.length > 0 ? item.lokasi[0].nama_ruas_jalan : null;
                    const roadInfo = roadName ? nationalRoadsData.features.find(f => 
                        String(f.properties['Nama Ruas'] || f.properties.LINK_NAME).trim().toUpperCase() === roadName.trim().toUpperCase()
                    ) : null;

                    feat.properties = {
                        id: item.id,
                        type: 'izin',
                        no_izin: item.nomor_izin,
                        pemohon: item.pemohon,
                        raw_jenis_izin: item.jenis_izin,
                        jenis_izin: (item.sub_jenis && item.sub_jenis !== '-') ? item.sub_jenis : item.jenis_izin,
                        satker_id: item.satker_id,
                        ruas_jalan: item.lokasi ? item.lokasi.map(l => l.nama_ruas_jalan).join(', ') : '-',
                        status: item.status,
                        icon: feat.properties.icon || item.icon,
                        masa_berlaku_awal: item.tanggal_terbit,
                        masa_berlaku_akhir: item.tanggal_akhir,
                        pnbp: item.pnbp || 0,
                        ppk: roadInfo ? roadInfo.properties.PPK : '-',
                        panjang_ruas: roadInfo ? turf.length(roadInfo, { units: 'kilometers' }).toFixed(2) : 0,
                        panjang_dimanfaatkan: turf.length(feat, { units: 'kilometers' }).toFixed(2),
                        sta_awal: item.lokasi && item.lokasi.length > 0 ? item.lokasi[0].sta_awal : '-',
                        sta_akhir: item.lokasi && item.lokasi.length > 0 ? item.lokasi[0].sta_akhir : '-'
                    };
                    allFeatures.push(feat);

                    // Add boundary markers if LineString
                    if (feat.geometry.type === 'LineString') {
                        const coords = feat.geometry.coordinates;
                        allFeatures.push({
                            type: 'Feature',
                            geometry: { type: 'Point', coordinates: coords[0] },
                            properties: { ...feat.properties, is_boundary: true, label: 'Titik Awal' }
                        });
                        allFeatures.push({
                            type: 'Feature',
                            geometry: { type: 'Point', coordinates: coords[coords.length - 1] },
                            properties: { ...feat.properties, is_boundary: true, label: 'Titik Akhir' }
                        });
                    }
                });
            } catch (e) {
                console.error("Invalid GeoJSON for permit ID", item.id, e);
            }
        }

        // 2. Process locations with coordinates (Snap to Road)
        if (item.lokasi && item.lokasi.length > 0) {
            item.lokasi.forEach(loc => {
                const coordsAwal = parseCoordinates(loc.sta_awal);
                const coordsAkhir = parseCoordinates(loc.sta_akhir);

                if (coordsAwal && coordsAkhir) {
                    const searchName = String(loc.nama_ruas_jalan).trim().toUpperCase();
                    const startPt = turf.point([coordsAwal[1], coordsAwal[0]]);
                    const endPt = turf.point([coordsAkhir[1], coordsAkhir[0]]);

                    // Temukan semua segmen jalan dengan nama yang sama
                    const roadSegments = nationalRoadsData.features.filter(f =>
                        String(f.properties['Nama Ruas'] || f.properties.LINK_NAME).trim().toUpperCase() === searchName
                    );

                    let roadFeature = null;
                    if (roadSegments.length > 0) {
                        // Cari segmen yang paling dekat dengan titik awal
                        let minDistance = Infinity;
                        roadSegments.forEach(seg => {
                            try {
                                const dist = turf.pointToLineDistance(startPt, seg);
                                if (dist < minDistance) {
                                    minDistance = dist;
                                    roadFeature = seg;
                                }
                            } catch (e) { }
                        });
                    }

                    // Fallback: Jika nama jalan tidak cocok, cari jalan terdekat APAPUN dalam radius 1km
                    if (!roadFeature) {
                        let minFallbackDist = Infinity;
                        nationalRoadsData.features.forEach(f => {
                            try {
                                const dist = turf.pointToLineDistance(startPt, f);
                                if (dist < minFallbackDist && dist < 1.0) { // max 1km
                                    minFallbackDist = dist;
                                    roadFeature = f;
                                }
                            } catch (e) { }
                        });
                    }

                    const commonProps = {
                        id: item.id,
                        type: 'izin',
                        no_izin: item.nomor_izin,
                        pemohon: item.pemohon,
                        raw_jenis_izin: item.jenis_izin,
                        jenis_izin: (item.sub_jenis && item.sub_jenis !== '-') ? item.sub_jenis : item.jenis_izin,
                        satker_id: item.satker_id,
                        ruas_jalan: loc.nama_ruas_jalan,
                        status: item.status,
                        icon: item.icon,
                        masa_berlaku_awal: item.tanggal_terbit,
                        masa_berlaku_akhir: item.tanggal_akhir,
                        pnbp: item.pnbp || 0,
                        ppk: roadFeature ? roadFeature.properties.PPK : '-',
                        panjang_ruas: roadFeature ? turf.length(roadFeature, { units: 'kilometers' }).toFixed(2) : 0,
                        panjang_dimanfaatkan: 0, // Updated below
                        sta_awal: loc.sta_awal || '-',
                        sta_akhir: loc.sta_akhir || '-'
                    };

                    if (roadFeature) {
                        try {
                            // Untuk lineSlice yang akurat pada fragmented roads, kita gabungkan koordinat jika memungkinkan
                            // Namun sebagai solusi paling stabil: gunakan segmen terdekat dan pastikan snapping sempurna.
                            let targetLine = roadFeature;
                            if (roadFeature.geometry.type === 'MultiLineString') {
                                const allCoords = roadFeature.geometry.coordinates.flat(1);
                                targetLine = turf.lineString(allCoords);
                            }

                            // Snapping yang lebih agresif
                            const snappedStart = turf.nearestPointOnLine(targetLine, startPt);
                            const snappedEnd = turf.nearestPointOnLine(targetLine, endPt);

                            const sliced = turf.lineSlice(snappedStart, snappedEnd, targetLine);

                            if (sliced && sliced.geometry && sliced.geometry.coordinates.length > 1) {
                                // TAMPILKAN SEBAGAI GARIS TEBAL (Utilitas)
                                const utilLen = turf.length(sliced, { units: 'kilometers' }).toFixed(2);
                                allFeatures.push({
                                    type: 'Feature',
                                    geometry: sliced.geometry,
                                    properties: { ...commonProps, is_utility_line: true, panjang_dimanfaatkan: utilLen }
                                });

                                // TAMPILKAN JUGA SEBAGAI AREA (Rumija) agar terlihat volumenya
                                const permitArea = turf.buffer(sliced, 12, { units: 'meters' });
                                allFeatures.push({
                                    type: 'Feature',
                                    geometry: permitArea.geometry,
                                    properties: { ...commonProps, panjang_dimanfaatkan: utilLen }
                                });
                            } else {
                                throw new Error("Sliced geometry invalid");
                            }
                        } catch (err) {
                            console.warn("Snap-to-road failing, using straight line fallback:", err);
                            const straightLine = turf.lineString([[coordsAwal[1], coordsAwal[0]], [coordsAkhir[1], coordsAkhir[0]]]);
                            const fallbackLen = turf.length(straightLine, { units: 'kilometers' }).toFixed(2);
                            allFeatures.push({
                                type: 'Feature',
                                geometry: straightLine.geometry,
                                properties: { ...commonProps, is_utility_line: true, panjang_dimanfaatkan: fallbackLen }
                            });
                        }
                    } else {
                        // Fallback ke garis lurus jika ruas tidak ditemukan
                        allFeatures.push({
                            type: 'Feature',
                            geometry: {
                                type: 'LineString',
                                coordinates: [[coordsAwal[1], coordsAwal[0]], [coordsAkhir[1], coordsAkhir[0]]]
                            },
                            properties: commonProps
                        });
                    }

                    // 2. Tambahkan Penanda Awal & Akhir
                    allFeatures.push({
                        type: 'Feature',
                        geometry: { type: 'Point', coordinates: [coordsAwal[1], coordsAwal[0]] },
                        properties: { ...commonProps, is_boundary: true, label: 'Titik Awal' }
                    });
                    allFeatures.push({
                        type: 'Feature',
                        geometry: { type: 'Point', coordinates: [coordsAkhir[1], coordsAkhir[0]] },
                        properties: { ...commonProps, is_boundary: true, label: 'Titik Akhir' }
                    });
                } else if (coordsAwal || coordsAkhir) {
                    const coords = coordsAwal || coordsAkhir;
                    allFeatures.push({
                        type: 'Feature',
                        geometry: { type: 'Point', coordinates: [coords[1], coords[0]] },
                        properties: {
                            id: item.id,
                            type: 'izin',
                            no_izin: item.nomor_izin,
                            pemohon: item.pemohon,
                            raw_jenis_izin: item.jenis_izin,
                            jenis_izin: (item.sub_jenis && item.sub_jenis !== '-') ? item.sub_jenis : item.jenis_izin,
                            satker_id: item.satker_id,
                            ruas_jalan: loc.nama_ruas_jalan,
                            status: item.status,
                            icon: item.icon,
                            masa_berlaku_awal: item.tanggal_terbit,
                            masa_berlaku_akhir: item.tanggal_akhir,
                            pnbp: item.pnbp || 0
                        }
                    });
                }
            });
        }
    });

    geojsonData = {
        type: "FeatureCollection",
        features: allFeatures
    };

    // Clear previous layers before rendering
    layers.izin.clearLayers();
    loadDataToMap();
}

fetchPerizinan();

// Define EPSG:32750 (UTM Zone 50S)
proj4.defs("EPSG:32750", "+proj=utm +zone=50 +south +datum=WGS84 +units=m +no_defs");

// Fetch Peta Jalan Nasional
fetch('../api/Peta%20Jalan%20Nasional.geojson')
    .then(response => response.json())
    .then(data => {
        // Project coordinates from EPSG:32750 to EPSG:4326
        const projectedFeatures = data.features.map(feature => {
            const newFeature = JSON.parse(JSON.stringify(feature));

            const projectCoords = (coords) => {
                if (typeof coords[0] === 'number') {
                    const pt = proj4("EPSG:32750", "EPSG:4326", [coords[0], coords[1]]);
                    return [pt[0], pt[1]];
                } else {
                    return coords.map(projectCoords);
                }
            };

            if (newFeature.geometry && newFeature.geometry.coordinates) {
                newFeature.geometry.coordinates = projectCoords(newFeature.geometry.coordinates);
            }
            return newFeature;
        });

        const projectedGeoJSON = {
            type: "FeatureCollection",
            features: projectedFeatures
        };

        nationalRoadsData = projectedGeoJSON;
        renderPerizinanOnMap();

        // Calculate Total Length
        try {
            const totalLengthKm = turf.length(projectedGeoJSON, { units: 'kilometers' });
            const formattedLength = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(totalLengthKm);
            const summaryEl = document.getElementById('summary-panjang-jalan');
            if (summaryEl) {
                summaryEl.innerText = formattedLength + ' Km';
            }
        } catch (e) {
            console.error("Error calculating total length:", e);
        }

        // Populate Search Autocomplete
        populateAutocomplete();

        // 1. Draw Garis (Lines) for Ruas Jalan
        L.geoJSON(projectedGeoJSON, {
            style: styleRuasJalan,
            onEachFeature: function (feature, layer) {
                const namaRuas = feature.properties['Nama Ruas'] || feature.properties.LINK_NAME;
                if (namaRuas) {
                    const length = turf.length(feature, { units: 'kilometers' }).toFixed(2);
                    const ppk = feature.properties.PPK || '-';
                    layer.bindPopup(`
                        <div class="p-1">
                            <b class="text-primary block mb-1">Ruas Jalan</b>
                            <div class="text-sm font-bold mb-2">${namaRuas}</div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-gray-500 block">Panjang</span>
                                    <span class="font-semibold">${length} Km</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Pemilik ruas</span>
                                    <span class="font-semibold">${ppk}</span>
                                </div>
                            </div>
                        </div>
                    `);
                }
                layers.ruas_jalan.addLayer(layer);
            }
        });

        // 2. Generate Poligon (Buffer) for RUMIJA using Turf.js
        try {
            // Buffer roads by 15 meters to create polygons
            const bufferedPolygons = turf.buffer(projectedGeoJSON, 15, { units: 'meters' });

            L.geoJSON(bufferedPolygons, {
                style: styleRumija,
                onEachFeature: function (feature, layer) {
                    const namaRuas = feature.properties['Nama Ruas'] || feature.properties.LINK_NAME;
                    if (namaRuas) {
                        const length = turf.length(feature, { units: 'kilometers' }).toFixed(2);
                        const ppk = feature.properties.PPK || '-';
                        layer.bindPopup(`
                            <div class="p-1">
                                <b class="text-indigo-600 block mb-1">Area RUMIJA</b>
                                <div class="text-sm font-bold mb-2">${namaRuas}</div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="text-gray-500 block">Panjang Ruas</span>
                                        <span class="font-semibold">${length} Km</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block">Pemilik ruas</span>
                                        <span class="font-semibold">${ppk}</span>
                                    </div>
                                </div>
                            </div>
                        `);
                    }
                    layers.rumija.addLayer(layer);
                }
            });
        } catch (e) {
            console.error("Error generating polygons with Turf:", e);
        }
    })
    .catch(error => console.error('Error loading Peta Jalan Nasional:', error));

function loadDataToMap(data = geojsonData) {
    L.geoJSON(data, {
        pointToLayer: function (feature, latlng) {
            if (feature.properties.type === 'izin') {
                const marker = pointToLayerIzin(feature, latlng);
                
                // Only bind general popup if NOT a boundary marker (Start/End)
                if (!feature.properties.is_boundary) {
                    marker.bindPopup(createPopupContent(feature.properties, latlng), {
                        className: 'custom-popup'
                    });
                }

                // Click event for marker to open detail panel
                marker.on('click', () => {
                    openDetailPanel(feature.properties);
                });

                return marker;
            }
        },
        style: function (feature) {
            if (feature.properties.type === 'rumija') {
                return styleRumija(feature);
            }
            if (feature.properties.type === 'izin') {
                const isLine = feature.geometry.type.includes('LineString');
                return {
                    color: '#00BFFF', // Biru Muda Cerah (DeepSkyBlue)
                    weight: isLine ? 3 : 2,
                    opacity: 0.9,
                    lineJoin: 'round',
                    lineCap: 'round'
                };
            }
        },
        onEachFeature: function (feature, layer) {
            // Add to appropriate feature group
            if (feature.properties.type === 'izin') {
                layers.izin.addLayer(layer);

                // Pastikan layer izin tampil di depan ruas jalan
                if (typeof layer.bringToFront === 'function') {
                    layer.bringToFront();
                }

                // Jika geometri bukan Point, kita tambahkan marker di tengah bounds
                if (feature.geometry && feature.geometry.type !== 'Point') {
                    // Pastikan layer memiliki getBounds()
                    if (typeof layer.getBounds === 'function') {
                        const center = layer.getBounds().getCenter();
                        const marker = pointToLayerIzin(feature, center);
                        marker.bindPopup(createPopupContent(feature.properties, center), {
                            className: 'custom-popup'
                        });
                        marker.on('click', () => {
                            openDetailPanel(feature.properties);
                        });
                        layers.izin.addLayer(marker);

                        // Styling sudah dihandle di fungsi style:
                        // Namun kita tambahkan fillOpacity untuk poligon jika ada
                        if (feature.geometry.type.includes('Polygon')) {
                            layer.setStyle({ fillOpacity: 0.4 });
                        }

                        layer.bindPopup(createPopupContent(feature.properties, center), {
                            className: 'custom-popup'
                        });
                        layer.on('click', () => {
                            openDetailPanel(feature.properties);
                        });
                    }
                }
            } else if (feature.properties.type === 'rumija') {
                layers.rumija.addLayer(layer);
            }
        }
    });
}

// Create Popup Content
function createPopupContent(props, latlng) {
    const svUrl = latlng ? `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${latlng.lat},${latlng.lng}` : '#';
    
    return `
        <div class="title">${props.pemohon}</div>
        <div class="subtitle">${props.jenis_izin}</div>
        
        <div class="info-row">
            <span class="info-label">Ruas:</span>
            <span class="info-value">${props.ruas_jalan}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Pemilik ruas:</span>
            <span class="info-value">${props.ppk || '-'}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Panjang Ruas:</span>
            <span class="info-value">${props.panjang_ruas ? props.panjang_ruas + ' Km' : '-'}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Panjang Dimanfaatkan:</span>
            <span class="info-value">${props.panjang_dimanfaatkan ? props.panjang_dimanfaatkan + ' Km' : '-'}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Masa Berlaku:</span>
            <span class="info-value">${props.masa_berlaku_akhir}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value" style="color: ${getStatusColor(props.status)}">${getStatusLabel(props.status)}</span>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 flex gap-2">
            <button class="bg-primary text-white text-[10px] font-bold py-1.5 px-3 rounded flex items-center gap-1.5 hover:bg-primary/90 transition-colors" onclick="openDetailPanelById(${props.id})">
                <i class="ph-fill ph-info"></i> DETAIL
            </button>
            <button class="bg-amber-500 text-white text-[10px] font-bold py-1.5 px-3 rounded flex items-center gap-1.5 hover:bg-amber-600 transition-colors" onclick="openStreetView(${latlng.lat}, ${latlng.lng})">
                <i class="ph-fill ph-person-simple-walk"></i> STREET VIEW
            </button>
        </div>
    `;
}

// Global function to open panel from popup button
window.openDetailPanelById = function (id) {
    if (!geojsonData) return;
    const feature = geojsonData.features.find(f => f.properties.id === id);
    if (feature) {
        openDetailPanel(feature.properties);
    }
};

// Open Detail Panel and populate data
function openDetailPanel(props) {
    if (props.type !== 'izin') return;

    // Header
    const statusEl = document.getElementById('detail-status');
    statusEl.textContent = getStatusLabel(props.status);
    statusEl.style.backgroundColor = getStatusColor(props.status) + '20'; // 20% opacity
    statusEl.style.color = getStatusColor(props.status);

    document.getElementById('detail-title').textContent = props.jenis_izin;
    document.getElementById('detail-subtitle').textContent = props.pemohon;

    // Tab Umum
    document.getElementById('info-no-izin').textContent = props.no_izin;
    document.getElementById('info-pemohon').textContent = props.pemohon;
    document.getElementById('info-jenis').textContent = props.jenis_izin;
    document.getElementById('info-masa-awal').textContent = props.masa_berlaku_awal;
    document.getElementById('info-masa-akhir').textContent = props.masa_berlaku_akhir;
    document.getElementById('info-pnbp').textContent = formatRupiah(props.pnbp);

    // Tab Teknis
    document.getElementById('teknis-ruas').textContent = props.ruas_jalan;
    document.getElementById('teknis-sta-awal').textContent = props.sta_awal || '-';
    document.getElementById('teknis-sta-akhir').textContent = props.sta_akhir || '-';
    document.getElementById('teknis-panjang').textContent = props.panjang_dimanfaatkan ? props.panjang_dimanfaatkan + ' Km' : '-';

    // Show Panel
    detailPanel.classList.remove('translate-x-full');
}

// Close Detail Panel
closePanelBtn.addEventListener('click', () => {
    detailPanel.classList.add('translate-x-full');
});

// Tab Switching Logic
tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all tabs
        tabBtns.forEach(b => {
            b.classList.remove('text-primary', 'border-primary');
            b.classList.add('text-gray-500', 'border-transparent');
        });

        // Add active class to clicked tab
        btn.classList.remove('text-gray-500', 'border-transparent');
        btn.classList.add('text-primary', 'border-primary');

        // Hide all contents
        tabContents.forEach(c => c.classList.add('hidden'));

        // Show target content
        const targetId = btn.getAttribute('data-target');
        document.getElementById(targetId).classList.remove('hidden');
    });
});

// ─── FILTER LOGIC ───────────────────────────────────────────
const filterJenis = document.getElementById('filter-jenis');
const filterSatker = document.getElementById('filter-satker');
const filterStatusAktif = document.getElementById('filter-status-aktif');
const filterStatusWarning = document.getElementById('filter-status-warning');
const filterStatusExpired = document.getElementById('filter-status-expired');
const searchInput = document.getElementById('search-input');

function filterData() {
    if (!geojsonData) return;

    const jenisValue = filterJenis.value;
    const satkerValue = filterSatker.value;
    const isAktif = filterStatusAktif.checked;
    const isWarning = filterStatusWarning.checked;
    const isExpired = filterStatusExpired.checked;
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

    const filteredFeatures = geojsonData.features.filter(feat => {
        const props = feat.properties;
        
        // 1. Filter Jenis
        let matchJenis = true;
        if (jenisValue !== 'all') {
            if (jenisValue === 'dispensasi') {
                matchJenis = props.raw_jenis_izin === 'dispensasi';
            } else {
                matchJenis = props.jenis_izin === jenisValue;
            }
        }

        // 2. Filter Satker
        let matchSatker = true;
        if (satkerValue !== 'all') {
            matchSatker = String(props.satker_id) === String(satkerValue);
        }

        // 3. Filter Status
        let matchStatus = false;
        if (props.status === 'aktif' && isAktif) matchStatus = true;
        if (props.status === 'hampir_habis' && isWarning) matchStatus = true;
        if (props.status === 'kadaluarsa' && isExpired) matchStatus = true;
        
        // 4. Filter Search
        let matchSearch = true;
        if (searchTerm !== '') {
            const pemohon = (props.pemohon || '').toLowerCase();
            const noIzin = (props.no_izin || '').toLowerCase();
            const ruasJalan = (props.ruas_jalan || '').toLowerCase();
            matchSearch = pemohon.includes(searchTerm) || noIzin.includes(searchTerm) || ruasJalan.includes(searchTerm);
        }

        return matchJenis && matchSatker && matchStatus && matchSearch;
    });

    const filteredGeoJSON = {
        type: "FeatureCollection",
        features: filteredFeatures
    };

    // Re-render
    layers.izin.clearLayers();
    loadDataToMap(filteredGeoJSON);

    // Update Summary based on filtered raw data
    updateSummaryStats(filteredFeatures);
}

// Event Listeners for Filters
[filterJenis, filterSatker, filterStatusAktif, filterStatusWarning, filterStatusExpired].forEach(el => {
    if (el) el.addEventListener('change', filterData);
});

let allRuasNames = [];

if (searchInput) {
    const clearBtn = document.getElementById('clear-search');

    // Function to adjust input width based on content
    const adjustInputWidth = () => {
        const val = searchInput.value;
        if (!val) {
            searchInput.style.width = '280px';
            return;
        }

        // Create a temporary span to measure text width
        const tempSpan = document.createElement('span');
        tempSpan.style.visibility = 'hidden';
        tempSpan.style.position = 'absolute';
        tempSpan.style.whiteSpace = 'pre';
        tempSpan.style.font = window.getComputedStyle(searchInput).font;
        tempSpan.innerText = val;
        document.body.appendChild(tempSpan);
        
        const textWidth = tempSpan.getBoundingClientRect().width;
        document.body.removeChild(tempSpan);

        // pl-9 (36px) + pr-10 (40px) + extra buffer (20px) = ~96px
        const newWidth = Math.min(600, Math.max(280, textWidth + 100));
        searchInput.style.width = `${newWidth}px`;
    };

    searchInput.addEventListener('input', (e) => {
        const val = e.target.value;
        
        // Toggle Clear Button
        if (clearBtn) {
            if (val.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        // Adjust width
        adjustInputWidth();

        const datalist = document.getElementById('ruas-list');
        
        if (val.length >= 2) {
            if (datalist && datalist.children.length === 0) {
                allRuasNames.forEach(name => {
                    const option = document.createElement('option');
                    option.value = name;
                    datalist.appendChild(option);
                });
            }
        } else {
            if (datalist) datalist.innerHTML = '';
        }
        
        filterData();
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.style.width = '280px';
            clearBtn.classList.add('hidden');
            const datalist = document.getElementById('ruas-list');
            if (datalist) datalist.innerHTML = '';
            filterData();
        });
    }
}

function populateAutocomplete() {
    if (!nationalRoadsData) return;
    
    const ruasNames = new Set();
    nationalRoadsData.features.forEach(f => {
        const name = f.properties['Nama Ruas'] || f.properties.LINK_NAME;
        if (name) ruasNames.add(name);
    });

    allRuasNames = [...ruasNames].sort();
    // We don't populate the datalist immediately anymore
}

// Update updateSummaryStats to handle filtered features
function updateSummaryStats(filteredFeatures = null) {
    let dataToUse;
    
    if (filteredFeatures) {
        // Map features back to unique permit objects for stats
        const uniqueIds = [...new Set(filteredFeatures.map(f => f.properties.id))];
        dataToUse = allPerizinanData.filter(item => uniqueIds.includes(item.id));
    } else {
        dataToUse = allPerizinanData;
    }

    if (!dataToUse) return;

    const activeCount = dataToUse.filter(i => i.status === 'aktif').length;
    const warningCount = dataToUse.filter(i => i.status === 'hampir_habis').length;
    const totalPnbp = dataToUse.reduce((sum, item) => sum + parseFloat(item.pnbp || 0), 0);

    const activeEl = document.getElementById('summary-izin-aktif');
    const warningEl = document.getElementById('summary-jatuh-tempo');
    const pnbpEl = document.getElementById('summary-total-pnbp');

    if (activeEl) activeEl.innerText = activeCount;
    if (warningEl) warningEl.innerText = warningCount;
    if (pnbpEl) pnbpEl.innerText = formatRupiah(totalPnbp);
}

// Layer Toggles
document.getElementById('layer-ruas').addEventListener('change', (e) => {
    if (e.target.checked) map.addLayer(layers.ruas_jalan);
    else map.removeLayer(layers.ruas_jalan);
});

document.getElementById('layer-rumija').addEventListener('change', (e) => {
    if (e.target.checked) map.addLayer(layers.rumija);
    else map.removeLayer(layers.rumija);
});

document.getElementById('layer-titik').addEventListener('change', (e) => {
    if (e.target.checked) map.addLayer(layers.izin);
    else map.removeLayer(layers.izin);
});

// ─── STREET VIEW BAR LOGIC ──────────────────────────────────
const pegmanBtn = document.getElementById('pegman-btn');
const pegmanIcon = document.getElementById('pegman-icon');
const svTooltip = document.getElementById('sv-tooltip');
const mapDiv = document.getElementById('map');

if (pegmanBtn) {
    pegmanBtn.addEventListener('mouseenter', () => {
        if (svTooltip) svTooltip.style.opacity = '1';
    });
    
    pegmanBtn.addEventListener('mouseleave', () => {
        if (svTooltip) svTooltip.style.opacity = '0';
    });
    
    pegmanBtn.addEventListener('click', () => {
        const center = map.getCenter();
        openStreetView(center.lat, center.lng);
    });
}

// Drag & Drop Pegman logic
if (pegmanIcon && mapDiv) {
    pegmanIcon.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', 'pegman');
        pegmanIcon.classList.add('dragging');
        // Hide tooltip while dragging
        if (svTooltip) svTooltip.style.opacity = '0';
    });

    let dragTarget = null;

    pegmanIcon.addEventListener('dragend', () => {
        pegmanIcon.classList.remove('dragging');
        if (dragTarget) {
            dragTarget.remove();
            dragTarget = null;
        }
    });

    mapDiv.addEventListener('dragover', (e) => {
        e.preventDefault(); // Allow drop
        e.dataTransfer.dropEffect = 'move';

        if (!dragTarget) {
            dragTarget = document.createElement('div');
            dragTarget.className = 'drag-target-pointer';
            document.body.appendChild(dragTarget);
        }
        
        dragTarget.style.left = e.clientX + 'px';
        dragTarget.style.top = e.clientY + 'px';
    });

    mapDiv.addEventListener('drop', (e) => {
        e.preventDefault();
        const data = e.dataTransfer.getData('text/plain');
        if (data !== 'pegman') return;

        // Create ripple effect at drop point
        const ripple = document.createElement('div');
        ripple.className = 'drop-ripple';
        ripple.style.left = e.clientX + 'px';
        ripple.style.top = e.clientY + 'px';
        document.body.appendChild(ripple);
        setTimeout(() => ripple.remove(), 1000);

        const latlng = map.mouseEventToLatLng(e);
        const dropPoint = map.mouseEventToContainerPoint(e);
        
        let closestMarker = null;
        let minDistance = 40; // Tolerance in pixels to snap to a utility icon

        layers.izin.eachLayer(layer => {
            if (layer.getLatLng) {
                const markerPoint = map.latLngToContainerPoint(layer.getLatLng());
                const dist = markerPoint.distanceTo(dropPoint);
                
                if (dist < minDistance) {
                    minDistance = dist;
                    closestMarker = layer;
                }
            }
        });

        const targetLatLng = closestMarker ? closestMarker.getLatLng() : latlng;
        openStreetView(targetLatLng.lat, targetLatLng.lng);
    });
}

// Street View Modal Logic
const svModal = document.getElementById('sv-modal');
const svIframe = document.getElementById('sv-iframe');
const svLoading = document.getElementById('sv-loading');
const closeSvModal = document.getElementById('close-sv-modal');

function openStreetView(lat, lng) {
    console.log('Opening Street View in new tab for:', lat, lng);
    // Standard Google Maps URL for Street View
    const svUrl = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${lat},${lng}`;
    window.open(svUrl, '_blank');
}

if (closeSvModal) {
    closeSvModal.addEventListener('click', () => {
        console.log('Closing Street View Modal');
        svModal.classList.add('hidden');
        svModal.style.display = 'none';
        svIframe.src = ''; // Stop the loading/audio
    });
}

// Close modal on click outside content
if (svModal) {
    svModal.addEventListener('click', (e) => {
        if (e.target === svModal) {
            closeSvModal.click();
        }
    });
}

// ─── DEEP LINKING ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    
    if (id) {
        // Tunggu data geojson siap
        const checkReady = setInterval(() => {
            if (geojsonData && geojsonData.features.length > 0) {
                clearInterval(checkReady);
                focusOnPermit(id);
            }
        }, 500);
    }
});

function focusOnPermit(id) {
    const feature = geojsonData.features.find(f => String(f.properties.id) === String(id));
    if (feature) {
        // Buka panel detail
        openDetailPanel(feature.properties);
        
        // Cari layer di peta
        layers.izin.eachLayer(layer => {
            // Kita prioritaskan marker (titik tengah) daripada poligon/garis untuk pemusatan
            if (layer.feature && String(layer.feature.properties.id) === String(id)) {
                if (layer instanceof L.Marker) {
                    map.setView(layer.getLatLng(), 16);
                    layer.openPopup();
                } else if (!layer._popupOpened) { // Jika belum ada yang buka, gunakan geometri apapun
                     if (layer.getBounds) {
                        map.fitBounds(layer.getBounds(), { padding: [50, 50] });
                        layer.openPopup();
                    }
                }
            }
        });
    }
}
