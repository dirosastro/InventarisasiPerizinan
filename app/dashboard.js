// Dashboard API URLs
const API_PERIZINAN = '/api/perizinan';

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
        `;
        tbody.appendChild(tr);
    });
}

function initPnbpChart(data, year) {
    if (pnbpChartInstance) pnbpChartInstance.destroy();
    const ctx = document.getElementById('pnbpChart').getContext('2d');

    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

    let labels = [];
    let pnbpValues = [];

    if (year === 'all') {
        // Kumpulkan semua tahun yang ada dalam data
        const yearSet = new Set(data.map(item => new Date(item.tanggal_terbit).getFullYear()));
        const sortedYears = [...yearSet].sort((a, b) => a - b);

        // Buat map: 'YYYY-MM' => total PNBP
        const pnbpByYearMonth = {};
        data.forEach(item => {
            const date = new Date(item.tanggal_terbit);
            const key = `${date.getFullYear()}-${String(date.getMonth()).padStart(2, '0')}`;
            pnbpByYearMonth[key] = (pnbpByYearMonth[key] || 0) + (parseFloat(item.pnbp) || 0) / 1000000;
        });

        // Buat label kronologis dari tahun minimum ke maksimum
        sortedYears.forEach(yr => {
            for (let m = 0; m < 12; m++) {
                const key = `${yr}-${String(m).padStart(2, '0')}`;
                labels.push(`${monthNames[m]} ${yr}`);
                pnbpValues.push(pnbpByYearMonth[key] || 0);
            }
        });
    } else {
        // Filter per tahun tertentu: tampilkan 12 bulan saja
        labels = monthNames;
        pnbpValues = new Array(12).fill(0);
        data.forEach(item => {
            const date = new Date(item.tanggal_terbit);
            if (date.getFullYear() === year) {
                pnbpValues[date.getMonth()] += (parseFloat(item.pnbp) || 0) / 1000000;
            }
        });
    }

    pnbpChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'PNBP (Juta Rp)',
                data: pnbpValues,
                borderColor: '#3182CE',
                backgroundColor: 'rgba(49, 130, 206, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        maxRotation: year === 'all' ? 45 : 0,
                        minRotation: year === 'all' ? 45 : 0,
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => 'Rp ' + v + ' Jt' }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` PNBP: Rp ${context.parsed.y.toFixed(2)} Juta`;
                        }
                    }
                }
            }
        }
    });
}

function initTypeChart(data, year) {
    if (typeChartInstance) typeChartInstance.destroy();
    const ctx = document.getElementById('typeChart').getContext('2d');

    // Kelompokkan berdasarkan jenis_izin (sesuai formulir: izin, rekomendasi, dispensasi)
    // dan kumpulkan daftar sub_jenis unik per grup untuk tooltip
    const counts = {};
    const subJenisPerGroup = {}; // { 'Izin': Set(['Izin Penempatan...', ...]), ... }

    const jenisLabelMap = {
        'izin': 'Izin',
        'rekomendasi': 'Rekomendasi',
        'dispensasi': 'Dispensasi'
    };

    data.forEach(item => {
        const date = new Date(item.tanggal_terbit);
        if (year === 'all' || date.getFullYear() === year) {
            // Kelompokkan berdasarkan jenis_izin utama dari formulir
            const jenisKey = item.jenis_izin ? item.jenis_izin.toLowerCase() : 'izin';
            const groupLabel = jenisLabelMap[jenisKey] || jenisKey;

            counts[groupLabel] = (counts[groupLabel] || 0) + 1;

            // Kumpulkan sub_jenis unik dalam grup ini
            if (!subJenisPerGroup[groupLabel]) subJenisPerGroup[groupLabel] = new Set();
            const sj = (item.sub_jenis && item.sub_jenis !== '-') ? item.sub_jenis : null;
            if (sj) subJenisPerGroup[groupLabel].add(sj);
        }
    });

    const labels = Object.keys(counts);
    const values = Object.values(counts);

    const colorMap = {
        'Izin': '#3182CE',
        'Rekomendasi': '#48BB78',
        'Dispensasi': '#ECC94B'
    };
    const defaultColors = ['#ED64A6', '#805AD5', '#38B2AC', '#E53E3E', '#DD6B20', '#4A5568'];
    const backgroundColors = labels.map((label, index) => {
        return colorMap[label] || defaultColors[index % defaultColors.length];
    });

    const doughnutPercentagePlugin = {
        id: 'doughnutPercentagePlugin',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            chart.data.datasets.forEach((dataset, i) => {
                const meta = chart.getDatasetMeta(i);
                meta.data.forEach((element, index) => {
                    const dataValue = dataset.data[index];
                    if (dataValue === 0) return;

                    const total = dataset.data.reduce((sum, val) => sum + val, 0);
                    const percentage = total > 0 ? ((dataValue / total) * 100).toFixed(1) + '%' : '0%';

                    const { x, y } = (element.tooltipPosition && typeof element.tooltipPosition === 'function')
                        ? element.tooltipPosition()
                        : { x: element.x, y: element.y };

                    ctx.save();
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 11px Inter, sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
                    ctx.shadowBlur = 3;
                    ctx.fillText(percentage, x, y);
                    ctx.restore();
                });
            });
        }
    };

    typeChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: backgroundColors,
                borderWidth: 0
            }]
        },
        plugins: [doughnutPercentagePlugin],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                const dataset = data.datasets[0];
                                const total = dataset.data.reduce((sum, val) => sum + val, 0);
                                const meta = chart.getDatasetMeta(0);
                                return data.labels.map((label, i) => {
                                    const value = dataset.data[i];
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';

                                    let fillStyle = backgroundColors[i];
                                    let strokeStyle = '#fff';
                                    let lineWidth = 0;
                                    if (meta && meta.controller && typeof meta.controller.getStyle === 'function') {
                                        try {
                                            const style = meta.controller.getStyle(i);
                                            if (style) {
                                                fillStyle = style.backgroundColor || fillStyle;
                                                strokeStyle = style.borderColor || strokeStyle;
                                                lineWidth = style.borderWidth !== undefined ? style.borderWidth : lineWidth;
                                            }
                                        } catch (e) {}
                                    }

                                    let hidden = false;
                                    if (typeof chart.getDataVisibility === 'function') {
                                        hidden = !chart.getDataVisibility(i);
                                    } else if (meta && meta.data && meta.data[i]) {
                                        hidden = meta.data[i].hidden;
                                    }

                                    return {
                                        text: `${label} (${value} - ${percentage})`,
                                        fillStyle: fillStyle,
                                        strokeStyle: strokeStyle,
                                        lineWidth: lineWidth,
                                        hidden: hidden,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label;
                            const value = context.raw;
                            const total = context.chart.data.datasets[0].data.reduce((sum, val) => sum + val, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';

                            const lines = [
                                ` Jenis Izin : ${label}`,
                                ` Jumlah     : ${value} izin (${percentage})`
                            ];

                            // Tampilkan daftar sub jenis yang ada dalam grup ini
                            const subJenisSet = subJenisPerGroup[label];
                            if (subJenisSet && subJenisSet.size > 0) {
                                lines.push(` Sub Jenis  :`);
                                subJenisSet.forEach(sj => lines.push(`   • ${sj}`));
                            }

                            return lines;
                        }
                    }
                }
            }
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
