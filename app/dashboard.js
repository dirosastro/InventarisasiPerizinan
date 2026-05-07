// Dashboard API URLs
const API_PERIZINAN = 'http://127.0.0.1:8000/api/perizinan';

// Global Chart Instances to allow updates
let pnbpChartInstance = null;
let typeChartInstance = null;
let satkerChartInstance = null;
let allData = [];

// Initialize Dashboard on DOM Content Loaded
document.addEventListener('DOMContentLoaded', () => {
    
    // Global Chart Settings
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#718096';
    
    fetchDashboardData();

    // Event Listener for Year Filter
    document.getElementById('year-filter').addEventListener('change', (e) => {
        const val = e.target.value;
        updateChartsByYear(val === 'all' ? 'all' : parseInt(val));
    });
});

async function fetchDashboardData() {
    try {
        const response = await fetch(API_PERIZINAN);
        const result = await response.json();
        if (!result.success) throw new Error(result.message);

        allData = result.data;
        
        // Setup Year Filter Options
        populateYearFilter(allData);
        
        // Default to "All Years" or Current Year? User said "semua tahun harus ada", usually means default is a specific year or all.
        // Let's default to "All Years" since they emphasized it.
        document.getElementById('year-filter').value = 'all';

        updateKPIs(allData);
        updateRecentPermits(allData);
        updateChartsByYear('all');

    } catch (error) {
        console.error("Dashboard Error:", error);
    }
}

function populateYearFilter(data) {
    const years = [...new Set(data.map(i => new Date(i.tanggal_terbit).getFullYear()))].sort((a, b) => b - a);
    const select = document.getElementById('year-filter');
    select.innerHTML = '<option value="all">Semua Tahun</option>';
    
    years.forEach(year => {
        const opt = document.createElement('option');
        opt.value = year;
        opt.textContent = `Tahun ${year}`;
        select.appendChild(opt);
    });
}

function updateChartsByYear(year) {
    // Update Chart Title
    const chartTitle = document.querySelector('h3.text-base.font-bold.text-gray-800');
    if (chartTitle && chartTitle.textContent.includes('Tren Penerimaan PNBP')) {
        chartTitle.textContent = `Tren Penerimaan PNBP (${year === 'all' ? 'Semua Tahun' : year})`;
    }

    initPnbpChart(allData, year);
    initTypeChart(allData, year);
    initSatkerChart(allData, year);
}

function updateKPIs(data) {
    const total = data.length;
    const aktif = data.filter(i => i.status === 'aktif').length;
    const warning = data.filter(i => i.status === 'hampir_habis').length;
    const pnbp = data.reduce((sum, i) => sum + (parseFloat(i.pnbp) || 0), 0);
    
    let totalPanjang = 0;
    data.forEach(i => {
        if (i.lokasi) {
            i.lokasi.forEach(l => {
                const s = parseSta(l.sta_awal);
                const e = parseSta(l.sta_akhir);
                if (s !== null && e !== null) totalPanjang += Math.abs(e - s);
            });
        }
    });

    document.getElementById('total-perizinan').textContent = total.toLocaleString('id-ID');
    document.getElementById('izin-aktif').textContent = aktif.toLocaleString('id-ID');
    document.getElementById('hampir-habis').textContent = warning.toLocaleString('id-ID');
    
    if (pnbp >= 1000000000) {
        document.getElementById('total-pnbp').textContent = 'Rp ' + (pnbp / 1000000000).toFixed(2) + ' M';
    } else {
        document.getElementById('total-pnbp').textContent = 'Rp ' + (pnbp / 1000000).toFixed(1) + ' Jt';
    }
    document.getElementById('total-panjang').textContent = totalPanjang.toFixed(1) + ' Km';
}

function parseSta(sta) {
    if (!sta) return null;
    const match = sta.match(/(\d+)\+(\d+)/);
    if (match) return parseInt(match[1]) + (parseInt(match[2]) / 1000);
    return null;
}

function updateRecentPermits(data) {
    const tbody = document.getElementById('recent-perizinan');
    tbody.innerHTML = '';
    const recent = [...data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 5);
    
    recent.forEach(item => {
        const tgl = new Date(item.tanggal_terbit).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        const ruas = item.lokasi && item.lokasi.length > 0 ? item.lokasi[0].nama_ruas_jalan : '-';
        
        let statusBadge = '';
        if (item.status === 'aktif') {
            statusBadge = `<span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded bg-green-100 text-green-700">Aktif</span>`;
        } else if (item.status === 'hampir_habis') {
            statusBadge = `<span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded bg-yellow-100 text-yellow-700">Jatuh Tempo</span>`;
        } else {
            statusBadge = `<span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded bg-red-100 text-red-700">Expired</span>`;
        }

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 transition-colors';
        tr.innerHTML = `
            <td class="px-5 py-3">
                <div class="font-medium text-gray-800">${item.pemohon}</div>
                <div class="text-[10px] text-gray-500 uppercase">${ruas}</div>
            </td>
            <td class="px-5 py-3 text-xs text-gray-600">${item.sub_jenis || item.jenis_izin}</td>
            <td class="px-5 py-3 text-xs text-gray-600">${tgl}</td>
            <td class="px-5 py-3 text-center">${statusBadge}</td>
            <td class="px-5 py-3 text-center">
                ${item.status === 'hampir_habis' ? `
                    <a href="https://wa.me/?text=Halo%20${encodeURIComponent(item.pemohon)},%20izin%20${encodeURIComponent(item.sub_jenis)}%20Anda%20akan%20segera%20berakhir." target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded bg-[#25D366] text-white hover:bg-[#128C7E] transition-colors shadow-sm">
                        <i class="ph ph-whatsapp-logo text-lg"></i>
                    </a>
                ` : '<span class="text-gray-300">-</span>'}
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function initPnbpChart(data, year) {
    if (pnbpChartInstance) pnbpChartInstance.destroy();
    const ctx = document.getElementById('pnbpChart').getContext('2d');
    
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    const pnbpByMonth = new Array(12).fill(0);
    
    data.forEach(item => {
        const date = new Date(item.tanggal_terbit);
        if (year === 'all' || date.getFullYear() === year) {
            pnbpByMonth[date.getMonth()] += (parseFloat(item.pnbp) || 0) / 1000000;
        }
    });

    pnbpChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'PNBP (Juta Rp)',
                data: pnbpByMonth,
                borderColor: '#3182CE',
                backgroundColor: 'rgba(49, 130, 206, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' Jt' } } }
        }
    });
}

function initTypeChart(data, year) {
    if (typeChartInstance) typeChartInstance.destroy();
    const ctx = document.getElementById('typeChart').getContext('2d');
    
    const counts = {};
    data.forEach(item => {
        const date = new Date(item.tanggal_terbit);
        if (year === 'all' || date.getFullYear() === year) {
            const type = item.sub_jenis || item.jenis_izin;
            counts[type] = (counts[type] || 0) + 1;
        }
    });

    const labels = Object.keys(counts);
    const values = Object.values(counts);

    typeChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: ['#3182CE', '#48BB78', '#ECC94B', '#ED64A6', '#805AD5', '#38B2AC'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

function initSatkerChart(data, year) {
    if (satkerChartInstance) satkerChartInstance.destroy();
    const ctx = document.getElementById('satkerChart').getContext('2d');
    
    const satkerData = {};
    data.forEach(item => {
        const date = new Date(item.tanggal_terbit);
        if ((year === 'all' || date.getFullYear() === year) && item.satker) {
            const name = item.satker.nama_satker.replace('Satker PJN Wilayah ', 'Wil. ');
            if (!satkerData[name]) satkerData[name] = { aktif: 0, warning: 0 };
            if (item.status === 'aktif') satkerData[name].aktif++;
            else if (item.status === 'hampir_habis') satkerData[name].warning++;
        }
    });

    const labels = Object.keys(satkerData);
    const aktif = labels.map(l => satkerData[l].aktif);
    const warning = labels.map(l => satkerData[l].warning);

    satkerChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Aktif', data: aktif, backgroundColor: '#48BB78', borderRadius: 4 },
                { label: 'Jatuh Tempo', data: warning, backgroundColor: '#ECC94B', borderRadius: 4 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
        }
    });
}
