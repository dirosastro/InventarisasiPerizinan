<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Dashboard Monitoring Perizinan Pemanfaatan Bagian Jalan Nasional di Provinsi Nusa Tenggara Barat secara terintegrasi dan real-time.">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>Beranda - Simpanan (Sistem Informasi Perizinan Jalan)</title>

    <!-- Resource Hints & Preconnects -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'bps-navy': '#003366',
                        'bps-blue': '#007BFF',
                        'bps-light': '#F8F9FA',
                        'bps-gray': '#6C757D',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web" defer></script>

    <style>
        .hero-overlay {
            background: linear-gradient(rgba(0, 51, 102, 0.8), rgba(0, 51, 102, 0.6));
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stat-card {
            transition: all 0.3s ease;
            border-top: 4px solid #007BFF;
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        .stat-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175);
        }

        .animate-in {
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }

        .nav-sticky {
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 4px;
            background: #007BFF;
            margin-top: 10px;
        }

        .footer-bps {
            background-color: #002244;
        }
    </style>
</head>

<body class="bg-white text-gray-900 antialiased">

    <!-- NAVIGATION -->
    <nav class="nav-sticky bg-white py-4 px-6 border-b border-gray-100">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Simpanan BPJN NTB" width="48" height="48" class="h-12 w-auto">
                <div class="flex flex-col leading-tight border-l pl-4 border-gray-200">
                    <span class="font-bold text-xl text-bps-navy tracking-tight uppercase">Simpanan</span>
                    <span class="text-[10px] text-bps-gray font-semibold tracking-widest uppercase">Sistem Informasi
                        Perizinan Jalan</span>
                </div>
            </div>

            <div class="hidden lg:flex items-center space-x-8 text-sm font-semibold text-bps-navy">
                <a href="{{ route('home') }}" class="text-bps-blue">Beranda</a>
                <a href="{{ route('dashboard') }}" id="nav-dashboard-link" class="hidden hover:text-bps-blue transition-colors">Dashboard</a>
                <a href="{{ route('peta') }}" class="hover:text-bps-blue transition-colors">Peta Pemanfaatan</a>
                <a href="{{ route('perizinan_view') }}" id="nav-perizinan-link" class="hidden hover:text-bps-blue transition-colors">Data Perizinan</a>
                <a href="{{ route('dasar-hukum') }}" class="hover:text-bps-blue transition-colors">Dasar Hukum</a>
                <a href="{{ route('login') }}" id="nav-login-btn"
                    class="bg-bps-blue text-white px-6 py-2.5 rounded-md hover:bg-blue-700 transition-all flex items-center gap-2 shadow-sm">
                    <i class="ph ph-user-circle text-lg"></i>
                    <span>Akses Admin</span>
                </a>
            </div>

            <!-- MOBILE MENU BUTTON -->
            <button id="mobile-menu-btn" aria-label="Toggle navigation menu" aria-expanded="false" class="lg:hidden text-bps-navy p-2 focus:outline-none">
                <i class="ph ph-list text-3xl" aria-hidden="true"></i>
            </button>
        </div>

        <!-- MOBILE MENU CONTENT -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-100 flex flex-col p-6 space-y-4 font-semibold text-bps-navy animate-in">
            <a href="{{ route('home') }}" class="text-bps-blue py-2 border-b border-gray-50">Beranda</a>
            <a href="{{ route('dashboard') }}" id="mobile-nav-dashboard-link" class="hidden hover:text-bps-blue transition-colors py-2 border-b border-gray-50">Dashboard</a>
            <a href="{{ route('peta') }}" class="hover:text-bps-blue transition-colors py-2 border-b border-gray-50">Peta Pemanfaatan</a>
            <a href="{{ route('perizinan_view') }}" id="mobile-nav-perizinan-link" class="hidden hover:text-bps-blue transition-colors py-2 border-b border-gray-50">Data Perizinan</a>
            <a href="{{ route('dasar-hukum') }}" class="hover:text-bps-blue transition-colors py-2 border-b border-gray-50">Dasar Hukum</a>
            <a href="{{ route('login') }}" id="mobile-nav-login-btn"
                class="bg-bps-blue text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-all flex items-center justify-center gap-2 shadow-sm">
                <i class="ph ph-user-circle text-lg"></i>
                <span>Akses Admin</span>
            </a>
        </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="relative min-h-[calc(100vh-80px)] flex flex-col justify-center items-center overflow-hidden py-20">
        <img src="{{ asset('img/back_login.png') }}" alt="Latar belakang pemandangan jalan" width="1920" height="1080" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 hero-overlay"></div>

        <div class="max-w-7xl mx-auto px-6 w-full relative z-10 text-center">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-white text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-6 animate-in">
                    Dashboard Monitoring Perizinan Pemanfaatan Bagian - Bagian Jalan Nasional
                </h1>
                <p class="text-blue-100 text-lg lg:text-xl mb-8 opacity-90 leading-relaxed animate-in delay-1">
                    Menyajikan informasi dan pemantauan perizinan pemanfaatan bagian-bagian jalan nasional di Provinsi Nusa Tenggara Barat secara terintegrasi, akurat, dan real-time guna mendukung pelayanan yang transparan, efektif, serta pengambilan keputusan yang tepat di lingkungan BPJN NTB.
                </p>

                <!-- Search Bar Style BPS -->
                <div
                    class="bg-white p-2 rounded-lg shadow-2xl flex flex-col md:flex-row items-center gap-2 max-w-2xl mx-auto animate-in delay-2">
                    <div class="flex-1 w-full px-4 flex items-center gap-3">
                        <i class="ph ph-magnifying-glass text-bps-gray text-xl" aria-hidden="true"></i>
                        <input type="text" id="hero-search-input" aria-label="Cari data perizinan atau pemohon" list="search-suggestions" placeholder="Cari data perizinan atau pemohon..."
                            class="w-full py-3 focus:outline-none text-gray-700 font-medium">
                        <datalist id="search-suggestions"></datalist>
                    </div>
                    <button id="hero-search-btn"
                        class="bg-bps-blue text-white px-8 py-3 rounded-md font-bold hover:bg-blue-700 transition-all w-full md:w-auto">
                        Cari Data
                    </button>
                </div>
            </div>
        </div>

        <!-- KEY INDICATORS -->
        <div class="max-w-7xl mx-auto px-6 w-full relative z-10 mt-12 mb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat Card 1 -->
                <div class="stat-card bg-white p-6 rounded-xl shadow-lg">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-blue-50 p-3 rounded-lg text-bps-blue">
                            <i class="ph ph-file-text text-2xl"></i>
                        </div>
                        <span class="text-[10px] font-bold text-green-500 bg-green-50 px-2 py-1 rounded">+12.5%</span>
                    </div>
                    <div class="text-3xl font-extrabold text-bps-navy mb-1" id="kpi-total">2.5k+</div>
                    <div class="text-xs font-bold text-bps-gray uppercase tracking-wider">Total Perizinan</div>
                </div>

                <!-- Stat Card 2 -->
                <div class="stat-card bg-white p-6 rounded-xl shadow-lg border-t-[#28A745]">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-green-50 p-3 rounded-lg text-green-600">
                            <i class="ph ph-check-circle text-2xl"></i>
                        </div>
                        <span class="text-[10px] font-bold text-blue-500 bg-blue-50 px-2 py-1 rounded">Update Hari
                            ini</span>
                    </div>
                    <div class="text-3xl font-extrabold text-bps-navy mb-1" id="kpi-aktif">1,820</div>
                    <div class="text-xs font-bold text-bps-gray uppercase tracking-wider">Izin Aktif</div>
                </div>

                <!-- Stat Card 3 -->
                <div class="stat-card bg-white p-6 rounded-xl shadow-lg border-t-[#FFC107]">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-yellow-50 p-3 rounded-lg text-yellow-600">
                            <i class="ph ph-money text-2xl"></i>
                        </div>
                        <span class="text-[10px] font-bold text-gray-500 bg-gray-50 px-2 py-1 rounded">Tahun 2026</span>
                    </div>
                    <div class="text-3xl font-extrabold text-bps-navy mb-1" id="kpi-pnbp">Rp 0 Juta</div>
                    <div class="text-xs font-bold text-bps-gray uppercase tracking-wider">Realisasi PNBP</div>
                </div>

                <!-- Stat Card 4 -->
                <div class="stat-card bg-white p-6 rounded-xl shadow-lg border-t-[#6F42C1]">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-purple-50 p-3 rounded-lg text-purple-600">
                            <i class="ph ph-map-pin text-2xl"></i>
                        </div>
                        <span
                            class="text-[10px] font-bold text-purple-500 bg-purple-50 px-2 py-1 rounded">Terpetakan</span>
                    </div>
                    <div class="text-3xl font-extrabold text-bps-navy mb-1" id="kpi-panjang">45.2 Km</div>
                    <div class="text-xs font-bold text-bps-gray uppercase tracking-wider">Jalan Dimanfaatkan</div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Auth state from server-side session
        document.addEventListener('DOMContentLoaded', () => {
            // Handle both desktop and mobile nav
            const perizinanLinks = [
                document.getElementById('nav-perizinan-link'),
                document.getElementById('mobile-nav-perizinan-link')
            ];
            const dashboardLinks = [
                document.getElementById('nav-dashboard-link'),
                document.getElementById('mobile-nav-dashboard-link')
            ];
            const navBtns = [
                document.getElementById('nav-login-btn'),
                document.getElementById('mobile-nav-login-btn')
            ];
            
            @if(auth()->check())
            navBtns.forEach(btn => {
                if (!btn) return;
                btn.innerHTML = '<i class="ph ph-sign-out text-lg"></i><span>Keluar Admin</span>';
                btn.className = 'bg-red-600 text-white px-6 py-2.5 rounded-md hover:bg-red-700 transition-all flex items-center justify-center gap-2 shadow-sm w-full lg:w-auto';
                btn.href = '{{ route("logout") }}';
                btn.onclick = null;
            });
            perizinanLinks.forEach(link => {
                if (link) link.classList.remove('hidden');
            });
            dashboardLinks.forEach(link => {
                if (link) link.classList.remove('hidden');
            });
            @else
            perizinanLinks.forEach(link => {
                if (link) link.classList.add('hidden');
            });
            dashboardLinks.forEach(link => {
                if (link) link.classList.add('hidden');
            });
            @endif

            // Mobile Menu Toggle Logic
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                    const icon = mobileMenuBtn.querySelector('i');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.classList.replace('ph-x', 'ph-list');
                    } else {
                        icon.classList.replace('ph-list', 'ph-x');
                    }
                });
            }

            // Fetch Data for KPI & Autocomplete
            const API_URL = "{{ url('/api/perizinan') }}";
            fetch(API_URL)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const data = res.data;
                        
                        // KPI Update
                        document.getElementById('kpi-total').textContent = data.length.toLocaleString('id-ID') + '+';
                        document.getElementById('kpi-aktif').textContent = data.filter(i => i.status === 'aktif').length.toLocaleString('id-ID');
                        const pnbp = data.reduce((sum, i) => sum + (parseFloat(i.pnbp) || 0), 0);
                        document.getElementById('kpi-pnbp').textContent = 'Rp ' + (pnbp / 1000000).toLocaleString('id-ID', {maximumFractionDigits: 1}) + ' Juta';

                        // Prepare Autocomplete Data
                        const allSuggestions = [];
                        const suggestionsSet = new Set();
                        data.forEach(item => {
                            if (item.nomor_izin) suggestionsSet.add(item.nomor_izin);
                            if (item.pemohon) suggestionsSet.add(item.pemohon);
                        });
                        allSuggestions.push(...([...suggestionsSet].sort()));

                        const heroSearchInput = document.getElementById('hero-search-input');
                        const datalist = document.getElementById('search-suggestions');

                        if (heroSearchInput && datalist) {
                            heroSearchInput.addEventListener('input', () => {
                                const val = heroSearchInput.value.trim();
                                datalist.innerHTML = ''; // Kosongkan dulu
                                
                                if (val.length >= 3) {
                                    // Berikan saran yang sesuai (opsional: jika diletakkan semua ke datalist, 
                                    // browser otomatis memfilter, tapi kita ingin muncul HANYA saat >= 3 huruf)
                                    allSuggestions.forEach(s => {
                                        if (s.toLowerCase().includes(val.toLowerCase())) {
                                            const option = document.createElement('option');
                                            option.value = s;
                                            datalist.appendChild(option);
                                        }
                                    });
                                }
                            });
                        }
                    }
                });

            // Hero Search Button logic
            const searchBtn = document.getElementById('hero-search-btn');
            const searchInput = document.getElementById('hero-search-input');
            
            const doSearch = () => {
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = "{{ route('peta') }}?q=" + encodeURIComponent(query);
                } else {
                    window.location.href = "{{ route('peta') }}";
                }
            };

            if (searchBtn) {
                searchBtn.addEventListener('click', doSearch);
            }

            if (searchInput) {
                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        doSearch();
                    }
                });
            }

            // Typing Effect for Search Placeholder
            const heroSearch = document.getElementById('hero-search-input');
            const suggestions = [
                "Cari data perizinan...",
                "Cari nama pemohon (PT. Telekomunikasi)...",
                "Cari nomor izin (IZN/2026/...)",
                "Cari lokasi ruas jalan (Mataram-Zodiac)..."
            ];

            let suggestionIdx = 0;
            let charIdx = 0;
            let isDeleting = false;
            let typingSpeed = 100;

            function playTyping() {
                const currentText = suggestions[suggestionIdx];
                
                if (isDeleting) {
                    heroSearch.placeholder = currentText.substring(0, charIdx--);
                    typingSpeed = 50;
                } else {
                    heroSearch.placeholder = currentText.substring(0, charIdx++);
                    typingSpeed = 100;
                }

                if (!isDeleting && charIdx > currentText.length) {
                    isDeleting = true;
                    typingSpeed = 2000; // Jeda saat teks lengkap
                } else if (isDeleting && charIdx < 0) {
                    isDeleting = false;
                    suggestionIdx = (suggestionIdx + 1) % suggestions.length;
                    charIdx = 0;
                    typingSpeed = 500;
                }

                if (document.activeElement !== heroSearch && heroSearch.value === "") {
                    setTimeout(playTyping, typingSpeed);
                } else {
                    setTimeout(() => {
                        if (document.activeElement !== heroSearch && heroSearch.value === "") {
                            playTyping();
                        }
                    }, 5000);
                }
            }

            playTyping();
        });
    </script>    <!-- Include Customer Service Widget -->
    @include('components.customer_service_widget')

</body>

</html>
