<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Tester | Siperjalan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f0f4f8; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full glass-card rounded-3xl p-8 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="ph-fill ph-whatsapp-logo text-4xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">WhatsApp Bot Tester</h1>
        </div>

        <div id="status-alert" class="hidden p-4 rounded-xl text-sm font-medium border animate-pulse"></div>

        <form id="test-form" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Nomor WhatsApp</label>
                <input type="text" id="phone_number" required placeholder="08..." class="w-full px-4 py-3.5 bg-gray-50 border rounded-2xl">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Pesan Tes</label>
                <textarea id="message" required rows="6" class="w-full px-4 py-3.5 bg-gray-50 border rounded-2xl">Tes Bot Siperjalan!</textarea>
            </div>
            <button type="submit" id="btn-send" class="w-full py-4 bg-green-600 text-white font-bold rounded-2xl shadow-lg">
                Kirim Pesan Tes
            </button>
        </form>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
            <a href="{{ route('perizinan_view') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                <i class="ph ph-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <script>
        const API_URL = "{{ url('api/wa-test') }}";
        document.getElementById('test-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btn-send');
            const alert = document.getElementById('status-alert');
            const number = document.getElementById('phone_number').value.replace(/[+-]/g, '');
            const message = document.getElementById('message').value;

            btn.disabled = true;
            btn.innerHTML = `Mengirim...`;

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ number, message })
                });
                const result = await response.json();
                alert.classList.remove('hidden');
                alert.textContent = result.message;
                alert.className = result.success ? 'p-4 bg-green-50 text-green-700 rounded-xl' : 'p-4 bg-red-50 text-red-700 rounded-xl';
            } catch (error) {
                alert.classList.remove('hidden');
                alert.textContent = 'Gagal menghubungi server.';
            } finally {
                btn.disabled = false;
                btn.innerHTML = `Kirim Pesan Tes`;
            }
        });
    </script>
</body>
</html>
