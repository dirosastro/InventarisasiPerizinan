// ─── GLOBAL DATA ───────────────────────────────────────────
let allSatker = [];
let allPpk = [];
let allRuasJalan = [];
let geojsonData = null;
let editId = new URLSearchParams(window.location.search).get('id');
let uploadedFilesMetadata = []; // Metadata file yang sudah berhasil diunggah
let deletedDokumenIds = []; // Array untuk menampung ID dokumen yang dihapus

const API_BASE = window.API_BASE_URL || '';

// ─── DOM ELEMENTS ──────────────────────────────────────────
const jenisIzin = document.getElementById('jenis_izin');
const subJenis = document.getElementById('sub_jenis');
const iconOptions = document.querySelectorAll('.icon-option');
const iconInput = document.getElementById('icon_input');
const docInput = document.getElementById('dokumen_pendukung');
const docList = document.getElementById('dokumen-list');
const pnbpInput = document.getElementById('pnbp');
const mainSatker = document.getElementById('main_satker');
const form = document.getElementById('form-perizinan');
const btnSubmit = document.getElementById('btn-submit');
const toast = document.getElementById('toast');
const toastMessage = document.getElementById('toast-message');

const subJenisOptions = {
    'izin': [
        { value: 'Izin Penempatan Jaringan Utilitas', text: 'Izin Penempatan Jaringan Utilitas' },
        { value: 'Izin Penempatan Iklan/Reklame', text: 'Izin Penempatan Iklan/Reklame' }
    ],
    'rekomendasi': [
        { value: 'Akses Jalan Keluar/Masuk', text: 'Akses Jalan Keluar/Masuk' }
    ],
    'dispensasi': [
        { value: '-', text: 'Tidak ada Sub Jenis' }
    ]
};

function updateSubJenis() {
    const selected = jenisIzin.value;
    const options = subJenisOptions[selected] || [];
    subJenis.innerHTML = options.map(opt => `<option value="${opt.value}">${opt.text}</option>`).join('');

    if (selected === 'dispensasi') {
        subJenis.disabled = true;
        subJenis.removeAttribute('required');
    } else {
        subJenis.disabled = false;
        subJenis.setAttribute('required', 'required');
    }
    handleTanggalAkhirVisibility();
}

function handleTanggalAkhirVisibility() {
    const container = document.getElementById('container_tanggal_akhir');
    const input = document.getElementById('tanggal_akhir');

    if (subJenis.value === 'Akses Jalan Keluar/Masuk') {
        if (container) container.classList.add('hidden');
        if (input) {
            input.removeAttribute('required');
            input.value = '';
        }
    } else {
        if (container) container.classList.remove('hidden');
        if (input) input.setAttribute('required', 'required');
    }
    handlePnbpVisibility();
    handleDimensiVisibility();
    autoSelectIcon();
}

function handleDimensiVisibility() {
    const containerPanjang = document.getElementById('container_panjang');
    const inputPanjang = document.getElementById('panjang');

    if (subJenis.value === 'Akses Jalan Keluar/Masuk') {
        if (containerPanjang) containerPanjang.classList.add('hidden');
        if (inputPanjang) inputPanjang.value = '';
    } else {
        if (containerPanjang) containerPanjang.classList.remove('hidden');
    }
}

function autoSelectIcon() {
    const sj = subJenis.value;
    let iconToSelect = '';

    if (sj.includes('Utilitas')) {
        iconToSelect = 'ph-wifi-high';
    } else if (sj.includes('Reklame')) {
        iconToSelect = 'ph-signpost';
    } else if (sj.includes('Akses Jalan')) {
        iconToSelect = 'ph-car';
    } else if (sj.includes('Rambu')) {
        iconToSelect = 'ph-warning-octagon';
    } else if (sj.includes('Lahan') || sj.includes('Tanah')) {
        iconToSelect = 'ph-plant';
    } else {
        iconToSelect = 'ph-map-pin';
    }

    if (iconToSelect) {
        iconOptions.forEach(opt => {
            if (opt.dataset.icon === iconToSelect) {
                // Trigger the click event to select it
                iconOptions.forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
                iconInput.value = iconToSelect;
            }
        });
    }
}

function handlePnbpVisibility() {
    const container = document.getElementById('container_pnbp');
    const input = document.getElementById('pnbp');

    if (jenisIzin.value === 'rekomendasi' && subJenis.value === 'Akses Jalan Keluar/Masuk') {
        if (container) container.classList.add('hidden');
        if (input) input.value = '0';
    } else {
        if (container) container.classList.remove('hidden');
    }
}

// ─── PNBP FORMATTING ────────────────────────────────────────
if (pnbpInput) {
    pnbpInput.addEventListener('keydown', (e) => {
        const allowed = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Home', 'End'];
        if (!allowed.includes(e.key) && !/^\d$/.test(e.key) && !e.ctrlKey && !e.metaKey) {
            e.preventDefault();
        }
    });

    pnbpInput.addEventListener('input', (e) => {
        const input = e.target;
        const cursorPos = input.selectionStart;
        const oldValue = input.value;
        const rawDigits = oldValue.replace(/\D/g, '');
        const formatted = rawDigits === '' ? '' : rawDigits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Simple cursor management
        const digitsBefore = oldValue.substring(0, cursorPos).replace(/\./g, '').length;
        input.value = formatted;

        let newCursor = 0;
        let digitCount = 0;
        for (let i = 0; i < formatted.length; i++) {
            if (formatted[i] !== '.') digitCount++;
            if (digitCount === digitsBefore) {
                newCursor = i + 1;
                break;
            }
        }
        if (digitCount < digitsBefore) newCursor = formatted.length;
        input.setSelectionRange(newCursor, newCursor);
    });
}

// ─── GEOJSON UPLOAD ─────────────────────────────────────────
const fileInput = document.getElementById('geojson_file');
const fileDisplay = document.getElementById('file-name-display');
const fileText = document.getElementById('file-name-text');

if (fileInput) {
    fileInput.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        fileText.textContent = file.name;
        fileDisplay.classList.remove('hidden');
        const ext = file.name.split('.').pop().toLowerCase();

        try {
            if (ext === 'geojson') {
                const text = await file.text();
                JSON.parse(text);
                geojsonData = text;
            } else if (ext === 'kml') {
                const text = await file.text();
                const dom = new DOMParser().parseFromString(text, "text/xml");
                const geojson = toGeoJSON.kml(dom);
                geojsonData = JSON.stringify(geojson);
            } else if (ext === 'kmz') {
                const arrayBuffer = await file.arrayBuffer();
                const zip = await JSZip.loadAsync(arrayBuffer);
                const kmlFileObj = Object.values(zip.files).find(f => f.name.toLowerCase().endsWith('.kml'));
                if (!kmlFileObj) throw new Error("File KML tidak ditemukan di dalam KMZ");
                const kmlText = await kmlFileObj.async('string');
                const dom = new DOMParser().parseFromString(kmlText, "text/xml");
                const geojson = toGeoJSON.kml(dom);
                geojsonData = JSON.stringify(geojson);
            } else {
                throw new Error("Format tidak didukung");
            }
        } catch (err) {
            console.error(err);
            alert("Gagal membaca file: " + err.message);
            fileInput.value = "";
            fileDisplay.classList.add('hidden');
            geojsonData = null;
        }
    });
}

// ─── MASTER DATA ───────────────────────────────────────────
async function fetchAllMasterData() {
    try {
        const [sRes, pRes, rRes] = await Promise.all([
            fetch(API_BASE + '/api/satker'),
            fetch(API_BASE + '/api/ppk'),
            fetch(API_BASE + '/api/ruas-jalan')
        ]);
        const [sJson, pJson, rJson] = await Promise.all([sRes.json(), pRes.json(), rRes.json()]);
        if (sJson.success) {
            allSatker = sJson.data;
            buildSelectOptions(mainSatker, allSatker, s => s.id, s => s.nama_satker, '-- Pilih Satker --');
        }
        if (pJson.success) allPpk = pJson.data;
        if (rJson.success) allRuasJalan = rJson.data;
    } catch (error) {
        console.error("Gagal mengambil master data:", error);
    }
}

function buildSelectOptions(selectEl, items, valueFn, labelFn, placeholder) {
    if (!selectEl) return;
    selectEl.innerHTML = `<option value="">${placeholder}</option>`;
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = valueFn(item);
        opt.textContent = labelFn(item);
        selectEl.appendChild(opt);
    });
}

// ─── MULTI-LOKASI ───────────────────────────────────────────
function updateLokasiEmpty() {
    const container = document.getElementById('lokasi-container');
    const empty = document.getElementById('lokasi-empty');
    if (empty) empty.classList.toggle('hidden', container && container.children.length > 0);
}

function tambahLokasi() {
    const idx = Date.now();
    const container = document.getElementById('lokasi-container');
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'bg-white border border-gray-200 rounded-xl p-4 space-y-3 shadow-sm';
    row.dataset.idx = idx;
    row.innerHTML = `
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-accent uppercase tracking-wide">
                <i class="ph-fill ph-map-pin"></i> Lokasi ${container.children.length + 1}
            </span>
            <button type="button" class="btn-hapus-lokasi text-red-400 hover:text-red-600 transition-colors text-lg p-1 rounded-md hover:bg-red-50">
                <i class="ph ph-x"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Satker</label>
                <select class="sel-satker w-full px-2 py-1.5 border border-gray-200 rounded-lg text-sm bg-gray-50">
                    <option value="">-- Pilih Satker --</option>
                    ${allSatker.map(s => `<option value="${s.id}">${s.nama_satker}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">PPK</label>
                <select class="sel-ppk w-full px-2 py-1.5 border border-gray-200 rounded-lg text-sm bg-gray-50" disabled>
                    <option value="">-- Pilih Satker dulu --</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Ruas Jalan</label>
                <select class="sel-ruas w-full px-2 py-1.5 border border-gray-200 rounded-lg text-sm bg-gray-50" disabled>
                    <option value="">-- Pilih PPK dulu --</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">STA / Koordinat Awal</label>
                <input type="text" class="inp-sta-awal w-full px-2 py-1.5 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-accent focus:outline-none transition-all" placeholder="Km 12+500 atau -8.5, 116.1">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">STA / Koordinat Akhir</label>
                <input type="text" class="inp-sta-akhir w-full px-2 py-1.5 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-accent focus:outline-none transition-all" placeholder="Km 13+000 atau -8.6, 116.2">
            </div>
        </div>
    `;

    container.appendChild(row);
    updateLokasiEmpty();
    updateLokasiNumbers();

    const selSatker = row.querySelector('.sel-satker');
    const selPpk = row.querySelector('.sel-ppk');
    const selRuas = row.querySelector('.sel-ruas');
    const btnHapus = row.querySelector('.btn-hapus-lokasi');

    btnHapus.onclick = () => {
        row.remove();
        updateLokasiEmpty();
        updateLokasiNumbers();
    };

    selSatker.addEventListener('change', () => {
        const sid = selSatker.value;
        if (sid) {
            const filtered = allPpk.filter(p => String(p.satker_id) === String(sid));
            buildSelectOptions(selPpk, filtered, p => p.id, p => p.nama_ppk, '-- Pilih PPK --');
            selPpk.disabled = false;
        } else {
            selPpk.innerHTML = '<option value="">-- Pilih Satker dulu --</option>';
            selPpk.disabled = true;
        }
        selRuas.innerHTML = '<option value="">-- Pilih PPK dulu --</option>';
        selRuas.disabled = true;
    });

    selPpk.addEventListener('change', () => {
        const pid = selPpk.value;
        if (pid) {
            const filtered = allRuasJalan.filter(r => String(r.ppk_id) === String(pid));
            buildSelectOptions(selRuas, filtered, r => r.nama_ruas, r => r.nama_ruas, '-- Pilih Ruas Jalan --');
            selRuas.disabled = false;
        } else {
            selRuas.innerHTML = '<option value="">-- Pilih PPK dulu --</option>';
            selRuas.disabled = true;
        }
    });
}

function updateLokasiNumbers() {
    document.querySelectorAll('#lokasi-container > div').forEach((row, i) => {
        const label = row.querySelector('span.text-xs');
        if (label) label.innerHTML = `<i class="ph-fill ph-map-pin"></i> Lokasi ${i + 1}`;
    });
}

function collectLokasi() {
    const rows = document.querySelectorAll('#lokasi-container > div');
    const result = [];
    rows.forEach(row => {
        result.push({
            satker_id: parseInt(row.querySelector('.sel-satker').value) || null,
            ppk_id: parseInt(row.querySelector('.sel-ppk').value) || null,
            nama_ruas_jalan: row.querySelector('.sel-ruas').value || '',
            sta_awal: row.querySelector('.inp-sta-awal').value || null,
            sta_akhir: row.querySelector('.inp-sta-akhir').value || null,
        });
    });
    return result;
}

// ─── FORM SUBMIT ────────────────────────────────────────────
if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const lokasiData = collectLokasi();
        if (lokasiData.length === 0) {
            showToast('Harap tambahkan minimal 1 lokasi pemanfaatan!', 'error');
            return;
        }
        const invalid = lokasiData.find(l => !l.satker_id || !l.ppk_id || !l.nama_ruas_jalan);
        if (invalid) {
            showToast('Setiap lokasi harus memiliki Satker, PPK, dan Ruas Jalan!', 'error');
            return;
        }

        btnSubmit.innerHTML = `<i class="ph ph-spinner animate-spin"></i> Menyimpan...`;
        btnSubmit.disabled = true;

        const formData = new FormData();
        formData.append('nomor_izin', document.getElementById('nomor_izin').value);
        formData.append('jenis_izin', document.getElementById('jenis_izin').value);
        formData.append('sub_jenis', document.getElementById('sub_jenis').value);
        formData.append('pemohon', document.getElementById('pemohon').value);
        formData.append('no_hp', document.getElementById('no_hp').value);
        formData.append('tanggal_terbit', document.getElementById('tanggal_terbit').value);
        formData.append('tanggal_akhir', document.getElementById('tanggal_akhir').value);

        const pnbpRaw = document.getElementById('pnbp').value.replace(/\./g, '') || 0;
        formData.append('pnbp', pnbpRaw);
        
        formData.append('panjang', document.getElementById('panjang').value || '');
        formData.append('lebar', document.getElementById('lebar').value || '');

        formData.append('satker_id', mainSatker.value);
        formData.append('icon', iconInput.value || '');
        formData.append('geojson', geojsonData || '');
        formData.append('lokasi', JSON.stringify(lokasiData));
        formData.append('uploaded_dokumen', JSON.stringify(uploadedFilesMetadata));

        // Tambahkan ID dokumen yang akan dihapus jika ada
        if (deletedDokumenIds.length > 0) {
            deletedDokumenIds.forEach(id => {
                formData.append('deleted_dokumen[]', id);
            });
        }

        const url = editId ? `${API_BASE}/api/perizinan/${editId}` : `${API_BASE}/api/perizinan`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const response = await fetch(url, {
                method: 'POST',
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });
            const result = await response.json();
            if (response.ok && result.success) {
                setTimeout(() => window.location.href = (window.API_BASE_URL || '') + '/perizinan-data', 1500);
            } else {
                showToast(result.message || 'Gagal menyimpan data.', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Gagal terhubung ke server.', 'error');
        } finally {
            btnSubmit.innerHTML = `<i class="ph ph-floppy-disk"></i> Simpan Data`;
            btnSubmit.disabled = false;
        }
    });
}

function showToast(message, type) {
    if (!toast) { alert(message); return; }
    toastMessage.textContent = message;
    toast.className = 'p-4 mb-4 text-sm rounded-lg border';
    toast.classList.add(type === 'success' ? 'bg-green-50' : 'bg-red-50',
        type === 'success' ? 'text-green-700' : 'text-red-700',
        type === 'success' ? 'border-green-200' : 'border-red-200');
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 5000);
}

// ─── INIT ───────────────────────────────────────────────────
async function fetchAndPopulateData() {
    if (!editId) return;

    const pageTitle = document.getElementById('page-title');
    if (pageTitle) pageTitle.textContent = 'Ubah Data Perizinan';
    if (btnSubmit) btnSubmit.innerHTML = `<i class="ph ph-floppy-disk"></i> Perbarui Data`;

    try {
        const response = await fetch(`${API_BASE}/api/perizinan/${editId}`);
        const result = await response.json();
        if (!result.success) return;

        const data = result.data;
        document.getElementById('nomor_izin').value = data.nomor_izin;
        document.getElementById('pemohon').value = data.pemohon;
        document.getElementById('no_hp').value = data.no_hp || '';
        document.getElementById('jenis_izin').value = data.jenis_izin;
        updateSubJenis();
        document.getElementById('sub_jenis').value = data.sub_jenis;
        document.getElementById('tanggal_terbit').value = data.tanggal_terbit;
        document.getElementById('tanggal_akhir').value = data.tanggal_akhir || '';
        if (mainSatker) mainSatker.value = data.satker_id || '';

        const pnbpStr = String(Math.round(parseFloat(data.pnbp || 0)));
        if (pnbpInput) pnbpInput.value = pnbpStr === '' ? '' : pnbpStr.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
        document.getElementById('panjang').value = data.panjang || '';
        document.getElementById('lebar').value = data.lebar || '';

        handleTanggalAkhirVisibility();

        if (data.icon) {
            iconInput.value = data.icon;
            iconOptions.forEach(opt => {
                if (opt.dataset.icon === data.icon) opt.classList.add('selected');
                else opt.classList.remove('selected');
            });
        }

        if (data.geojson) {
            geojsonData = data.geojson;
            if (fileText) fileText.textContent = "Data geospasial tersimpan";
            if (fileDisplay) fileDisplay.classList.remove('hidden');
        }

        // Lokasi
        if (data.lokasi && data.lokasi.length > 0) {
            data.lokasi.forEach((loc, i) => {
                tambahLokasi();
                const rows = document.querySelectorAll('#lokasi-container > div');
                const row = rows[rows.length - 1];

                const selSatker = row.querySelector('.sel-satker');
                const selPpk = row.querySelector('.sel-ppk');
                const selRuas = row.querySelector('.sel-ruas');

                selSatker.value = loc.satker_id;

                const filteredPpk = allPpk.filter(p => String(p.satker_id) === String(loc.satker_id));
                buildSelectOptions(selPpk, filteredPpk, p => p.id, p => p.nama_ppk, '-- Pilih PPK --');
                selPpk.disabled = false;
                selPpk.value = loc.ppk_id;

                const filteredRuas = allRuasJalan.filter(r => String(r.ppk_id) === String(loc.ppk_id));
                buildSelectOptions(selRuas, filteredRuas, r => r.nama_ruas, r => r.nama_ruas, '-- Pilih Ruas Jalan --');
                selRuas.disabled = false;
                selRuas.value = loc.nama_ruas_jalan;

                row.querySelector('.inp-sta-awal').value = loc.sta_awal || '';
                row.querySelector('.inp-sta-akhir').value = loc.sta_akhir || '';
            });
        }

        // Tampilkan Dokumen Pendukung Eksisting
        const existingDocList = document.getElementById('existing-dokumen-list');
        if (existingDocList && data.dokumen && data.dokumen.length > 0) {
                existingDocList.innerHTML = '<p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Dokumen Tersimpan:</p>';
                data.dokumen.forEach(doc => {
                    const item = document.createElement('div');
                    item.className = 'flex items-center justify-between p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs';
                    const displayFileName = (data.pemohon && doc.nama_file) ? (doc.nama_file.startsWith(data.pemohon) ? doc.nama_file : data.pemohon + '_' + doc.nama_file) : (doc.nama_file || 'Tanpa Nama');
                    item.innerHTML = `
                        <div class="flex items-center gap-2 text-gray-700">
                            <i class="ph ph-file-pdf text-lg text-red-500"></i>
                            <a href="${API_BASE}/api/perizinan/download/${doc.id}" target="_blank" class="font-medium hover:text-accent underline truncate max-w-[250px]">${displayFileName}</a>
                        </div>
                        <button type="button" class="btn-hapus-dokumen-eksisting text-red-500 hover:text-red-700 p-1" data-id="${doc.id}">
                            <i class="ph ph-trash text-lg"></i>
                        </button>
                    `;
                existingDocList.appendChild(item);

                item.querySelector('.btn-hapus-dokumen-eksisting').onclick = () => {
                    if (confirm('Hapus dokumen ini?')) {
                        deletedDokumenIds.push(doc.id);
                        item.remove();
                    }
                };
            });
        }

    } catch (err) {
        console.error("Error fetching data:", err);
        showToast('Gagal mengambil data perizinan.', 'error');
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    if (jenisIzin) jenisIzin.addEventListener('change', updateSubJenis);
    if (subJenis) subJenis.addEventListener('change', handleTanggalAkhirVisibility);

    iconOptions.forEach(option => {
        option.addEventListener('click', () => {
            iconOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            iconInput.value = option.dataset.icon;
        });
    });

    if (docInput) {
        docInput.addEventListener('change', () => {
            const files = Array.from(docInput.files);
            files.forEach(file => {
                uploadFileWithProgress(file);
            });
            docInput.value = ''; // Reset
        });
    }

    function uploadFileWithProgress(file) {
        if (!docList) return;

        // Validasi Nomor Izin & Pemohon
        const nomorIzin = document.getElementById('nomor_izin').value;
        const pemohon = document.getElementById('pemohon').value;

        if (!nomorIzin || !pemohon) {
            showToast('Harap isi Nomor Izin dan Pemohon terlebih dahulu sebelum mengunggah dokumen!', 'error');
            return;
        }

        const fileId = 'upload-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        const size = (file.size / 1024).toFixed(1);

        // Buat elemen progress bar
        const item = document.createElement('div');
        item.id = fileId;
        item.className = 'bg-white border border-gray-100 rounded-xl p-3 space-y-2 shadow-sm animate-fade-in';
        item.innerHTML = `
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-gray-700 font-medium">
                    <i class="ph ph-file-text text-lg text-accent"></i>
                    <span class="truncate max-w-[200px]">${file.name}</span>
                    <span class="text-gray-400">(${size} KB)</span>
                </div>
                <span class="progress-percent font-bold text-accent">0%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                <div class="progress-bar bg-accent h-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <div class="upload-status text-[10px] text-gray-500 italic">Menghubungkan...</div>
        `;
        docList.appendChild(item);

        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('file', file);
        formData.append('nomor_izin', nomorIzin);
        formData.append('pemohon', pemohon);

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                item.querySelector('.progress-bar').style.width = percent + '%';
                item.querySelector('.progress-percent').textContent = percent + '%';
                item.querySelector('.upload-status').textContent = percent < 100 ? 'Mengunggah ke server...' : 'Memproses ke Google Drive...';
            }
        });

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    const result = JSON.parse(xhr.responseText);
                    if (result.success) {
                        item.querySelector('.upload-status').innerHTML = '<span class="text-green-500 font-bold"><i class="ph ph-check-circle"></i> Berhasil diunggah ke Google Drive</span>';
                        item.querySelector('.progress-bar').classList.replace('bg-accent', 'bg-green-500');
                        item.querySelector('.progress-percent').classList.replace('text-accent', 'text-green-500');
                        
                        // Update nama tampilan dengan nama yang sudah diproses di server
                        const nameSpan = item.querySelector('span.truncate');
                        if (nameSpan) nameSpan.textContent = result.data.nama_file;

                        // Tambahkan metadata ke array global
                        uploadedFilesMetadata.push(result.data);

                        // Ganti dengan tombol hapus (mock hapus dari list lokal)
                        const actions = document.createElement('div');
                        actions.className = 'flex justify-end';
                        actions.innerHTML = `
                            <button type="button" class="text-red-500 hover:text-red-700 p-1 text-[10px] font-bold flex items-center gap-1">
                                <i class="ph ph-trash"></i> BATALKAN
                            </button>
                        `;
                        item.appendChild(actions);
                        actions.querySelector('button').onclick = () => {
                            uploadedFilesMetadata = uploadedFilesMetadata.filter(m => m.file_id !== result.data.file_id);
                            item.remove();
                        };
                    } else {
                        handleUploadError(item, result.message || 'Gagal diproses');
                    }
                } else if (xhr.status === 0) {
                    handleUploadError(item, 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
                } else if (xhr.status === 413) {
                    handleUploadError(item, 'Ukuran file terlalu besar. Maksimum 40MB.');
                } else if (xhr.status === 419) {
                    handleUploadError(item, 'Sesi telah berakhir. Silakan refresh halaman dan login ulang.');
                } else if (xhr.status === 401) {
                    handleUploadError(item, 'Sesi login telah berakhir. Silakan login ulang.');
                } else if (xhr.status === 422) {
                    let msg = 'Data tidak valid.';
                    try {
                        const errData = JSON.parse(xhr.responseText);
                        msg = errData.message || Object.values(errData.errors || {}).flat().join(', ') || msg;
                    } catch(e) {}
                    handleUploadError(item, msg);
                } else {
                    let serverMsg = '';
                    try {
                        const errData = JSON.parse(xhr.responseText);
                        serverMsg = errData.message || '';
                    } catch(e) {}
                    handleUploadError(item, serverMsg || `Kesalahan Server (${xhr.status}). Silakan coba lagi.`);
                }
            }
        };

        xhr.onerror = () => {
            handleUploadError(item, 'Koneksi terputus. Periksa jaringan Anda dan coba lagi.');
        };

        xhr.timeout = 300000; // 5 menit timeout untuk upload ke Google Drive
        xhr.ontimeout = () => {
            handleUploadError(item, 'Upload timeout. File terlalu besar atau koneksi terlalu lambat.');
        };

        xhr.open('POST', (window.API_BASE_URL || '') + '/api/perizinan/upload-temp');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
        xhr.send(formData);
    }

    function handleUploadError(item, message) {
        item.querySelector('.progress-bar').classList.replace('bg-accent', 'bg-red-500');
        item.querySelector('.progress-percent').classList.replace('text-accent', 'text-red-500');
        item.querySelector('.upload-status').innerHTML = `<span class="text-red-500 font-bold"><i class="ph ph-warning"></i> ${message}</span>`;
        
        const retryBtn = document.createElement('button');
        retryBtn.type = 'button';
        retryBtn.className = 'mt-2 text-[10px] font-bold text-gray-500 hover:text-gray-700';
        retryBtn.textContent = 'Hapus & Coba Lagi';
        retryBtn.onclick = () => item.remove();
        item.appendChild(retryBtn);
    }

    const btnTambahLokasi = document.getElementById('btn-tambah-lokasi');
    if (btnTambahLokasi) btnTambahLokasi.addEventListener('click', tambahLokasi);

    updateSubJenis();
    await fetchAllMasterData();
    updateLokasiEmpty();

    if (editId) {
        await fetchAndPopulateData();
    }
});
