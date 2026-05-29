# Script Otomatisasi Deployment Siperjalan (PowerShell)
# Simpan file ini dengan nama: deploy.ps1

# --- KONFIGURASI SERVER ---
$SERVER_IP = "202.155.91.204"
$SERVER_USER = "root"
$REMOTE_BASE_PATH = "/var/www" # Folder utama di server

# Folder tujuan di server (Sesuaikan dengan setting Nginx/Apache)
$PATH_FRONTEND = "$REMOTE_BASE_PATH/siperjalan"
$PATH_BACKEND = "$REMOTE_BASE_PATH/api-siperjalan"
$PATH_BOT = "$REMOTE_BASE_PATH/bot-siperjalan"

function Show-Header {
    Write-Host "==============================================" -ForegroundColor Cyan
    Write-Host "   SIPERJALAN DEPLOYMENT AUTOMATION" -ForegroundColor Cyan
    Write-Host "==============================================" -ForegroundColor Cyan
}

function Prepare-Remote-Dir {
    param($path)
    Write-Host "Memastikan folder $path tersedia di server..." -ForegroundColor Gray
    ssh "${SERVER_USER}@${SERVER_IP}" "mkdir -p $path"
}

function Deploy-Frontend {
    Write-Host "`n[1/3] Memulai Deployment Frontend..." -ForegroundColor Yellow
    Prepare-Remote-Dir $PATH_FRONTEND
    
    # 1. Build Vite
    Write-Host "Menjalankan build Vite..."
    npm run build
    
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Gagal melakukan build!" -ForegroundColor Red
        return
    }

    # 2. Kompres file (agar upload cepat)
    Write-Host "Mengompres file..."
    tar -czf frontend.tar.gz -C app/dist .

    # 3. Upload & Extract
    Write-Host "Mengunggah ke server..."
    scp frontend.tar.gz "${SERVER_USER}@${SERVER_IP}:${PATH_FRONTEND}"
    
    Write-Host "Extracting di server..."
    ssh "${SERVER_USER}@${SERVER_IP}" "cd ${PATH_FRONTEND} && tar -xzf frontend.tar.gz && rm frontend.tar.gz"
    
    Remove-Item frontend.tar.gz
    Write-Host "Frontend Berhasil Diupdate!" -ForegroundColor Green
}

function Deploy-Backend {
    Write-Host "`n[2/3] Memulai Deployment Backend (Laravel)..." -ForegroundColor Yellow
    Prepare-Remote-Dir $PATH_BACKEND
    
    # 1. Kompres (Kecuali vendor, node_modules, .env, git)
    Write-Host "Mengompres file (tanpa vendor/env)..."
    tar --exclude="vendor" --exclude="node_modules" --exclude=".env" --exclude=".git" -czf backend.tar.gz -C backend .

    # 2. Upload & Extract
    Write-Host "Mengunggah ke server..."
    scp backend.tar.gz "${SERVER_USER}@${SERVER_IP}:${PATH_BACKEND}"
    
    Write-Host "Extracting & Optimizing di server..."
    ssh "${SERVER_USER}@${SERVER_IP}" "cd ${PATH_BACKEND} && tar -xzf backend.tar.gz && rm backend.tar.gz && php artisan optimize"
    
    Remove-Item backend.tar.gz
    Write-Host "Backend Berhasil Diupdate!" -ForegroundColor Green
}

function Deploy-Bot {
    Write-Host "`n[3/3] Memulai Deployment WhatsApp Bot..." -ForegroundColor Yellow
    Prepare-Remote-Dir $PATH_BOT
    
    # 1. Kompres
    Write-Host "Mengompres file bot..."
    tar --exclude="node_modules" --exclude=".git" -czf bot.tar.gz -C botWA .

    # 2. Upload & Extract
    Write-Host "Mengunggah ke server..."
    scp bot.tar.gz "${SERVER_USER}@${SERVER_IP}:${PATH_BOT}"
    
    Write-Host "Extracting & Restarting PM2..."
    ssh "${SERVER_USER}@${SERVER_IP}" "cd ${PATH_BOT} && tar -xzf bot.tar.gz && rm bot.tar.gz && npm install && (pm2 restart wa-bot || pm2 start bot.js --name 'wa-bot')"
    
    Remove-Item bot.tar.gz
    Write-Host "WhatsApp Bot Berhasil Diupdate!" -ForegroundColor Green
}

# --- MAIN MENU ---
Clear-Host
Show-Header
Write-Host "Target Server: ${SERVER_USER}@${SERVER_IP}" -ForegroundColor Gray
Write-Host "Pilih Komponen yang ingin di-deploy:"
Write-Host "1. Frontend Saja (Vite Build + Upload)"
Write-Host "2. Backend Saja (Laravel Upload + Optimize)"
Write-Host "3. WhatsApp Bot Saja (Upload + Restart PM2)"
Write-Host "4. SEMUA (Full Deploy)"
Write-Host "Q. Keluar"

$choice = Read-Host "`nMasukkan pilihan Anda (1/2/3/4/Q)"

switch ($choice) {
    "1" { Deploy-Frontend }
    "2" { Deploy-Backend }
    "3" { Deploy-Bot }
    "4" { 
        Deploy-Frontend
        Deploy-Backend
        Deploy-Bot
    }
    "Q" { exit }
    Default { Write-Host "Pilihan tidak valid." -ForegroundColor Red }
}

Write-Host "`nProses Selesai!" -ForegroundColor Cyan
pause
