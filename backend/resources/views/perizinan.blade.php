<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>Daftar Perizinan | Siperjalan BPJN NTB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- xlsx-js-style for Styled Excel Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>

    <!-- jsPDF & jsPDF-AutoTable for PDF Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .text-primary { color: #0066FF; }
        .bg-accent { background-color: #0066FF; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.4s ease-out forwards; }
    </style>
</head>
<body class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col fixed h-full z-20">
        <div class="p-6 border-b border-gray-50 flex items-center gap-3">
            <div class="w-10 h-10 bg-accent rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                <i class="ph-fill ph-road-horizon text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-gray-900 tracking-tight leading-none">Siperjalan</h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">BPJN NTB</p>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1 mt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl transition-all group">
                <i class="ph ph-squares-four text-xl group-hover:text-accent"></i>
                <span class="font-semibold text-sm">Dashboard</span>
            </a>
            <a href="{{ route('peta') }}" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl transition-all group">
                <i class="ph ph-map-trifold text-xl group-hover:text-accent"></i>
                <span class="font-semibold text-sm">Peta Pemanfaatan</span>
            </a>
            <a href="{{ route('perizinan_view') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-accent rounded-xl transition-all group">
                <i class="ph-fill ph-file-text text-xl"></i>
                <span class="font-bold text-sm">Daftar Perizinan</span>
            </a>
            <div id="admin-menu" class="hidden">
                <a href="{{ route('users') }}" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl transition-all group">
                    <i class="ph ph-users-three text-xl group-hover:text-accent"></i>
                    <span class="font-semibold text-sm">Manajemen User</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-gray-50">
            <div class="bg-gray-50 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-accent">
                    <i class="ph ph-user text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-900 truncate user-name-display">Admin BPJN</p>
                    <p class="text-[10px] text-gray-500 truncate">Administrator</p>
                </div>
                <button onclick="logout()" class="text-gray-400 hover:text-red-500"><i class="ph ph-sign-out"></i></button>
            </div>
        </div>
    </aside>

    <script>
        function logout() {
            localStorage.removeItem('isLoggedIn');
            localStorage.removeItem('userRole');
            window.location.href = "{{ route('home') }}";
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
            const userRole = localStorage.getItem('userRole');
            const adminMenu = document.getElementById('admin-menu');

            if (!isLoggedIn) {
                window.location.href = "{{ route('login') }}";
                return;
            }

            if (isLoggedIn && userRole === 'superadmin' && adminMenu) {
                adminMenu.classList.remove('hidden');
            }

            // Tampilkan nama user yang login
            const userName = localStorage.getItem('userName') || 'Admin BPJN';
            const nameElements = document.querySelectorAll('.user-name-display');
            nameElements.forEach(el => el.innerText = userName);
        });
    </script>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Daftar Perizinan Masuk</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola data permohonan izin pemanfaatan bagian-bagian jalan.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('test-wa') }}" class="bg-green-50 text-green-700 border border-green-200 px-4 py-2 text-sm font-medium rounded-lg shadow-sm hover:bg-green-100 flex items-center gap-2 transition-colors">
                        <i class="ph ph-whatsapp-logo"></i> Test Bot
                    </a>
                    <button id="export-btn" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-50 flex items-center gap-2 transition-colors">
                        <i class="ph ph-export"></i> Export
                    </button>
                    <a href="{{ route('admin') }}" class="bg-accent text-white px-4 py-2 text-sm font-medium rounded-lg shadow-sm hover:bg-blue-700 flex items-center gap-2 transition-colors">
                        <i class="ph ph-plus-circle"></i> Tambah Izin Baru
                    </a>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col lg:flex-row gap-4 justify-between items-center">
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <div class="relative min-w-[250px] flex-1 lg:flex-none">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="search-input" list="ruas-list" placeholder="Cari nama pemohon, nomor izin..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        <datalist id="ruas-list"></datalist>
                    </div>
                    <select id="filter-jenis" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent min-w-[150px]">
                        <option value="">Semua Jenis Izin</option>
                        <option value="Izin Penempatan Jaringan Utilitas">Jaringan Utilitas (Kabel/Pipa)</option>
                        <option value="Izin Penempatan Iklan/Reklame">Iklan & Reklame</option>
                        <option value="Akses Jalan Keluar/Masuk">Akses Jalan</option>
                        <option value="dispensasi">Dispensasi</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 w-full lg:w-auto justify-end">
                    <span class="text-xs text-gray-500">Urutkan:</span>
                    <select id="sort-order" class="px-3 py-2 text-sm border-none bg-transparent font-medium text-gray-700 focus:outline-none focus:ring-0 cursor-pointer hover:bg-gray-50 rounded-lg">
                        <option value="newest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                    </select>

                    <div class="w-[1px] h-4 bg-gray-200 mx-1"></div>

                    <span class="text-xs text-gray-500">Tampilkan:</span>
                    <select id="items-per-page" class="px-3 py-2 text-sm border-none bg-transparent font-medium text-gray-700 focus:outline-none focus:ring-0 cursor-pointer hover:bg-gray-50 rounded-lg">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-4 font-semibold w-[5%] text-center">No</th>
                                <th class="px-5 py-4 font-semibold w-[15%]">Nomor Izin & Tgl</th>
                                <th class="px-5 py-4 font-semibold w-[20%]">Pemohon & Jenis Izin</th>
                                <th class="px-5 py-4 font-semibold w-[20%]">Ruas Jalan / Lokasi</th>
                                <th class="px-5 py-4 font-semibold w-[12%] text-right">Total PNBP</th>
                                <th class="px-5 py-4 font-semibold w-[10%]">Kontak</th>
                                <th class="px-5 py-4 font-semibold w-[12%] text-center">Status</th>
                                <th class="px-5 py-4 font-semibold w-[6%] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-perizinan" class="divide-y divide-gray-100">
                            <!-- Data akan dimuat secara dinamis via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                    <div class="text-xs text-gray-500">
                        Menampilkan <span class="font-medium text-gray-700">1</span> sampai <span class="font-medium text-gray-700">0</span> dari <span class="font-medium text-gray-700">0</span> data
                    </div>
                    <div class="flex gap-1">
                        <!-- Pagination buttons will be generated by JS -->
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- WA NOTIF CONFIRM MODAL -->
    <div id="wa-confirm-modal" class="fixed inset-0 z-[70] flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden animate-fade-in-up">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-500 px-6 py-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="ph-fill ph-whatsapp-logo text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg">Kirim Notifikasi WhatsApp</h3>
                    <p class="text-green-100 text-xs">Konfirmasi sebelum mengirim pesan ke pemohon</p>
                </div>
                <button id="wa-cancel-btn" class="ml-auto text-white/70 hover:text-white transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>

            <!-- Info Penerima -->
            <div class="px-6 pt-5 pb-3">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Pemohon</p>
                        <p id="wa-modal-pemohon" class="text-sm font-bold text-gray-800 truncate">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">No. HP</p>
                        <p id="wa-modal-no-hp" class="text-sm font-bold text-green-700">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Nomor Izin</p>
                        <p id="wa-modal-no-izin" class="text-sm font-medium text-gray-700 truncate">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Tgl Berakhir</p>
                        <p id="wa-modal-tgl-akhir" class="text-sm font-medium text-gray-700 truncate">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Sisa Waktu</p>
                        <p id="wa-modal-sisa" class="text-sm font-bold text-blue-600">-</p>
                    </div>
                </div>

                <!-- Preview Pesan -->
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gray-100 px-4 py-2 flex items-center gap-2 border-b border-gray-200">
                        <i class="ph ph-chat-text text-gray-500 text-sm"></i>
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Preview Pesan</span>
                    </div>
                    <div class="bg-[#ECE5DD] p-4 max-h-48 overflow-y-auto custom-scrollbar">
                        <div class="bg-white rounded-lg rounded-tl-none px-4 py-3 shadow-sm max-w-[90%]">
                            <pre id="wa-modal-preview" class="text-xs text-gray-700 whitespace-pre-wrap font-sans leading-relaxed"></pre>
                            <p class="text-[9px] text-gray-400 text-right mt-2 font-medium">Bot Siperjalan ✓✓</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3 justify-end">
                <button id="wa-cancel-btn-bottom" onclick="document.getElementById('wa-confirm-modal').classList.add('hidden')"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white hover:bg-gray-100 border border-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button id="wa-send-btn"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-green-600 hover:bg-green-700 rounded-xl shadow-lg shadow-green-200 transition-all flex items-center gap-2">
                    <i class="ph ph-paper-plane-tilt"></i> Kirim Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 transform transition-all animate-fade-in-up">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ph-fill ph-trash text-3xl"></i>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Hapus Perizinan?</h3>
            <p class="text-gray-500 text-center mb-8">Data ini akan dihapus permanen dari sistem dan tidak dapat dikembalikan. Apakah Anda yakin?</p>
            
            <div class="flex gap-3">
                <button id="cancel-delete" class="flex-1 py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                    Batal
                </button>
                <button id="confirm-delete" class="flex-1 py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg shadow-red-200 transition-all">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- EXPORT MODAL -->
    <div id="export-modal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500/50 backdrop-blur-sm"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-8 py-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Opsi Ekspor Data</h3>
                    <button id="close-export-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="ph ph-x text-xl"></i>
                    </button>
                </div>
                <div class="p-8 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tahun Terbit</label>
                        <select id="export-tahun" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent bg-gray-50 font-medium">
                            <option value="all">Semua Tahun</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jenis Perizinan</label>
                        <select id="export-jenis" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent bg-gray-50 font-medium">
                            <option value="all">Semua Jenis</option>
                            <option value="Izin Penempatan Jaringan Utilitas">Jaringan Utilitas (Kabel/Pipa)</option>
                            <option value="Izin Penempatan Iklan/Reklame">Iklan & Reklame</option>
                            <option value="Akses Jalan Keluar/Masuk">Akses Jalan</option>
                            <option value="dispensasi">Dispensasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Ruas Jalan</label>
                        <select id="export-ruas" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent bg-gray-50 font-medium">
                            <option value="all">Semua Ruas Jalan</option>
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-5 flex justify-end gap-3">
                    <button id="cancel-export" class="px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                    <button id="confirm-export-pdf" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-red-100 transition-all flex items-center gap-2">
                        <i class="ph ph-file-pdf text-lg"></i> Download PDF
                    </button>
                    <button id="confirm-export" class="px-8 py-3 bg-accent hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-100 transition-all flex items-center gap-2">
                        <i class="ph ph-file-xls text-lg"></i> Download XLSX
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.API_BASE_URL = "{{ url('/') }}";
        const API_URL = (window.API_BASE_URL || '') + '/api/perizinan';
        let allPerizinanData = [];
        let filteredPerizinanData = [];
        let allRuasNames = [];
        
        // Pagination State
        let currentPage = 1;
        let itemsPerPage = 10;
        
        async function fetchRuasJalan() {
            try {
                const response = await fetch((window.API_BASE_URL || '') + '/api/ruas-jalan');
                const result = await response.json();
                if (result.success) {
                    allRuasNames = [...new Set(result.data.map(r => r.nama_ruas))].sort();
                }
            } catch (error) {
                console.error('Error fetching ruas jalan:', error);
            }
        }

        async function fetchPerizinan() {
            const tbody = document.getElementById('tabel-perizinan');
            
            try {
                const response = await fetch(API_URL);
                const result = await response.json();
                console.log("API Result:", result);

                if (result.success) {
                    allPerizinanData = result.data;
                    filteredPerizinanData = [...allPerizinanData]; 
                    currentPage = 1;
                    renderTable();
                } else {
                    console.error("Gagal mengambil data: ", result.message);
                    tbody.innerHTML = `<tr><td colspan="8" class="px-5 py-4 text-center text-red-500">Gagal memuat data dari server.</td></tr>`;
                }

            } catch (error) {
                console.error('Error saat fetch API:', error);
                tbody.innerHTML = `<tr><td colspan="8" class="px-5 py-4 text-center text-red-500">Terjadi kesalahan jaringan.</td></tr>`;
            }
        }

        function renderTable() {
            const tbody = document.getElementById('tabel-perizinan');
            tbody.innerHTML = '';

            const data = filteredPerizinanData;
            
            window._permitMap = {};
            allPerizinanData.forEach(p => { window._permitMap[p.id] = p; });
            
            if(data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="px-5 py-4 text-center text-gray-500">Tidak ada data.</td></tr>`;
                updatePaginationInfo(0, 0, 0);
                renderPagination(0);
                return;
            }

            const totalItems = data.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
            const paginatedData = data.slice(startIndex, endIndex);

            updatePaginationInfo(startIndex + 1, endIndex, totalItems);
            renderPagination(totalPages);

            paginatedData.forEach((item, index) => {
                const tglTerbit = new Date(item.tanggal_terbit).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                
                let statusBadge = '';
                if (item.status === 'aktif') {
                    statusBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold uppercase rounded-full bg-green-100 text-green-700 border border-green-200"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif</span>`;
                } else if (item.status === 'hampir_habis') {
                    statusBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold uppercase rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Akan Jatuh Tempo</span>`;
                } else {
                    statusBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold uppercase rounded-full bg-red-100 text-red-700 border border-red-200"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Kadaluarsa</span>`;
                }

                const formatRupiah = (number) => {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(number);
                };

                // Build Lokasi HTML
                let lokasiHtml = '-';
                if (item.lokasi && item.lokasi.length > 0) {
                    lokasiHtml = '<div class="flex flex-col gap-1.5">';
                    item.lokasi.forEach(l => {
                        // LOGIKA DISAMAKAN DENGAN PETA (app.js)
                        // 1. Coba ambil dari GeoJSON jika ada (Prioritas Utama)
                        let lat, lng;
                        let foundInGeo = false;

                        if (item.geojson) {
                            try {
                                const geo = typeof item.geojson === 'string' ? JSON.parse(item.geojson) : item.geojson;
                                const firstFeature = geo.features ? geo.features[0] : geo;
                                if (firstFeature && firstFeature.geometry) {
                                    const coords = firstFeature.geometry.coordinates;
                                    // GeoJSON format [lng, lat]
                                    lng = coords[0];
                                    lat = coords[1];
                                    foundInGeo = true;
                                }
                            } catch (e) { console.warn("GeoJSON parse error", e); }
                        }

                        // 2. Jika tidak ada di GeoJSON, coba parse dari sta_awal (Fallback)
                        if (!foundInGeo) {
                            const rawCoords = l.sta_awal ? l.sta_awal.replace(/[^\d\.\-\, ]/g, ' ').split(/[\s,]+/).filter(p => p.length > 0) : null;
                            if (rawCoords && rawCoords.length >= 2) {
                                let val1 = parseFloat(rawCoords[0]);
                                let val2 = parseFloat(rawCoords[1]);
                                if (!isNaN(val1) && !isNaN(val2)) {
                                    // Deteksi Lintang vs Bujur (NTB: Lat ~ -8, Lng ~ 116)
                                    if (Math.abs(val1) < 20 && Math.abs(val2) > 90) {
                                        lat = val1; lng = val2;
                                    } else if (Math.abs(val2) < 20 && Math.abs(val1) > 90) {
                                        lat = val2; lng = val1;
                                    } else {
                                        lat = val1; lng = val2;
                                    }
                                    foundInGeo = true;
                                }
                            }
                        }

                        let svUrl = foundInGeo ? `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${lat},${lng}` : null;
                        
                        lokasiHtml += `
                            <div class="flex items-center gap-1">
                                ${svUrl ? `
                                <a href="${svUrl}" target="_blank" class="bg-amber-500 text-white px-2.5 py-1 rounded-md text-[11px] font-bold inline-flex items-center gap-1.5 hover:bg-amber-600 transition-colors shadow-sm" title="Buka Street View">
                                    <i class="ph-fill ph-person-simple-walk"></i> ${l.nama_ruas_jalan}
                                </a>` : `
                                <span class="bg-gray-100 text-gray-400 px-2.5 py-1 rounded-md text-[11px] font-semibold inline-flex items-center gap-1.5">
                                    <i class="ph-fill ph-warning"></i> ${l.nama_ruas_jalan}
                                </a>`}
                                <a href="{{ url('/peta') }}?id=${item.id}&road=${encodeURIComponent(l.nama_ruas_jalan)}" class="bg-blue-50 border border-blue-100 text-blue-600 p-1 rounded-md hover:bg-blue-100 transition-colors" title="Lihat di Peta">
                                    <i class="ph-fill ph-map-pin text-sm"></i>
                                </a>
                            </div>
                        `;
                    });
                    lokasiHtml += '</div>';
                }

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition-colors group';
                
                tr.innerHTML = `
                    <td class="px-5 py-4 text-center text-gray-500">${startIndex + index + 1}</td>
                    <td class="px-5 py-4">
                        <div class="font-medium text-gray-800">${item.nomor_izin}</div>
                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <i class="ph ph-calendar-blank"></i> ${tglTerbit}
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-semibold text-primary">${item.pemohon}</div>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span class="text-[10px] text-gray-600 bg-gray-100 px-2 py-0.5 rounded uppercase font-medium">Izin ${(item.sub_jenis && item.sub_jenis !== '-') ? item.sub_jenis : item.jenis_izin}</span>
                            <span class="text-[10px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded uppercase font-bold border border-blue-100">${item.satker ? item.satker.nama_satker : '-'}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-medium text-gray-700">${lokasiHtml}</div>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="font-medium text-gray-800">${formatRupiah(item.pnbp || 0)}</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex flex-col gap-1.5">
                            <div class="text-xs font-bold text-gray-700">${item.no_hp || '-'}</div>
                            ${item.no_hp ? `
                            <div class="flex items-center gap-1.5">
                                <a href="https://wa.me/${item.no_hp.replace(/[^\d]/g, '').replace(/^0/, '62')}" target="_blank"
                                    class="inline-flex items-center gap-1.5 text-[10px] font-bold text-white bg-green-500 hover:bg-green-600 px-3 py-1.5 rounded-lg shadow-sm shadow-green-100 transition-all">
                                    <i class="ph-fill ph-whatsapp-logo text-sm"></i> CHAT
                                </a>
                                <button onclick="kirimNotifWA(${item.id})"
                                    class="inline-flex items-center gap-1.5 text-[10px] font-bold text-white bg-blue-500 hover:bg-blue-600 px-3 py-1.5 rounded-lg shadow-sm shadow-blue-100 transition-all">
                                    <i class="ph-fill ph-paper-plane-tilt text-sm"></i> NOTIF
                                </button>
                            </div>` : ''}
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center">
                        ${statusBadge}
                    </td>
                    <td class="px-5 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ url('/admin') }}?id=${item.id}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md transition-colors" title="Ubah Data">
                                <i class="ph ph-pencil-simple text-lg"></i>
                            </a>
                            <button onclick="hapusPerizinan(${item.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Hapus Data">
                                <i class="ph ph-trash text-lg"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function updatePaginationInfo(start, end, total) {
            const infoArea = document.querySelector('.text-xs.text-gray-500');
            if (infoArea) {
                infoArea.innerHTML = `Menampilkan <span class="font-medium text-gray-700">${start}</span> sampai <span class="font-medium text-gray-700">${end}</span> dari <span class="font-medium text-gray-700">${total}</span> data`;
            }
        }

        function renderPagination(totalPages) {
            const paginationContainer = document.querySelector('.flex.gap-1');
            if (!paginationContainer) return;

            paginationContainer.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = `px-3 py-1.5 border border-gray-200 bg-white text-gray-600 rounded hover:bg-gray-50 disabled:opacity-50 transition-colors ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}`;
            prevBtn.disabled = currentPage === 1;
            prevBtn.innerHTML = '<i class="ph ph-caret-left"></i>';
            prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; renderTable(); } };
            paginationContainer.appendChild(prevBtn);

            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            if (endPage - startPage + 1 < maxVisiblePages) startPage = Math.max(1, endPage - maxVisiblePages + 1);

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = i === currentPage 
                    ? "px-3 py-1.5 border-none bg-accent text-white rounded font-medium text-sm shadow-sm"
                    : "px-3 py-1.5 border border-gray-200 bg-white text-gray-600 rounded hover:bg-gray-50 transition-colors text-sm";
                pageBtn.innerText = i;
                pageBtn.onclick = () => { currentPage = i; renderTable(); };
                paginationContainer.appendChild(pageBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = `px-3 py-1.5 border border-gray-200 bg-white text-gray-600 rounded hover:bg-gray-50 disabled:opacity-50 transition-colors ${currentPage === totalPages || totalPages === 0 ? 'opacity-50 cursor-not-allowed' : ''}`;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
            nextBtn.innerHTML = '<i class="ph ph-caret-right"></i>';
            nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; renderTable(); } };
            paginationContainer.appendChild(nextBtn);
        }

        function filterData() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase().trim();
            const jenisValue = document.getElementById('filter-jenis').value;

            filteredPerizinanData = allPerizinanData.filter(item => {
                let matchJenis = true;
                if (jenisValue !== '') {
                    if (jenisValue === 'dispensasi') matchJenis = item.jenis_izin === 'dispensasi';
                    else matchJenis = item.sub_jenis === jenisValue;
                }
                let matchSearch = true;
                if (searchTerm !== '') {
                    const pemohon = (item.pemohon || '').toLowerCase();
                    const noIzin = (item.nomor_izin || '').toLowerCase();
                    const ruasJalan = item.lokasi ? item.lokasi.map(l => l.nama_ruas_jalan).join(' ').toLowerCase() : '';
                    matchSearch = pemohon.includes(searchTerm) || noIzin.includes(searchTerm) || ruasJalan.includes(searchTerm);
                }
                return matchJenis && matchSearch;
            });

            const sortOrder = document.getElementById('sort-order').value;
            filteredPerizinanData.sort((a, b) => {
                const dateA = new Date(a.tanggal_terbit);
                const dateB = new Date(b.tanggal_terbit);
                return sortOrder === 'newest' ? dateB - dateA : dateA - dateB;
            });

            currentPage = 1;
            renderTable();
        }

        document.getElementById('sort-order').addEventListener('change', filterData);
        document.getElementById('search-input').addEventListener('input', filterData);
        document.getElementById('filter-jenis').addEventListener('change', filterData);
        document.getElementById('items-per-page').addEventListener('change', (e) => {
            itemsPerPage = parseInt(e.target.value);
            currentPage = 1;
            renderTable();
        });

        // WhatsApp Notif Logic
        const WA_BOT_URL = "{{ url('api/wa-test') }}";
        let pendingNotifItem = null;

        function showToast(message, type = 'success') {
            const existing = document.getElementById('wa-toast');
            if (existing) existing.remove();
            const toast = document.createElement('div');
            toast.id = 'wa-toast';
            toast.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-2xl text-white text-sm font-semibold ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'} transition-all duration-300 opacity-0`;
            toast.innerHTML = `<span>${message}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.style.opacity = '1', 10);
            if (type !== 'loading') setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 4000);
            return toast;
        }

        function formatTglID(dateStr) {
            if (!dateStr) return '-';
            const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const d = new Date(dateStr);
            return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
        }

        function hitungSisaHari(dateStr) {
            if (!dateStr) return null;
            const akhir = new Date(dateStr);
            const sekarang = new Date();
            sekarang.setHours(0,0,0,0);
            return Math.ceil((akhir - sekarang) / (1000 * 60 * 60 * 24));
        }

        function buildPesanNotif(item) {
            const jenis      = (item.sub_jenis && item.sub_jenis !== '-') ? item.sub_jenis : item.jenis_izin;
            const tglFormat  = formatTglID(item.tanggal_akhir);
            const ruasJalan  = item.lokasi ? item.lokasi.map(l => l.nama_ruas_jalan).filter(Boolean).join(', ') : '-';
            const sisaHari   = hitungSisaHari(item.tanggal_akhir);

            return `📢 *PEMBERITAHUAN MASA BERLAKU IZIN*\n\nYth. *${item.pemohon}*,\n\nIzin pemanfaatan jalan Anda akan berakhir dalam *${sisaHari} hari* (pada tanggal *${tglFormat}*).\n\n📌 *Nomor Izin:* ${item.nomor_izin}\n📌 *Jenis:* ${jenis}\n📌 *Ruas Jalan:* ${ruasJalan}\n\nTerima kasih.\n\n_Sistem Siperjalan BPJN NTB_`;
        }

        function kirimNotifWA(permitId) {
            const item = window._permitMap ? window._permitMap[permitId] : null;
            if (!item) return;
            pendingNotifItem = item;
            document.getElementById('wa-modal-pemohon').textContent  = item.pemohon;
            document.getElementById('wa-modal-no-hp').textContent    = item.no_hp;
            document.getElementById('wa-modal-no-izin').textContent  = item.nomor_izin;
            document.getElementById('wa-modal-tgl-akhir').textContent = formatTglID(item.tanggal_akhir);
            document.getElementById('wa-modal-sisa').textContent = `${hitungSisaHari(item.tanggal_akhir)} hari lagi`;
            document.getElementById('wa-modal-preview').textContent = buildPesanNotif(item);
            document.getElementById('wa-confirm-modal').classList.remove('hidden');
        }

        document.getElementById('wa-send-btn').addEventListener('click', async () => {
            if (!pendingNotifItem) return;
            const btn = document.getElementById('wa-send-btn');
            btn.disabled = true;
            document.getElementById('wa-confirm-modal').classList.add('hidden');
            const toast = showToast('Mengirim...', 'loading');
            try {
                const response = await fetch(WA_BOT_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ number: pendingNotifItem.no_hp, message: buildPesanNotif(pendingNotifItem) })
                });
                toast.remove();
                if (response.ok) showToast('Berhasil dikirim');
                else showToast('Gagal mengirim', 'error');
            } catch (err) { toast.remove(); showToast('Error jaringan', 'error'); }
            finally { btn.disabled = false; pendingNotifItem = null; }
        });

        // Delete Logic
        let permitIdToDelete = null;
        function hapusPerizinan(id) { permitIdToDelete = id; document.getElementById('delete-modal').classList.remove('hidden'); }
        document.getElementById('cancel-delete').addEventListener('click', () => { document.getElementById('delete-modal').classList.add('hidden'); });
        document.getElementById('confirm-delete').addEventListener('click', async () => {
            if (!permitIdToDelete) return;
            try {
                const response = await fetch(`${API_URL}/${permitIdToDelete}`, { method: 'DELETE', headers: { 'Accept': 'application/json' } });
                if (response.ok) { document.getElementById('delete-modal').classList.add('hidden'); fetchPerizinan(); }
            } catch (error) { console.error(error); }
            finally { permitIdToDelete = null; }
        });

        // Export Modal
        const exportModal = document.getElementById('export-modal');
        document.getElementById('export-btn').addEventListener('click', () => {
            if (allPerizinanData.length === 0) return;
            const tahunSelect = document.getElementById('export-tahun');
            const ruasSelect = document.getElementById('export-ruas');
            const tahunSet = new Set();
            const ruasSet = new Set();
            allPerizinanData.forEach(item => {
                if (item.tanggal_terbit) tahunSet.add(item.tanggal_terbit.split('-')[0]);
                if (item.lokasi) item.lokasi.forEach(loc => ruasSet.add(loc.nama_ruas_jalan));
            });
            tahunSelect.innerHTML = '<option value="all">Semua Tahun</option>';
            [...tahunSet].sort((a,b) => b-a).forEach(y => tahunSelect.innerHTML += `<option value="${y}">${y}</option>`);
            ruasSelect.innerHTML = '<option value="all">Semua Ruas Jalan</option>';
            [...ruasSet].sort().forEach(r => ruasSelect.innerHTML += `<option value="${r}">${r}</option>`);
            exportModal.classList.remove('hidden');
        });

        [document.getElementById('close-export-modal'), document.getElementById('cancel-export')].forEach(el => {
            el.addEventListener('click', () => exportModal.classList.add('hidden'));
        });

        document.getElementById('confirm-export').addEventListener('click', () => {
            const filterTahun = document.getElementById('export-tahun').value;
            const filterJenis = document.getElementById('export-jenis').value;
            const filterRuas = document.getElementById('export-ruas').value;

            let filteredData = allPerizinanData.filter(item => {
                const matchTahun = filterTahun === 'all' || (item.tanggal_terbit && item.tanggal_terbit.startsWith(filterTahun));
                let matchJenis = filterJenis === 'all';
                if (filterJenis !== 'all') {
                    if (filterJenis === 'dispensasi') matchJenis = item.jenis_izin === 'dispensasi';
                    else matchJenis = item.sub_jenis === filterJenis;
                }
                let matchRuas = filterRuas === 'all';
                if (filterRuas !== 'all' && item.lokasi) {
                    matchRuas = item.lokasi.some(l => l.nama_ruas_jalan === filterRuas);
                }
                return matchTahun && matchJenis && matchRuas;
            });

            if (filteredData.length === 0) {
                alert('Tidak ada data yang sesuai dengan filter pilihan Anda.');
                return;
            }

            const header = ["NO", "NOMOR IZIN", "TANGGAL TERBIT", "PEMOHON", "JENIS", "SUB JENIS", "SATKER", "RUAS JALAN", "PNBP (Rp)", "STATUS"];
            const rows = filteredData.map((item, index) => [
                index + 1,
                item.nomor_izin,
                item.tanggal_terbit,
                item.pemohon,
                item.jenis_izin,
                item.sub_jenis || '-',
                item.satker ? item.satker.nama_satker : '-',
                item.lokasi ? item.lokasi.map(l => l.nama_ruas_jalan).join(', ') : '-',
                parseFloat(item.pnbp || 0),
                item.status.toUpperCase()
            ]);

            const ws = XLSX.utils.aoa_to_sheet([header, ...rows]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Laporan Perizinan");
            XLSX.writeFile(wb, `Rekap_Perizinan_${new Date().toISOString().split('T')[0]}.xlsx`);
            exportModal.classList.add('hidden');
        });

        document.getElementById('confirm-export-pdf').addEventListener('click', () => {
            const filterTahun = document.getElementById('export-tahun').value;
            const filterJenis = document.getElementById('export-jenis').value;
            const filterRuas = document.getElementById('export-ruas').value;

            const filteredData = allPerizinanData.filter(item => {
                const matchTahun = filterTahun === 'all' || (item.tanggal_terbit && item.tanggal_terbit.startsWith(filterTahun));
                let matchJenis = filterJenis === 'all';
                if (filterJenis !== 'all') {
                    if (filterJenis === 'dispensasi') matchJenis = item.jenis_izin === 'dispensasi';
                    else matchJenis = item.sub_jenis === filterJenis;
                }
                let matchRuas = filterRuas === 'all';
                if (filterRuas !== 'all' && item.lokasi) {
                    matchRuas = item.lokasi.some(l => l.nama_ruas_jalan === filterRuas);
                }
                return matchTahun && matchJenis && matchRuas;
            });

            if (filteredData.length === 0) {
                alert('Tidak ada data yang sesuai dengan filter pilihan Anda.');
                return;
            }

            try {
                let jsPDFConstructor = null;
                if (window.jspdf && window.jspdf.jsPDF) {
                    jsPDFConstructor = window.jspdf.jsPDF;
                } else if (typeof window.jsPDF === 'function') {
                    jsPDFConstructor = window.jsPDF;
                }

                if (!jsPDFConstructor) {
                    throw new Error('Library jsPDF tidak ditemukan.');
                }
                
                const doc = new jsPDFConstructor('l', 'mm', 'a4');

                doc.setFontSize(18);
                doc.setTextColor(30, 58, 138);
                doc.text("LAPORAN REKAPITULASI PERIZINAN JALAN", 148, 20, { align: 'center' });
                
                doc.setFontSize(10);
                doc.setTextColor(100);
                doc.text(`Dicetak pada: ${new Date().toLocaleString('id-ID')}`, 148, 27, { align: 'center' });

                const body = filteredData.map((item, index) => [
                    index + 1,
                    item.nomor_izin || '-',
                    item.tanggal_terbit || '-',
                    item.pemohon || '-',
                    (item.jenis_izin || '').toUpperCase(),
                    item.sub_jenis || '-',
                    item.satker ? item.satker.nama_satker : '-',
                    item.lokasi ? item.lokasi.map(l => l.nama_ruas_jalan).join(', ') : '-',
                    new Intl.NumberFormat('id-ID').format(parseFloat(item.pnbp || 0)),
                    (item.status || '').toUpperCase()
                ]);

                if (typeof doc.autoTable !== 'function') {
                    throw new Error('Plugin autoTable tidak ditemukan.');
                }

                doc.autoTable({
                    startY: 35,
                    head: [["NO", "NOMOR IZIN", "TGL TERBIT", "PEMOHON", "JENIS", "SUB JENIS", "SATKER", "RUAS JALAN", "PNBP", "STATUS"]],
                    body: body,
                    theme: 'striped',
                    headStyles: { fillColor: [30, 58, 138], textColor: 255, halign: 'center' },
                    styles: { fontSize: 7, cellPadding: 2 },
                    columnStyles: {
                        0: { halign: 'center', cellWidth: 8 },
                        1: { cellWidth: 35 },
                        3: { cellWidth: 40 },
                        7: { cellWidth: 50 },
                        8: { halign: 'right', cellWidth: 25 },
                        9: { halign: 'center', cellWidth: 20 }
                    }
                });

                doc.save(`Rekap_Perizinan_${new Date().toISOString().split('T')[0]}.pdf`);
                exportModal.classList.add('hidden');
            } catch (err) {
                console.error(err);
                alert('Gagal mengekspor PDF: ' + err.message);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            fetchPerizinan();
            fetchRuasJalan();
        });
    </script>
</body>
</html>
