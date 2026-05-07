const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const QR = require('qrcode');
const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');
const path = require('path');

const app = express();
const port = 3000;

// ─── CORS: Izinkan request dari browser (XAMPP, localhost, dll) ──────────────
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    if (req.method === 'OPTIONS') return res.sendStatus(200);
    next();
});
// ─────────────────────────────────────────────────────────────────────────────

app.use(bodyParser.json());

// Initialize WhatsApp Client
const client = new Client({
    authStrategy: new LocalAuth({
        dataPath: './sessions'
    }),
    webVersionCache: {
        type: 'remote',
        remotePath: 'https://raw.githubusercontent.com/wppconnect-team/wa-version/main/html/2.2412.54.html',
    },
    puppeteer: {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-extensions',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ],
    }
});

client.on('qr', (qr) => {
    console.log('\n\n==================================================');
    console.log('SCAN QR CODE DI BAWAH INI DENGAN WHATSAPP ANDA:');
    console.log('==================================================\n');
    
    // Tampilkan QR di terminal (gunakan small: false jika scanner sulit membaca)
    qrcode.generate(qr, { small: false });

    // Simpan QR ke folder sebagai gambar
    const imgDir = path.join(__dirname, 'img_bot');
    if (!fs.existsSync(imgDir)){
        fs.mkdirSync(imgDir);
    }
    const imgPath = path.join(imgDir, 'qr.png');
    QR.toFile(imgPath, qr, (err) => {
        if (err) console.error('Gagal menyimpan QR Image:', err);
        else console.log('QR Code disimpan ke:', imgPath);
    });

    // Link cadangan jika QR di terminal tidak muncul/berantakan
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(qr)}&size=300x300`;
    console.log('\nJika QR di atas tidak muncul atau sulit di-scan, buka link ini:');
    console.log(qrUrl);
    
    console.log('\nTips: Jika scan terus gagal, coba hapus folder "sessions" lalu jalankan ulang.');
    console.log('==================================================\n\n');
});

client.on('ready', () => {
    console.log('WhatsApp Bot Siperjalan sudah SIAP dan TERKONEKSI!');
});

client.on('auth_failure', msg => {
    console.error('AUTHENTICATION FAILURE', msg);
});

client.on('disconnected', (reason) => {
    console.log('WhatsApp terputus:', reason);
});

// Endpoint untuk mengirim pesan dari Laravel
app.post('/send-message', async (req, res) => {
    const { number, message } = req.body;

    if (!number || !message) {
        return res.status(400).json({ success: false, message: 'Nomor HP dan pesan harus diisi!' });
    }

    try {
        // Format nomor: hilangkan +, spasi, dan pastikan diawali 62
        let formattedNumber = number.replace(/[^\d]/g, '');
        if (formattedNumber.startsWith('0')) {
            formattedNumber = '62' + formattedNumber.slice(1);
        }
        if (!formattedNumber.endsWith('@c.us')) {
            formattedNumber += '@c.us';
        }

        const response = await client.sendMessage(formattedNumber, message);
        console.log('Pesan terkirim ke:', formattedNumber);
        res.json({ success: true, response });
    } catch (error) {
        console.error('Gagal mengirim pesan:', error);
        res.status(500).json({ success: false, error: error.message });
    }
});

client.initialize();

app.listen(port, () => {
    console.log(`Bot API listening at http://localhost:${port}`);
});
