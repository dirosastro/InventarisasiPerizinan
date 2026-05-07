// Initialize Charts on DOM Content Loaded
document.addEventListener('DOMContentLoaded', () => {
    
    // Global Chart Settings for consistent aesthetics
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#718096';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(30, 58, 95, 0.9)';
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    
    initPnbpChart();
    initTypeChart();
    initSatkerChart();
});

function initPnbpChart() {
    const ctx = document.getElementById('pnbpChart').getContext('2d');
    
    // Gradient fill for the line chart
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(49, 130, 206, 0.5)'); // Accent color fading out
    gradient.addColorStop(1, 'rgba(49, 130, 206, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Realisasi PNBP (Juta Rupiah)',
                data: [320, 450, 410, 580, 620, 590, 750, 810, 890, 950, 1100, 1250],
                borderColor: '#3182CE',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3182CE',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // smooth curves
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#EDF2F7',
                        drawBorder: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value + ' Jt';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false,
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
}

function initTypeChart() {
    const ctx = document.getElementById('typeChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Kabel Optik', 'Pipa Air', 'Reklame', 'Dispensasi Jalan'],
            datasets: [{
                data: [50, 25, 15, 10],
                backgroundColor: [
                    '#3182CE', // Blue
                    '#48BB78', // Green
                    '#ECC94B', // Yellow
                    '#ED64A6'  // Red/Pink
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.label}: ${context.raw}%`;
                        }
                    }
                }
            }
        }
    });
}

function initSatkerChart() {
    const ctx = document.getElementById('satkerChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['PPK 1.1', 'PPK 1.2', 'PPK 1.3', 'PPK 1.4', 'PPK 2.1', 'PPK 2.2'],
            datasets: [
                {
                    label: 'Izin Aktif',
                    data: [120, 150, 90, 80, 110, 130],
                    backgroundColor: '#48BB78', // Green
                    borderRadius: 4,
                },
                {
                    label: 'Jatuh Tempo',
                    data: [15, 10, 25, 5, 20, 12],
                    backgroundColor: '#ECC94B', // Yellow
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    grid: {
                        color: '#EDF2F7',
                        borderDash: [5, 5]
                    }
                }
            }
        }
    });
}
