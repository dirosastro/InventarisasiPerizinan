import makeWASocket, {
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
    makeCacheableSignalKeyStore,
    isJidBroadcast,
} from '@whiskeysockets/baileys';
import express from 'express';
import bodyParser from 'body-parser';
import qrcode from 'qrcode-terminal';
import QR from 'qrcode';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import pino from 'pino';

const __filename = fileURLToPath(import.meta.url);
const __dirname  = path.dirname(__filename);

const app  = express();
const port = 3000;

// ─── CORS ────────────────────────────────────────────────────────────────────
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    if (req.method === 'OPTIONS') return res.sendStatus(200);
    next();
});
app.use(bodyParser.json());
// ─────────────────────────────────────────────────────────────────────────────

// Menyimpan instance socket Baileys agar bisa dipakai endpoint Express
let sock = null;

// ─── Fungsi utama: inisialisasi & auto-reconnect ──────────────────────────────
async function startBot() {
    // Folder penyimpanan sesi (multi-file auth state)
    const sessionDir = path.join(__dirname, 'sessions');
    if (!fs.existsSync(sessionDir)) fs.mkdirSync(sessionDir, { recursive: true });

    const { state, saveCreds } = await useMultiFileAuthState(sessionDir);
    const { version } = await fetchLatestBaileysVersion();

    console.log(`\n🤖  Siperjalan WhatsApp Bot menggunakan Baileys v${version.join('.')}\n`);

    sock = makeWASocket({
        version,
        logger: pino({ level: 'silent' }), // Ganti ke 'debug' untuk troubleshooting
        auth: {
            creds: state.creds,
            keys: makeCacheableSignalKeyStore(state.keys, pino({ level: 'silent' })),
        },
        printQRInTerminal: false, // kita tangani sendiri agar bisa simpan ke file
        shouldIgnoreJid: jid => isJidBroadcast(jid),
        getMessage: async () => undefined,
    });

    // ── QR Code ──────────────────────────────────────────────────────────────
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            console.log('\n==================================================');
            console.log(' SCAN QR CODE DI BAWAH INI DENGAN WHATSAPP ANDA:');
            console.log('==================================================\n');

            // Tampilkan di terminal
            qrcode.generate(qr, { small: false });

            // Simpan sebagai gambar PNG
            const imgDir = path.join(__dirname, 'img_bot');
            if (!fs.existsSync(imgDir)) fs.mkdirSync(imgDir);
            const imgPath = path.join(imgDir, 'qr.png');
            QR.toFile(imgPath, qr, (err) => {
                if (err) console.error('Gagal menyimpan QR Image:', err);
                else console.log('📸  QR Code disimpan ke:', imgPath);
            });

            // Link cadangan
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(qr)}&size=300x300`;
            console.log('\nJika QR sulit di-scan, buka link ini di browser:');
            console.log(qrUrl);
            console.log('\nTips: Jika scan terus gagal, hapus folder "sessions" lalu jalankan ulang.');
            console.log('==================================================\n');
        }

        if (connection === 'close') {
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

            console.log(`\n⚠️  Koneksi terputus (kode: ${statusCode}). ${shouldReconnect ? 'Mencoba reconnect...' : 'Sesi di-logout.'}\n`);

            if (shouldReconnect) {
                setTimeout(startBot, 3000); // reconnect setelah 3 detik
            } else {
                // Hapus sesi lama agar bisa scan QR baru
                fs.rmSync(sessionDir, { recursive: true, force: true });
                console.log('🗑️  Folder sesi dihapus. Jalankan ulang bot untuk scan QR baru.');
            }
        }

        if (connection === 'open') {
            console.log('\n✅  WhatsApp Bot Siperjalan sudah SIAP dan TERKONEKSI!\n');
        }
    });

    // ── Simpan kredensial setiap ada perubahan ────────────────────────────────
    sock.ev.on('creds.update', saveCreds);
}

// ─── Endpoint: kirim pesan ────────────────────────────────────────────────────
app.post('/send-message', async (req, res) => {
    const { number, message } = req.body;

    if (!number || !message) {
        return res.status(400).json({ success: false, message: 'Nomor HP dan pesan harus diisi!' });
    }

    if (!sock) {
        return res.status(503).json({ success: false, message: 'Bot belum terhubung ke WhatsApp.' });
    }

    try {
        // Format nomor: hilangkan karakter non-digit, ubah awalan 0 → 62
        let formattedNumber = number.replace(/[^\d]/g, '');
        if (formattedNumber.startsWith('0')) {
            formattedNumber = '62' + formattedNumber.slice(1);
        }

        // Format JID Baileys: <nomor>@s.whatsapp.net  (bukan @c.us seperti wwjs)
        const jid = formattedNumber + '@s.whatsapp.net';

        const response = await sock.sendMessage(jid, { text: message });
        console.log(`📤  Pesan terkirim ke: ${jid}`);
        res.json({ success: true, response });
    } catch (error) {
        console.error('❌  Gagal mengirim pesan:', error);
        res.status(500).json({ success: false, error: error.message });
    }
});

// ─── Endpoint: cek status bot ─────────────────────────────────────────────────
app.get('/status', (req, res) => {
    const connected = sock?.user != null;
    res.json({
        success: true,
        connected,
        user: connected ? sock.user : null,
    });
});

// ─── Jalankan bot & server Express ───────────────────────────────────────────
startBot();

app.listen(port, '127.0.0.1', () => {
    console.log(`🚀  Bot API siap di http://127.0.0.1:${port}`);
    console.log(`   POST /send-message  → kirim pesan`);
    console.log(`   GET  /status        → cek status koneksi\n`);
});
