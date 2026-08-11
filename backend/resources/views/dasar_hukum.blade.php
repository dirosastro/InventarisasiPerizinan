<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>Dasar Hukum - Simpanan (Sistem Informasi Perizinan Jalan)</title>
    <meta name="description" content="Dasar hukum perizinan pemanfaatan bagian jalan nasional di Provinsi Nusa Tenggara Barat.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Resource Hints & Preconnects -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">

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
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    <script src="https://unpkg.com/@phosphor-icons/web" defer></script>

    <style>
        .nav-sticky { position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,.1); }
        .footer-bps { background-color: #002244; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .animate-in { opacity:0; animation:fadeInUp 0.6s ease-out forwards; }
        .delay-1 { animation-delay:.1s; } .delay-2 { animation-delay:.2s; } .delay-3 { animation-delay:.3s; }
        .hukum-card { transition: all .3s ease; border-left: 4px solid #007BFF; }
        .hukum-card:hover { transform: translateX(6px); box-shadow: 0 8px 30px rgba(0,51,102,.12); border-left-color: #003366; }
        .category-badge { font-size:.65rem; letter-spacing:.08em; }
        .hero-page { background: linear-gradient(135deg, #003366 0%, #005BBB 50%, #0078D4 100%); }
        .accordion-content { max-height:0; overflow:hidden; transition: max-height .4s ease, padding .3s ease; }
        .accordion-content.open { max-height:600px; }
        .accordion-icon { transition: transform .3s ease; }
        .accordion-icon.rotated { transform: rotate(180deg); }
        .tab-btn.active { background-color:#003366; color:#fff; border-color:#003366; }
        .tab-btn { transition: all .2s ease; }
        
        /* TOAST ANIMATIONS */
        @keyframes toastSlideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes toastFadeOut { from { opacity: 1; } to { opacity: 0; transform: translateY(-10px); } }
        .toast-enter { animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .toast-exit { animation: toastFadeOut 0.3s ease forwards; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">

    <!-- NAVIGATION -->
    <nav class="nav-sticky bg-white py-4 px-6 border-b border-gray-100">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Simpanan BPJN NTB" width="48" height="48" class="h-12 w-auto">
                <div class="flex flex-col leading-tight border-l pl-4 border-gray-200">
                    <span class="font-bold text-xl text-bps-navy tracking-tight uppercase">Simpanan</span>
                    <span class="text-[10px] text-bps-gray font-semibold tracking-widest uppercase">Sistem Informasi Perizinan Jalan</span>
                </div>
            </div>

            <div class="hidden lg:flex items-center space-x-8 text-sm font-semibold text-bps-navy">
                <a href="{{ route('home') }}" class="hover:text-bps-blue transition-colors">Beranda</a>
                @auth
                <a href="{{ route('dashboard') }}" class="hover:text-bps-blue transition-colors">Dashboard</a>
                @endauth
                <a href="{{ route('peta') }}" class="hover:text-bps-blue transition-colors">Peta Pemanfaatan</a>
                @auth
                <a href="{{ route('perizinan_view') }}" class="hover:text-bps-blue transition-colors">Data Perizinan</a>
                @endauth
                <a href="{{ route('dasar-hukum') }}" class="text-bps-blue border-b-2 border-bps-blue pb-0.5">Dasar Hukum</a>
                @auth
                <a href="{{ route('logout') }}" class="bg-red-600 text-white px-6 py-2.5 rounded-md hover:bg-red-700 transition-all flex items-center gap-2 shadow-sm">
                    <i class="ph ph-sign-out text-lg"></i><span>Keluar Admin</span>
                </a>
                @else
                <a href="{{ route('login') }}" class="bg-bps-blue text-white px-6 py-2.5 rounded-md hover:bg-blue-700 transition-all flex items-center gap-2 shadow-sm">
                    <i class="ph ph-user-circle text-lg"></i><span>Akses Admin</span>
                </a>
                @endauth
            </div>

            <button id="mobile-menu-btn" aria-label="Toggle navigation menu" class="lg:hidden text-bps-navy p-2 focus:outline-none">
                <i class="ph ph-list text-3xl" aria-hidden="true"></i>
            </button>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-100 flex flex-col p-6 space-y-4 font-semibold text-bps-navy">
            <a href="{{ route('home') }}" class="hover:text-bps-blue transition-colors py-2 border-b border-gray-50">Beranda</a>
            @auth
            <a href="{{ route('dashboard') }}" class="hover:text-bps-blue transition-colors py-2 border-b border-gray-50">Dashboard</a>
            @endauth
            <a href="{{ route('peta') }}" class="hover:text-bps-blue transition-colors py-2 border-b border-gray-50">Peta Pemanfaatan</a>
            @auth
            <a href="{{ route('perizinan_view') }}" class="hover:text-bps-blue transition-colors py-2 border-b border-gray-50">Data Perizinan</a>
            @endauth
            <a href="{{ route('dasar-hukum') }}" class="text-bps-blue py-2 border-b border-gray-50">Dasar Hukum</a>
            @auth
            <a href="{{ route('logout') }}" class="bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700 transition-all flex items-center justify-center gap-2">
                <i class="ph ph-sign-out text-lg"></i><span>Keluar Admin</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="bg-bps-blue text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-all flex items-center justify-center gap-2">
                <i class="ph ph-user-circle text-lg"></i><span>Akses Admin</span>
            </a>
            @endauth
        </div>
    </nav>

    <!-- HERO PAGE HEADER -->
    <section class="hero-page py-14 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center gap-3 mb-3 animate-in">
                <span class="text-blue-200 text-sm font-medium">
                    <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a> / Dasar Hukum
                </span>
            </div>
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="animate-in delay-1 flex-1">
                    <h1 class="text-white text-3xl md:text-4xl font-extrabold leading-tight mb-2">
                        Dasar Hukum Perizinan
                    </h1>
                    <p class="text-blue-200 text-base md:text-lg max-w-2xl leading-relaxed">
                        Peraturan perundang-undangan yang menjadi landasan hukum penyelenggaraan perizinan pemanfaatan bagian-bagian jalan nasional di wilayah BPJN Nusa Tenggara Barat.
                    </p>
                </div>
                <div class="flex items-center gap-4 animate-in delay-2 self-stretch md:self-auto flex-wrap">
                    @if(auth()->check() && auth()->user()->role === 'superadmin')
                    <button onclick="openAddModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-5 py-3.5 rounded-xl transition-all shadow-md flex items-center gap-2 justify-center shrink-0">
                        <i class="ph ph-plus-circle text-lg"></i>
                        <span>Tambah Regulasi</span>
                    </button>
                    @endif
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-5 py-3 shrink-0">
                        <div class="bg-white/20 p-2.5 rounded-lg text-white">
                            <i class="ph ph-scales text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-extrabold text-xl" id="total-regulasi">—</div>
                            <div class="text-blue-200 text-[10px] font-semibold uppercase tracking-wider">Total Regulasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-6 py-10">

        <!-- SEARCH & FILTER -->
        <div class="flex flex-col md:flex-row gap-4 mb-8 animate-in delay-2">
            <div class="relative flex-1">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg" aria-hidden="true"></i>
                <input type="text" id="search-hukum" aria-label="Cari dasar hukum, nomor, atau kata kunci" placeholder="Cari peraturan, nomor, atau kata kunci..."
                    class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-bps-blue/30">
            </div>
            <div class="flex gap-2 flex-wrap">
                <button class="tab-btn active px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-bps-navy shadow-sm" data-tab="semua">Semua</button>
                <button class="tab-btn px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-bps-navy shadow-sm" data-tab="uu">UU</button>
                <button class="tab-btn px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-bps-navy shadow-sm" data-tab="pp">PP</button>
                <button class="tab-btn px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-bps-navy shadow-sm" data-tab="pm">Permen</button>
                <button class="tab-btn px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-bps-navy shadow-sm" data-tab="se">SE / SK</button>
            </div>
        </div>

        <!-- REGULASI LIST -->
        <div id="regulasi-container" class="space-y-3"></div>

        <!-- EMPTY STATE -->
        <div id="empty-state" class="hidden text-center py-20">
            <i class="ph ph-file-search text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 font-semibold text-lg">Tidak ada regulasi ditemukan</p>
            <p class="text-gray-400 text-sm mt-1">Coba gunakan kata kunci yang berbeda</p>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="footer-bps text-white mt-16 py-10 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                <div class="max-w-xs">
                    <div class="flex items-center gap-3 mb-3">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo Simpanan BPJN NTB" width="40" height="40" class="h-10 w-auto">
                        <span class="font-bold text-lg">Simpanan</span>
                    </div>
                    <p class="text-blue-200 text-sm leading-relaxed">Sistem Informasi Perizinan Jalan Nasional — BPJN Nusa Tenggara Barat.</p>
                </div>
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wider mb-3 text-blue-200">Navigasi</h4>
                    <ul class="space-y-2 text-sm text-blue-100">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('peta') }}" class="hover:text-white transition-colors">Peta Pemanfaatan</a></li>
                        <li><a href="{{ route('dasar-hukum') }}" class="text-white font-semibold">Dasar Hukum</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wider mb-3 text-blue-200">Kontak</h4>
                    <ul class="space-y-2 text-sm text-blue-100">
                        <li class="flex items-center gap-2"><i class="ph ph-map-pin"></i> BPJN NTB, Mataram</li>
                        <li class="flex items-center gap-2"><i class="ph ph-envelope"></i> bpjn.ntb@pu.go.id</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-blue-900 mt-8 pt-6 text-center text-xs text-blue-300">
                &copy; {{ date('Y') }} Simpanan — BPJN Nusa Tenggara Barat. Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>

    @if(auth()->check() && auth()->user()->role === 'superadmin')
    <!-- MODAL ADD/EDIT -->
    <div id="regulasi-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[1050] hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-in">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 id="modal-title" class="font-bold text-bps-navy text-lg">Tambah Regulasi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>
            <form id="regulasi-form" class="p-6 space-y-4" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="form-id" name="id">
                <input type="hidden" id="delete-link-file" name="delete_link_file" value="false">
                <input type="hidden" id="delete-sop-file" name="delete_sop_file" value="false">
                
                <div>
                    <label class="block text-xs font-bold text-bps-gray uppercase tracking-wider mb-2">Kategori</label>
                    <select id="form-kategori" name="kategori" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bps-blue/30 bg-white">
                        <option value="uu">Undang-Undang</option>
                        <option value="pp">Peraturan Pemerintah</option>
                        <option value="pm">Peraturan Menteri</option>
                        <option value="se">Surat Edaran & Lainnya</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-bps-gray uppercase tracking-wider mb-2">Nomor Peraturan</label>
                        <input type="text" id="form-nomor" name="nomor" required placeholder="Contoh: UU No. 38 Tahun 2004" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bps-blue/30">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-bps-gray uppercase tracking-wider mb-2">Tahun</label>
                        <input type="number" id="form-tahun" name="tahun" required placeholder="Contoh: 2004" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bps-blue/30">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-bps-gray uppercase tracking-wider mb-2">Judul Peraturan</label>
                    <input type="text" id="form-judul" name="judul" required placeholder="Contoh: Jalan" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bps-blue/30">
                </div>

                <div>
                    <label class="block text-xs font-bold text-bps-gray uppercase tracking-wider mb-2">Ringkasan</label>
                    <textarea id="form-ringkasan" name="ringkasan" required rows="4" placeholder="Tulis ringkasan mengenai isi dasar hukum..." class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bps-blue/30"></textarea>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-bps-gray uppercase tracking-wider mb-1">File Dokumen Resmi (PDF)</label>
                        <div id="file-link-status" class="text-xs text-blue-700 bg-blue-50 border border-blue-100 rounded-lg p-2.5 mb-2 hidden flex items-center justify-between">
                            <span id="file-link-name" class="font-medium truncate">File terdeteksi</span>
                            <button type="button" onclick="markDeleteFile('link')" class="text-red-500 hover:text-red-700 font-bold transition-colors">Hapus</button>
                        </div>
                        <input type="file" id="form-link-file" name="link_file" accept=".pdf" class="w-full text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-bps-blue hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-bps-gray uppercase tracking-wider mb-1">File SOP (PDF)</label>
                        <div id="file-sop-status" class="text-xs text-emerald-800 bg-emerald-50 border border-emerald-100 rounded-lg p-2.5 mb-2 hidden flex items-center justify-between">
                            <span id="file-sop-name" class="font-medium truncate">File terdeteksi</span>
                            <button type="button" onclick="markDeleteFile('sop')" class="text-red-500 hover:text-red-700 font-bold transition-colors">Hapus</button>
                        </div>
                        <input type="file" id="form-sop-file" name="sop_file" accept=".pdf" class="w-full text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-bps-gray uppercase tracking-wider mb-2">Urutan Tampil (Opsional)</label>
                    <input type="number" id="form-urutan" name="urutan" placeholder="Contoh: 1" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bps-blue/30">
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-bps-navy hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" id="submit-btn" class="px-5 py-2.5 bg-bps-blue hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        <span id="submit-text">Simpan</span>
                        <i id="submit-spinner" class="ph ph-circle-notch animate-spin hidden"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- TOAST CONTAINER -->
    <div id="toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <script>
        const baseUrl = "{{ url('') }}";
        const isSuperAdmin = @json(auth()->check() && auth()->user()->role === 'superadmin');

        const badgeConfig = {
            uu: { label:'Undang-Undang', color:'bg-red-100 text-red-700' },
            pp: { label:'Peraturan Pemerintah', color:'bg-orange-100 text-orange-700' },
            pm: { label:'Peraturan Menteri', color:'bg-blue-100 text-blue-700' },
            se: { label:'SE / Keputusan', color:'bg-purple-100 text-purple-700' },
        };

        let regulasiData = [];
        let activeTab = 'semua';
        let searchQuery = '';

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const isError = type === 'error';
            const bgColor = isError ? 'bg-white' : 'bg-white';
            const borderColor = isError ? 'border-red-500' : 'border-emerald-500';
            const iconColor = isError ? 'text-red-500' : 'text-emerald-500';
            const iconName = isError ? 'ph-warning-circle' : 'ph-check-circle';

            toast.className = `toast-enter pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-lg border-l-4 ${borderColor} ${bgColor} min-w-[300px] max-w-sm`;
            
            toast.innerHTML = `
                <i class="ph ${iconName} text-2xl ${iconColor} shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 text-sm mb-0.5">${isError ? 'Gagal' : 'Berhasil'}</h4>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <i class="ph ph-x text-lg"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.classList.remove('toast-enter');
                    toast.classList.add('toast-exit');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        }

        function loadData() {
            fetch(`${baseUrl}/api/dasar-hukum`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        regulasiData = res.data;
                        renderRegulasi();
                    }
                })
                .catch(err => console.error("Error fetching data:", err));
        }

        function renderRegulasi() {
            const container = document.getElementById('regulasi-container');
            const emptyState = document.getElementById('empty-state');
            const filtered = regulasiData.filter(r => {
                const matchTab = activeTab === 'semua' || r.kategori === activeTab;
                const q = searchQuery.toLowerCase();
                const matchSearch = !q || r.nomor.toLowerCase().includes(q) || r.judul.toLowerCase().includes(q) || r.ringkasan.toLowerCase().includes(q);
                return matchTab && matchSearch;
            });

            document.getElementById('total-regulasi').textContent = regulasiData.length;

            if (filtered.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }
            emptyState.classList.add('hidden');

            container.innerHTML = filtered.map((r, idx) => {
                const badge = badgeConfig[r.kategori];
                return `
                <div class="hukum-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 cursor-pointer" onclick="toggleAccordion(${idx})" style="animation:fadeInUp 0.5s ease-out ${idx*0.05}s both">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-2">
                                <span class="category-badge font-bold uppercase tracking-wider px-2.5 py-1 rounded-full ${badge.color}">${badge.label}</span>
                                <span class="text-xs text-bps-gray font-medium">${r.tahun}</span>
                            </div>
                            <h3 class="font-bold text-bps-navy text-base leading-snug">${r.nomor}</h3>
                            <p class="text-bps-gray text-sm mt-0.5 font-medium">${r.judul}</p>
                        </div>
                        <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                            ${isSuperAdmin ? `
                                <button onclick="openEditModal(${r.id})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                                <button onclick="confirmDelete(${r.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                            ` : ''}
                            <i class="ph ph-caret-down accordion-icon text-bps-gray text-xl shrink-0 mt-1" id="icon-${idx}"></i>
                        </div>
                    </div>
                    <div class="accordion-content" id="acc-${idx}">
                        <div class="pt-4 border-t border-gray-100 mt-4">
                            <p class="text-gray-600 text-sm leading-relaxed">${r.ringkasan}</p>
                            <div class="flex flex-wrap items-center gap-3 mt-4" onclick="event.stopPropagation()">
                                ${r.link_file_id
                                    ? `<a href="${r.link_file_url}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-bps-blue hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-all shadow-sm">
                                        <i class="ph ph-arrow-square-out text-base"></i> Lihat
                                       </a>`
                                    : ''
                                }
                                ${r.sop_file_id
                                    ? `<a href="${r.sop_file_url}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-all shadow-sm">
                                        <i class="ph ph-file-doc text-base"></i> SOP
                                       </a>`
                                    : `<button disabled
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-400 text-sm font-semibold rounded-lg cursor-not-allowed">
                                        <i class="ph ph-file-doc text-base"></i> SOP Belum Tersedia
                                       </button>`
                                }
                            </div>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        function toggleAccordion(idx) {
            const content = document.getElementById(`acc-${idx}`);
            const icon = document.getElementById(`icon-${idx}`);
            if (!content) return;
            const isOpen = content.classList.contains('open');
            // Tutup semua
            document.querySelectorAll('.accordion-content').forEach(el => el.classList.remove('open'));
            document.querySelectorAll('.accordion-icon').forEach(el => el.classList.remove('rotated'));
            // Toggle yang diklik
            if (!isOpen) {
                content.classList.add('open');
                icon.classList.add('rotated');
            }
        }

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeTab = btn.dataset.tab;
                renderRegulasi();
            });
        });

        document.getElementById('search-hukum').addEventListener('input', e => {
            searchQuery = e.target.value;
            renderRegulasi();
        });

        document.getElementById('mobile-menu-btn').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // ─── SUPERADMIN JS LOGIC ──────────────────────────────────────────────────
        @if(auth()->check() && auth()->user()->role === 'superadmin')
        const modal = document.getElementById('regulasi-modal');
        const form = document.getElementById('regulasi-form');
        const modalTitle = document.getElementById('modal-title');
        const formId = document.getElementById('form-id');
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const submitSpinner = document.getElementById('submit-spinner');

        function openAddModal() {
            modalTitle.textContent = "Tambah Regulasi";
            form.reset();
            formId.value = "";
            
            document.getElementById('delete-link-file').value = "false";
            document.getElementById('delete-sop-file').value = "false";
            document.getElementById('file-link-status').classList.add('hidden');
            document.getElementById('file-sop-status').classList.add('hidden');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openEditModal(id) {
            const item = regulasiData.find(r => r.id === id);
            if (!item) return;

            modalTitle.textContent = "Edit Regulasi";
            form.reset();
            formId.value = item.id;
            document.getElementById('form-kategori').value = item.kategori;
            document.getElementById('form-nomor').value = item.nomor;
            document.getElementById('form-judul').value = item.judul;
            document.getElementById('form-ringkasan').value = item.ringkasan;
            document.getElementById('form-tahun').value = item.tahun;
            document.getElementById('form-urutan').value = item.urutan;

            document.getElementById('delete-link-file').value = "false";
            document.getElementById('delete-sop-file').value = "false";

            const linkStatus = document.getElementById('file-link-status');
            if (item.link_file_id) {
                document.getElementById('file-link-name').textContent = "File dokumen resmi aktif";
                linkStatus.classList.remove('hidden');
            } else {
                linkStatus.classList.add('hidden');
            }

            const sopStatus = document.getElementById('file-sop-status');
            if (item.sop_file_id) {
                document.getElementById('file-sop-name').textContent = "File SOP aktif";
                sopStatus.classList.remove('hidden');
            } else {
                sopStatus.classList.add('hidden');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function markDeleteFile(type) {
            if (type === 'link') {
                document.getElementById('delete-link-file').value = "true";
                document.getElementById('file-link-status').classList.add('hidden');
            } else if (type === 'sop') {
                document.getElementById('delete-sop-file').value = "true";
                document.getElementById('file-sop-status').classList.add('hidden');
            }
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            
            submitBtn.disabled = true;
            submitText.textContent = "Mengunggah...";
            submitSpinner.classList.remove('hidden');

            const id = formId.value;
            const formData = new FormData(form);
            const url = id ? `${baseUrl}/api/dasar-hukum/${id}` : `${baseUrl}/api/dasar-hukum`;

            // Custom headers for CSRF and JSON response
            const headers = new Headers();
            headers.append('Accept', 'application/json');
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                headers.append('X-CSRF-TOKEN', token.content);
            }

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: headers
            })
            .then(async res => {
                if (!res.ok) {
                    let errorMessage = "Gagal menyimpan data (Status " + res.status + ").";
                    try {
                        const data = await res.json();
                        if (res.status === 422) {
                            const errors = Object.values(data.errors || {}).flat().join('\n');
                            errorMessage = "Validasi gagal:\n" + errors;
                        } else if (data.message) {
                            errorMessage = data.message;
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e);
                    }
                    throw new Error(errorMessage);
                }
                return res.json();
            })
            .then(res => {
                if (res.success) {
                    showToast(res.message || "Data berhasil disimpan!", 'success');
                    closeModal();
                    loadData();
                } else {
                    showToast("Gagal menyimpan data: " + res.message, 'error');
                }
            })
            .catch(err => {
                console.error("Error submitting form:", err);
                showToast(err.message || "Terjadi kesalahan koneksi atau upload. Silakan coba lagi.", 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitText.textContent = "Simpan";
                submitSpinner.classList.add('hidden');
            });
        }

        function confirmDelete(id) {
            if (confirm("Apakah Anda yakin ingin menghapus regulasi dasar hukum ini? File-file terkait di Google Drive juga akan dihapus.")) {
                fetch(`${baseUrl}/api/dasar-hukum/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    }
                })
                .then(async res => {
                    if (!res.ok) {
                        let msg = "Gagal menghapus data";
                        try {
                            const data = await res.json();
                            msg = data.message || msg;
                        } catch(e) {}
                        throw new Error(msg);
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.success) {
                        showToast(res.message || "Data berhasil dihapus!", 'success');
                        loadData();
                    } else {
                        showToast("Gagal menghapus data: " + res.message, 'error');
                    }
                })
                .catch(err => {
                    console.error("Error deleting:", err);
                    showToast(err.message || "Terjadi kesalahan koneksi saat menghapus data.", 'error');
                });
            }
        }
        @endif

        // Init load
        loadData();
    </script>
</body>
</html>
