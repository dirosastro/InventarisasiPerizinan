<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Formulir administrasi penambahan dan pengeditan data perizinan pemanfaatan jalan nasional BPJN NTB.">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>Kelola Perizinan - Simpanan (Sistem Informasi Perizinan Jalan)</title>

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
                        primary: '#1E3A5F',
                        secondary: '#2C5282',
                        accent: '#3182CE',
                    }
                }
            }
        }
    </script>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web" defer></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"></noscript>
    <!-- JSZip for KMZ -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- toGeoJSON for KML -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/togeojson/0.16.0/togeojson.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }
        .icon-option.selected {
            background-color: #3182CE;
            color: white;
            border-color: #3182CE;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-['Inter'] h-screen overflow-hidden flex flex-col">

    <!-- HEADER -->
    <header class="bg-primary text-white h-[60px] flex items-center justify-between px-4 lg:px-6 shadow-md z-50 shrink-0">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2 font-bold text-lg tracking-wide">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Simpanan" width="32" height="32" class="h-8 w-auto">
                <span>Simpanan - ADMIN Panel</span>
            </div>
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-md hover:bg-secondary transition-colors">Beranda</a>
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md hover:bg-secondary transition-colors">Dashboard Admin</a>
                <a href="{{ route('peta') }}" class="px-3 py-2 rounded-md hover:bg-secondary transition-colors">Peta Pemanfaatan</a>
                <a href="{{ route('users') }}" id="admin-nav" class="hidden px-3 py-2 rounded-md hover:bg-secondary transition-colors">Manajemen User</a>
            </nav>
        </div>
        <div class="flex items-center">
            <button onclick="logout()" class="flex items-center gap-2 px-4 py-2 bg-red-500/20 text-red-100 hover:bg-red-500 hover:text-white rounded-lg transition-colors text-sm font-medium">
                <i class="ph ph-sign-out"></i> Logout
            </button>
            <script>
                function logout() {
                    localStorage.clear();
                    window.location.href = "{{ route('logout') }}";
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
                    const userRole = localStorage.getItem('userRole');
                    const adminNav = document.getElementById('admin-nav');
                    
                    if (isLoggedIn && userRole === 'superadmin' && adminNav) {
                        adminNav.classList.remove('hidden');
                    }
                });
            </script>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-gray-100">
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('perizinan_view') }}" class="p-2 bg-white rounded-full shadow-sm hover:bg-gray-50 transition-colors text-gray-600">
                    <i class="ph ph-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 id="page-title" class="text-2xl font-bold text-gray-800">Formulir Perizinan</h1>
                </div>
            </div>

            <!-- Toast Notification -->
            <div id="toast" class="hidden p-4 mb-4 text-sm rounded-lg" role="alert">
                <span id="toast-message" class="font-medium"></span>
            </div>

            <form id="form-perizinan" class="glass-panel rounded-2xl p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informasi Umum -->
                    <div class="space-y-4">
                        <h2 class="font-semibold text-lg border-b pb-2 mb-4">Informasi Umum</h2>
                        
                        <div>
                            <label for="nomor_izin" class="block text-sm font-medium text-gray-700 mb-1">Nomor Izin</label>
                            <input type="text" id="nomor_izin" required placeholder="IZN/BPJN-NTB/2026/001" 
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent transition-all bg-gray-50 hover:bg-white">
                        </div>

                        <div>
                            <label for="pemohon" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemohon / Perusahaan</label>
                            <input type="text" id="pemohon" required placeholder="PT. Telekomunikasi..." 
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent transition-all bg-gray-50 hover:bg-white">
                        </div>

                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">Nomor HP Pemohon (WhatsApp)</label>
                            <input type="text" id="no_hp" required placeholder="081234567890" 
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent transition-all bg-gray-50 hover:bg-white">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="jenis_izin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Izin</label>
                                <select id="jenis_izin" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent bg-gray-50 hover:bg-white">
                                    <option value="izin">Izin</option>
                                    <option value="rekomendasi">Rekomendasi</option>
                                    <option value="dispensasi">Dispensasi</option>
                                </select>
                            </div>
                            <div>
                                <label for="sub_jenis" class="block text-sm font-medium text-gray-700 mb-1">Sub Jenis</label>
                                <select id="sub_jenis" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent bg-gray-50 hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed">
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal_terbit" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terbit</label>
                                <input type="date" id="tanggal_terbit" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent bg-gray-50 hover:bg-white">
                            </div>
                            <div id="container_tanggal_akhir">
                                <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                <input type="date" id="tanggal_akhir" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent bg-gray-50 hover:bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="main_satker" class="block text-sm font-medium text-gray-700 mb-1">Satker Pengelola</label>
                                <select id="main_satker" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent bg-gray-50 hover:bg-white">
                                    <option value="">-- Pilih Satker --</option>
                                </select>
                            </div>
                            <div id="container_pnbp">
                                <label for="pnbp" class="block text-sm font-medium text-gray-700 mb-1">Nilai PNBP (Rp)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-sm">Rp</span>
                                    <input type="text" id="pnbp" placeholder="0" 
                                        class="w-full pl-11 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent transition-all bg-gray-50 hover:bg-white">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div id="container_panjang">
                                <label for="panjang" class="block text-sm font-medium text-gray-700 mb-1">Panjang (Meter)</label>
                                <input type="number" step="0.01" id="panjang" placeholder="Misal: 100" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent bg-gray-50 hover:bg-white">
                            </div>
                            <div id="container_lebar">
                                <label for="lebar" class="block text-sm font-medium text-gray-700 mb-1">Lebar (Meter)</label>
                                <input type="number" step="0.01" id="lebar" placeholder="Misal: 5" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent bg-gray-50 hover:bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Ikon & GeoJSON -->
                    <div class="space-y-4">
                        <h2 class="font-semibold text-lg border-b pb-2 mb-4">Ikon & Dokumen</h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Ikon Peta (Opsional)</label>
                            <input type="hidden" id="icon_input" value="">
                            <div class="grid grid-cols-4 gap-3" id="icon-picker">
                                <div class="icon-option p-3 border rounded-xl text-center cursor-pointer text-gray-500" data-icon="ph-lightning">
                                    <i class="ph ph-lightning text-2xl mb-1"></i><div class="text-[10px] font-medium">Listrik</div>
                                </div>
                                <div class="icon-option p-3 border rounded-xl text-center cursor-pointer text-gray-500" data-icon="ph-drop">
                                    <i class="ph ph-drop text-2xl mb-1"></i><div class="text-[10px] font-medium">Air</div>
                                </div>
                                <div class="icon-option p-3 border rounded-xl text-center cursor-pointer text-gray-500" data-icon="ph-wifi-high">
                                    <i class="ph ph-wifi-high text-2xl mb-1"></i><div class="text-[10px] font-medium">Optik</div>
                                </div>
                                <div class="icon-option p-3 border rounded-xl text-center cursor-pointer text-gray-500" data-icon="ph-signpost">
                                    <i class="ph ph-signpost text-2xl mb-1"></i><div class="text-[10px] font-medium">Reklame</div>
                                </div>
                                <div class="icon-option p-3 border rounded-xl text-center cursor-pointer text-gray-500" data-icon="ph-truck">
                                    <i class="ph ph-truck text-2xl mb-1"></i><div class="text-[10px] font-medium">Angkutan</div>
                                </div>
                                <div class="icon-option p-3 border rounded-xl text-center cursor-pointer text-gray-500" data-icon="ph-map-pin">
                                    <i class="ph ph-map-pin text-2xl mb-1"></i><div class="text-[10px] font-medium">Umum</div>
                                </div>
                                <div class="icon-option p-3 border rounded-xl text-center cursor-pointer text-gray-500" data-icon="ph-car">
                                    <i class="ph ph-car text-2xl mb-1"></i><div class="text-[10px] font-medium">Akses Jalan</div>
                                </div>
                                <div class="icon-option p-3 border rounded-xl text-center cursor-pointer text-gray-500" data-icon="ph-warning-octagon">
                                    <i class="ph ph-warning-octagon text-2xl mb-1"></i><div class="text-[10px] font-medium">Rambu</div>
                                </div>
                                <div class="icon-option p-3 border rounded-xl text-center cursor-pointer text-gray-500" data-icon="ph-plant">
                                    <i class="ph ph-plant text-2xl mb-1"></i><div class="text-[10px] font-medium">Lahan</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="dokumen_pendukung" class="block text-sm font-medium text-gray-700 mb-1">Dokumen Pendukung</label>
                            <input id="dokumen_pendukung" type="file" name="dokumen[]" multiple class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-accent hover:file:bg-blue-100" />
                            <div id="existing-dokumen-list" class="mt-3 space-y-2"></div>
                            <div id="dokumen-list" class="mt-3 space-y-2"></div>
                        </div>

                        <div class="mt-4">
                            <label for="geojson_file" class="block text-sm font-medium text-gray-700 mb-2">Upload File Geospasial (.geojson, .kml, .kmz)</label>
                            <input id="geojson_file" type="file" accept=".geojson,application/geo+json,.kml,.kmz" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100" />
                            <div id="file-name-display" class="text-xs text-green-600 font-medium mt-2 hidden flex items-center gap-1">
                                <i class="ph-fill ph-check-circle"></i> <span id="file-name-text"></span> siap diunggah.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DAFTAR LOKASI -->
                <div class="mt-6 glass-panel rounded-xl p-6 border border-blue-100">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-lg text-gray-800">Daftar Lokasi Pemanfaatan</h2>
                        <button type="button" id="btn-tambah-lokasi" class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-accent text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                            <i class="ph ph-plus"></i> Tambah Lokasi
                        </button>
                    </div>
                    <div id="lokasi-container" class="space-y-3"></div>
                    <p id="lokasi-empty" class="text-sm text-center text-gray-400 py-4 italic">Belum ada lokasi.</p>
                </div>

                <div class="mt-8 border-t pt-6 flex justify-end gap-3">
                    <button type="submit" id="btn-submit" class="px-5 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-blue-700 shadow-lg transition-all flex items-center gap-2">
                        <i class="ph ph-floppy-disk"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        window.API_BASE_URL = "{{ url('/') }}";
    </script>
    <script src="{{ asset('js/admin.js') }}?v={{ time() }}"></script>
</body>

</html>
