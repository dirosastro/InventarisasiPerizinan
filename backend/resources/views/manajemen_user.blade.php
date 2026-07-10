<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>Manajemen User | Siperjalan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .text-primary { color: #0066FF; }
        .bg-primary { background-color: #1E3A5F; }
        .bg-accent { background-color: #0066FF; }
        
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.4s ease-out forwards; }
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
            <a href="{{ route('perizinan_view') }}" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl transition-all group">
                <i class="ph ph-file-text text-xl group-hover:text-accent"></i>
                <span class="font-semibold text-sm">Daftar Perizinan</span>
            </a>
            <a href="{{ route('users') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-accent rounded-xl transition-all group">
                <i class="ph-fill ph-users-three text-xl"></i>
                <span class="font-bold text-sm">Manajemen User</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-50">
            <button onclick="logout()" class="w-full bg-gray-50 rounded-2xl p-4 flex items-center gap-3 hover:bg-gray-100 transition-colors">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-accent">
                    <i class="ph ph-user text-xl"></i>
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <p id="sidebar-user-name" class="text-xs font-bold text-gray-900 truncate">Admin BPJN</p>
                    <p id="sidebar-user-role" class="text-[10px] text-gray-500 truncate uppercase">Administrator</p>
                </div>
                <i class="ph ph-sign-out text-gray-400"></i>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8">
        <div class="max-w-5xl mx-auto space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola akun akses sistem Siperjalan BPJN NTB.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="fetchUsers()" class="p-2.5 text-gray-400 hover:text-accent hover:bg-gray-100 rounded-xl transition-all" title="Refresh Data">
                        <i class="ph ph-arrows-clockwise text-xl"></i>
                    </button>
                    <button onclick="showModal()" class="bg-accent text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all flex items-center gap-2">
                        <i class="ph ph-user-plus text-xl"></i> Tambah User Baru
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Nama User</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Username</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Level / Role</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="user-table-body" class="divide-y divide-gray-50"></tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Tambah User -->
    <div id="user-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="hideModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden animate-fade-in-up">
            <div class="bg-primary px-6 py-4 flex items-center justify-between">
                <h3 class="text-white font-bold text-lg">Tambah Pengguna Baru</h3>
                <button onclick="hideModal()" class="text-white/70 hover:text-white transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>
            <form id="user-form" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" id="user-nama" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Username</label>
                    <input type="text" id="user-username" required placeholder="Contoh: budi123"
                        class="w-full px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" id="user-password" required placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Role / Level</label>
                    <select id="user-role" required
                        class="w-full px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent bg-gray-50">
                        <option value="user">User / Operator</option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                </div>
                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="hideModal()"
                        class="flex-1 py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 px-4 bg-accent hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.API_BASE_URL = "{{ url('/') }}";
        const API_URL = (window.API_BASE_URL || '').replace(/\/$/, '') + '/api/users';
        let users = [];

        // Update sidebar info
        document.addEventListener('DOMContentLoaded', () => {
            const userName = localStorage.getItem('userName');
            const userRole = localStorage.getItem('userRole');
            if (userName) document.getElementById('sidebar-user-name').innerText = userName;
            if (userRole) document.getElementById('sidebar-user-role').innerText = userRole;
        });

        async function fetchUsers() {
            const tbody = document.getElementById('user-table-body');
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-10"><i class="ph ph-spinner animate-spin text-2xl"></i></td></tr>';
            try {
                console.log('Fetching users from:', API_URL);
                const response = await fetch(API_URL);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                
                const result = await response.json();
                if (result.success) {
                    users = result.data;
                    renderUsers();
                } else {
                    throw new Error(result.message || 'Gagal memproses data');
                }
            } catch (err) {
                console.error('Fetch error:', err);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-10">
                            <div class="text-red-500 font-bold mb-2">Gagal mengambil data pengguna</div>
                            <div class="text-xs text-gray-400">${err.message}</div>
                            <button onclick="fetchUsers()" class="mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-bold transition-colors">
                                Coba Lagi
                            </button>
                        </td>
                    </tr>`;
            }
        }

        function renderUsers() {
            const tbody = document.getElementById('user-table-body');
            tbody.innerHTML = '';
            users.forEach(user => {
                const isSystemUser = user.email === 'admin';
                const roleBadge = `<span class="px-2 py-1 rounded-md ${user.role === 'superadmin' ? 'bg-purple-50 text-purple-600 border-purple-100' : 'bg-blue-50 text-blue-600 border-blue-100'} text-[10px] font-bold uppercase border">${user.role || 'User'}</span>`;
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="font-bold text-gray-800">${user.nama || user.username}</div>
                            ${isSystemUser ? '<div class="text-[9px] text-amber-600 font-bold uppercase mt-0.5">System Protected</div>' : ''}
                        </td>
                        <td class="px-6 py-5 text-gray-500">${user.username || user.email}</td>
                        <td class="px-6 py-5">${roleBadge}</td>
                        <td class="px-6 py-5 text-center">
                            ${!isSystemUser ? `
                            <button onclick="deleteUser(${user.id})" class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors" title="Hapus User">
                                <i class="ph ph-trash text-lg"></i>
                            </button>` : '<i class="ph ph-lock text-gray-300"></i>'}
                        </td>
                    </tr>
                `;
            });
        }

        function showModal() {
            document.getElementById('user-modal').classList.remove('hidden');
            document.getElementById('user-form').reset();
        }

        function hideModal() {
            document.getElementById('user-modal').classList.add('hidden');
        }

        document.getElementById('user-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ph ph-spinner animate-spin"></i> Menyimpan...';

            const payload = {
                nama: document.getElementById('user-nama').value,
                username: document.getElementById('user-username').value,
                password: document.getElementById('user-password').value,
                role: document.getElementById('user-role').value
            };

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.success) {
                    hideModal();
                    fetchUsers();
                } else {
                    alert('Gagal: ' + (result.message || 'Cek kembali data Anda'));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Simpan User';
            }
        });

        async function deleteUser(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) return;
            try {
                const response = await fetch(`${API_URL}/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                const result = await response.json();
                if (result.success) {
                    fetchUsers();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('Gagal menghapus user');
            }
        }

        function logout() {
            localStorage.clear();
            window.location.href = "{{ route('logout') }}";
        }

        document.addEventListener('DOMContentLoaded', fetchUsers);
    </script>
</body>
</html>
