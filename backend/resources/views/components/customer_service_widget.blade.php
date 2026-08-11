<!-- CS Widget Container -->
<div id="cs-widget-container" class="fixed bottom-6 right-6 z-[9999] font-sans">
    
    <!-- Chat Popup Window -->
    <div id="cs-chat-popup" class="hidden flex-col bg-white rounded-2xl shadow-2xl w-80 mb-4 overflow-hidden border border-gray-100 transform transition-all duration-300 origin-bottom-right scale-95 opacity-0">
        <!-- Header -->
        <div class="bg-gradient-to-r from-bps-navy to-bps-blue p-4 text-white flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-inner">
                    <i class="ph ph-robot text-2xl text-bps-blue"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Customer Service</h3>
                    <p class="text-xs text-blue-100 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-400"></span> Online
                    </p>
                </div>
            </div>
            <button id="cs-close-btn" class="text-white hover:text-gray-200 focus:outline-none transition-colors">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>

        <!-- Chat Body (Form) -->
        <div class="p-4" id="cs-form-container">
            <p class="text-xs text-gray-500 mb-4 text-center">Tinggalkan pesan dan kami akan segera merespon via WhatsApp Anda.</p>
            <form id="cs-chat-form" class="space-y-3">
                <div>
                    <input type="text" id="cs-name" placeholder="Nama Anda (opsional)" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition-all">
                </div>
                <div>
                    <input type="text" id="cs-number" required placeholder="Nomor WhatsApp (Cth: 0812...)" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition-all">
                </div>
                <div>
                    <textarea id="cs-message" required placeholder="Tulis pesan Anda..." rows="3" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition-all resize-none"></textarea>
                </div>
                <button type="submit" id="cs-submit-btn" class="w-full bg-bps-blue hover:bg-bps-navy text-white font-medium py-2 px-4 rounded-lg flex items-center justify-center gap-2 transition-colors">
                    <span>Kirim Pesan</span>
                    <i class="ph ph-paper-plane-right"></i>
                </button>
            </form>
        </div>

        <!-- Success Message -->
        <div id="cs-success-message" class="hidden p-6 flex-col items-center text-center">
            <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mb-4">
                <i class="ph ph-check-circle text-4xl"></i>
            </div>
            <h4 class="font-bold text-gray-800 mb-2">Pesan Terkirim!</h4>
            <p class="text-sm text-gray-500">Silakan cek WhatsApp Anda, kami akan segera membalasnya di sana.</p>
            <button id="cs-new-message-btn" class="mt-4 text-sm text-bps-blue font-medium hover:underline" type="button">Kirim pesan baru</button>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button id="cs-fab" class="w-14 h-14 bg-bps-blue hover:bg-bps-navy text-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-transform duration-300 focus:outline-none float-right">
        <i class="ph ph-chat-teardrop-text text-3xl"></i>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fab = document.getElementById('cs-fab');
        const popup = document.getElementById('cs-chat-popup');
        const closeBtn = document.getElementById('cs-close-btn');
        const form = document.getElementById('cs-chat-form');
        const submitBtn = document.getElementById('cs-submit-btn');
        const formContainer = document.getElementById('cs-form-container');
        const successMessage = document.getElementById('cs-success-message');
        const newMessageBtn = document.getElementById('cs-new-message-btn');

        // Toggle popup
        function togglePopup() {
            if (popup.classList.contains('hidden')) {
                popup.classList.remove('hidden');
                // Allow browser to render block before animating opacity and scale
                setTimeout(() => {
                    popup.classList.remove('scale-95', 'opacity-0');
                    popup.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                popup.classList.remove('scale-100', 'opacity-100');
                popup.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    popup.classList.add('hidden');
                }, 300); // Wait for transition to finish
            }
        }

        fab.addEventListener('click', togglePopup);
        closeBtn.addEventListener('click', togglePopup);

        // Form Submit handler
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = document.getElementById('cs-name').value;
            const number = document.getElementById('cs-number').value;
            const message = document.getElementById('cs-message').value;

            // Loading state
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ph ph-spinner-gap animate-spin"></i> Mengirim...';
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                // We use web.php route, wait, I added it as /cs-chat, not /api/cs-chat
                // Wait, it is in web.php, but it doesn't have the api prefix unless it's in the api group!
                // Ah, I added it to web.php inside `Route::prefix('api')->group(...)`! So it is `/api/cs-chat`
                
                // Let's get CSRF token if needed. Wait, `/api/cs-chat` is in `web.php`, but routes in `web.php` use `web` middleware group (which includes VerifyCsrfToken).
                // Wait! Let me check `backend/app/Http/Kernel.php` or how routes are loaded. 
                // Wait, if it is in the `api` group, it might be in `web.php` just to have them together?
                // Let's check where the API routes were mapped. "Semua route API telah dipindahkan ke routes/web.php agar menggunakan session state Laravel."
                // Oh! If they are in `web.php` but prefixed with `/api`, they might still use `web` middleware!
                // If it uses `web` middleware, we need a CSRF token.
                
                let csrfToken = document.querySelector('meta[name="csrf-token"]');
                let headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                }

                const response = await fetch('{{ url('api/cs-chat') }}', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({ name, number, message })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Show success state
                    formContainer.classList.add('hidden');
                    successMessage.classList.remove('hidden');
                    successMessage.classList.add('flex');
                    form.reset();
                } else {
                    alert('Gagal mengirim pesan: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menghubungi server. Periksa koneksi internet Anda.');
            } finally {
                // Restore button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });

        newMessageBtn.addEventListener('click', function() {
            successMessage.classList.add('hidden');
            successMessage.classList.remove('flex');
            formContainer.classList.remove('hidden');
        });
    });
</script>
