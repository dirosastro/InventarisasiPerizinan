<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Halaman login administratif Sistem Informasi Perizinan Jalan (Simpanan) BPJN NTB.">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>Akses Admin - Simpanan</title>

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
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"></noscript>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web" defer></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 50%, #3182ce 100%);
            background-attachment: fixed;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('img/back_login.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.15;
            z-index: -1;
        }
    </style>
</head>

<body class="font-sans h-screen flex items-center justify-center relative">

    <div class="max-w-md w-full mx-4">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block mb-4 hover:scale-105 transition-transform">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Simpanan BPJN NTB" width="64" height="64" class="h-16 w-auto mx-auto">
            </a>
            <h1 class="text-2xl font-bold text-white">Selamat Datang Kembali</h1>
            <p class="text-white/80 text-sm mt-1">Masuk ke Dasbor Admin Simpanan</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div id="error-message"
                class="hidden bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4 border border-red-100 flex items-center gap-2">
                <i class="ph-fill ph-warning-circle text-lg"></i>
                <span>Username atau password salah!</span>
            </div>

            <form id="login-form" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                    <div class="relative">
                        <i class="ph ph-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg" aria-hidden="true"></i>
                        <input type="text" id="username" required placeholder="Masukkan username"
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <i class="ph ph-lock-key absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg" aria-hidden="true"></i>
                        <input type="password" id="password" required placeholder="••••••••"
                            class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all">
                        <button type="button" id="toggle-password" aria-label="Tampilkan password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="ph ph-eye text-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember" type="checkbox"
                        class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                    <label for="remember" class="ml-2 block text-sm text-gray-600">Ingat saya</label>
                </div>

                <button type="submit" id="btn-submit"
                    class="w-full bg-primary text-white font-semibold py-3 rounded-xl hover:bg-secondary transition-all flex items-center justify-center gap-2 shadow-md">
                    <i class="ph ph-sign-in"></i> Masuk Sekarang
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}"
                    class="text-sm text-gray-500 hover:text-accent font-medium flex items-center justify-center gap-1">
                    <i class="ph ph-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-password');
        const passInput = document.getElementById('password');

        toggleBtn.addEventListener('click', () => {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            toggleBtn.innerHTML = type === 'password' ? '<i class="ph ph-eye text-lg"></i>' : '<i class="ph ph-eye-slash text-lg"></i>';
        });

        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const user = document.getElementById('username').value;
            const pass = document.getElementById('password').value;
            const btn = document.getElementById('btn-submit');
            const errorMsg = document.getElementById('error-message');

            btn.innerHTML = '<i class="ph ph-spinner animate-spin text-xl"></i>';
            btn.disabled = true;
            errorMsg.classList.add('hidden');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch("{{ url('api/login') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ username: user, password: pass })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    localStorage.setItem('isLoggedIn', 'true');
                    localStorage.setItem('userRole', result.data.role);
                    localStorage.setItem('userName', result.data.nama);
                    window.location.href = "{{ route('dashboard') }}";
                } else {
                    errorMsg.innerHTML = `<i class="ph-fill ph-warning-circle text-lg"></i> <span>${result.message || 'Username atau password salah!'}</span>`;
                    errorMsg.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
                errorMsg.innerHTML = `<i class="ph-fill ph-warning-circle text-lg"></i> <span>Koneksi ke server gagal!</span>`;
                errorMsg.classList.remove('hidden');
            } finally {
                btn.innerHTML = '<i class="ph ph-sign-in"></i> Masuk Sekarang';
                btn.disabled = false;
            }
        });
    </script>
</body>

</html>
