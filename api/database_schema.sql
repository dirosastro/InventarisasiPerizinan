-- 1. satker
CREATE TABLE satker (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_satker VARCHAR(255) NOT NULL,
    kode_satker VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2. ppk
CREATE TABLE ppk (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_ppk VARCHAR(255) NOT NULL,
    satker_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (satker_id) REFERENCES satker(id) ON DELETE CASCADE
);

-- 3. users
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'ppk', 'satker', 'pimpinan') DEFAULT 'satker',
    satker_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (satker_id) REFERENCES satker(id) ON DELETE SET NULL
);

-- 4. ruas_jalan
CREATE TABLE ruas_jalan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_ruas VARCHAR(255) NOT NULL,
    kode_ruas VARCHAR(255) NOT NULL UNIQUE,
    panjang_km DECIMAL(8, 2) NULL,
    satker_id BIGINT UNSIGNED NOT NULL,
    ppk_id BIGINT UNSIGNED NOT NULL,
    geom GEOMETRY NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (satker_id) REFERENCES satker(id) ON DELETE CASCADE,
    FOREIGN KEY (ppk_id) REFERENCES ppk(id) ON DELETE CASCADE
);

-- 5. perizinan
CREATE TABLE perizinan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_izin VARCHAR(255) NOT NULL UNIQUE,
    jenis_izin ENUM('rekomendasi', 'izin', 'dispensasi') NOT NULL,
    sub_jenis ENUM('kabel', 'pipa', 'reklame', 'angkutan') NOT NULL,
    pemohon VARCHAR(255) NOT NULL,
    satker_id BIGINT UNSIGNED NOT NULL,
    ppk_id BIGINT UNSIGNED NOT NULL,
    ruas_id BIGINT UNSIGNED NOT NULL,
    tanggal_terbit DATE NOT NULL,
    tanggal_akhir DATE NOT NULL,
    status ENUM('aktif', 'hampir_habis', 'kadaluarsa') DEFAULT 'aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (satker_id) REFERENCES satker(id) ON DELETE CASCADE,
    FOREIGN KEY (ppk_id) REFERENCES ppk(id) ON DELETE CASCADE,
    FOREIGN KEY (ruas_id) REFERENCES ruas_jalan(id) ON DELETE CASCADE
);

-- 6. data_teknis
CREATE TABLE data_teknis (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perizinan_id BIGINT UNSIGNED NOT NULL,
    panjang_rumija DECIMAL(10, 2) NULL,
    panjang_rumaja DECIMAL(10, 2) NULL,
    panjang_dimanfaatkan DECIMAL(10, 2) NULL,
    sta_awal VARCHAR(255) NULL,
    sta_akhir VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (perizinan_id) REFERENCES perizinan(id) ON DELETE CASCADE
);

-- 7. pnbp
CREATE TABLE pnbp (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perizinan_id BIGINT UNSIGNED NOT NULL,
    jumlah DECIMAL(15, 2) NOT NULL,
    tanggal_bayar DATE NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (perizinan_id) REFERENCES perizinan(id) ON DELETE CASCADE
);

-- 8. dokumen
CREATE TABLE dokumen (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perizinan_id BIGINT UNSIGNED NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    tipe_dokumen ENUM('jaminan_pelaksanaan', 'izin', 'lainnya') DEFAULT 'lainnya',
    ukuran_file INT NOT NULL COMMENT 'Size in KB',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (perizinan_id) REFERENCES perizinan(id) ON DELETE CASCADE
);

-- 9. geojson_layer
CREATE TABLE geojson_layer (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_layer VARCHAR(255) NOT NULL,
    jenis_layer ENUM('ruas', 'rumija', 'rumaja', 'pemanfaatan', 'titik_izin') NOT NULL,
    data_geojson JSON NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 10. perizinan_geo
CREATE TABLE perizinan_geo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perizinan_id BIGINT UNSIGNED NOT NULL,
    geojson JSON NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (perizinan_id) REFERENCES perizinan(id) ON DELETE CASCADE
);

-- 11. notifikasi
CREATE TABLE notifikasi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perizinan_id BIGINT UNSIGNED NOT NULL,
    jenis_notifikasi ENUM('H-30', 'H-14', 'H-7', 'H-1') NOT NULL,
    tanggal_kirim DATE NOT NULL,
    status_kirim ENUM('pending', 'terkirim') DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (perizinan_id) REFERENCES perizinan(id) ON DELETE CASCADE
);

-- 12. log_aktivitas
CREATE TABLE log_aktivitas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    aktivitas VARCHAR(255) NOT NULL,
    tabel VARCHAR(255) NOT NULL,
    data_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
