-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Bulan Mei 2026 pada 03.57
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_perizinan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_teknis`
--

CREATE TABLE `data_teknis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perizinan_id` bigint(20) UNSIGNED NOT NULL,
  `panjang_rumija` decimal(10,2) DEFAULT NULL,
  `panjang_rumaja` decimal(10,2) DEFAULT NULL,
  `panjang_dimanfaatkan` decimal(10,2) DEFAULT NULL,
  `sta_awal` varchar(255) DEFAULT NULL,
  `sta_akhir` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen`
--

CREATE TABLE `dokumen` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perizinan_id` bigint(20) UNSIGNED NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `tipe_dokumen` enum('jaminan_pelaksanaan','izin','lainnya') NOT NULL DEFAULT 'lainnya',
  `ukuran_file` int(11) NOT NULL COMMENT 'Size in KB',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `geojson_layer`
--

CREATE TABLE `geojson_layer` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_layer` varchar(255) NOT NULL,
  `jenis_layer` enum('ruas','rumija','rumaja','pemanfaatan','titik_izin') NOT NULL,
  `data_geojson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data_geojson`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `tabel` varchar(255) NOT NULL,
  `data_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(17, '2026_04_28_000001_create_satker_table', 1),
(18, '2026_04_28_000002_create_ppk_table', 1),
(19, '2026_04_28_000003_create_users_table', 1),
(20, '2026_04_28_000004_create_ruas_jalan_table', 1),
(21, '2026_04_28_000005_create_perizinan_table', 1),
(22, '2026_04_28_000005b_create_perizinan_lokasi_table', 1),
(23, '2026_04_28_000006_create_data_teknis_table', 1),
(24, '2026_04_28_000007_create_pnbp_table', 1),
(25, '2026_04_28_000008_create_dokumen_table', 1),
(26, '2026_04_28_000009_create_geojson_layer_table', 1),
(27, '2026_04_28_000010_create_perizinan_geo_table', 1),
(28, '2026_04_28_000011_create_notifikasi_table', 1),
(29, '2026_04_28_000012_create_log_aktivitas_table', 1),
(30, '2026_05_04_055549_add_berkas_to_perizinan_table', 1),
(31, '2026_05_04_061657_make_tanggal_akhir_nullable_in_perizinan_table', 1),
(32, '2026_05_05_000000_add_pnbp_to_perizinan_table', 1),
(33, '2026_05_06_075036_add_no_hp_to_perizinan_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perizinan_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_notifikasi` enum('H-30','H-14','H-7','H-1') NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `status_kirim` enum('pending','terkirim') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `perizinan`
--

CREATE TABLE `perizinan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_izin` varchar(255) NOT NULL,
  `jenis_izin` enum('rekomendasi','izin','dispensasi') NOT NULL,
  `sub_jenis` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `pemohon` varchar(255) NOT NULL,
  `no_hp` varchar(255) DEFAULT NULL,
  `satker_id` bigint(20) UNSIGNED NOT NULL,
  `berkas` varchar(255) DEFAULT NULL,
  `tanggal_terbit` date NOT NULL,
  `tanggal_akhir` date DEFAULT NULL,
  `pnbp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('aktif','hampir_habis','kadaluarsa') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `perizinan`
--

INSERT INTO `perizinan` (`id`, `nomor_izin`, `jenis_izin`, `sub_jenis`, `icon`, `pemohon`, `no_hp`, `satker_id`, `berkas`, `tanggal_terbit`, `tanggal_akhir`, `pnbp`, `status`, `created_at`, `updated_at`) VALUES
(8, 'IZN/BPJN_NTB/2026/006', 'izin', 'Izin Penempatan Jaringan Utilitas', 'ph-wifi-high', 'PT. Maulana Jaya Media', '087758527777', 1, NULL, '2026-05-04', '2026-05-15', 20000000.00, 'hampir_habis', '2026-05-05 21:21:42', '2026-05-06 08:24:02'),
(9, 'IZN/BPJN_NTB/2026/010', 'rekomendasi', 'Akses Jalan Keluar/Masuk', 'ph-car', 'Soetikno Gani Wijaya', NULL, 1, NULL, '2025-06-11', NULL, 0.00, 'aktif', '2026-05-05 23:21:21', '2026-05-05 23:21:21'),
(10, 'PR.03.01-Bb9/920', 'rekomendasi', 'Akses Jalan Keluar/Masuk', 'ph-car', 'Muhammad Arsyad', '087823670989', 1, NULL, '2025-03-14', NULL, 0.00, 'aktif', '2026-05-05 23:29:28', '2026-05-07 02:10:38'),
(11, 'IZN/BPJN_NTB/2026/011', 'rekomendasi', 'Akses Jalan Keluar/Masuk', 'ph-car', 'Sdri. Lily', '082342352020', 1, NULL, '2026-05-07', NULL, 0.00, 'aktif', '2026-05-06 21:10:33', '2026-05-06 21:10:33'),
(12, 'IZN/BPJN_NTB/2026/012', 'rekomendasi', 'Akses Jalan Keluar/Masuk', 'ph-car', 'Baiq Endang Suprihartini, Ssi, Msi, Apt', '081907716934', 1, NULL, '2026-05-07', NULL, 0.00, 'aktif', '2026-05-06 21:14:47', '2026-05-06 21:14:47'),
(13, 'IZN/BPJN_NTB/2026/013', 'izin', 'Izin Penempatan Jaringan Utilitas', 'ph-signpost', 'Dinas PRKP Kab. Sumbawa', '085253000280', 1, NULL, '2026-05-07', '2030-02-07', 0.00, 'aktif', '2026-05-06 21:20:28', '2026-05-06 21:22:02'),
(14, 'IZN/BPJN_NTB/2026/015', 'izin', 'Izin Penempatan Jaringan Utilitas', 'ph-wifi-high', 'Dinas PUPR Kab. Lombok Barat', '085268484294', 1, NULL, '2026-05-07', '2030-07-07', 0.00, 'aktif', '2026-05-06 21:25:11', '2026-05-06 21:25:11'),
(15, 'IZN/BPJN_NTB/2026/016', 'rekomendasi', 'Akses Jalan Keluar/Masuk', 'ph-car', 'I Nengah Sekartana', '087864123950', 1, NULL, '2026-05-07', NULL, 0.00, 'aktif', '2026-05-06 21:27:34', '2026-05-06 21:27:34'),
(16, 'IZN/BPJN_NTB/2026/017', 'izin', 'Izin Penempatan Jaringan Utilitas', 'ph-wifi-high', 'Meyla Kusumadiarti Rr.St (PT. Telekomunikasi Indonesia)', '081393881979', 3, NULL, '2026-05-07', '2030-06-07', 0.00, 'aktif', '2026-05-06 21:32:07', '2026-05-06 21:32:07'),
(17, 'IZN/BPJN_NTB/2026/018', 'rekomendasi', 'Akses Jalan Keluar/Masuk', 'ph-car', 'Yuda Waskita Pandar Widi, ST', '08776588808', 1, NULL, '2026-05-07', NULL, 0.00, 'aktif', '2026-05-06 21:35:11', '2026-05-06 21:35:11'),
(18, 'IZN/BPJN_NTB/2026/019', 'rekomendasi', 'Akses Jalan Keluar/Masuk', 'ph-car', 'PT. Berlian Lombok Properti', '085172211944', 1, NULL, '2026-05-07', NULL, 0.00, 'aktif', '2026-05-06 22:05:19', '2026-05-06 22:05:19'),
(19, 'IZN/BPJN_NTB/2026/020', 'dispensasi', '-', 'ph-truck', 'Abhijeet Vikram Singh (PT. Total Movements Internasional)', '081316742330', 2, NULL, '2026-05-07', '2026-05-30', 0.00, 'aktif', '2026-05-06 22:08:17', '2026-05-06 22:08:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perizinan_geo`
--

CREATE TABLE `perizinan_geo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perizinan_id` bigint(20) UNSIGNED NOT NULL,
  `geojson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`geojson`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `perizinan_geo`
--

INSERT INTO `perizinan_geo` (`id`, `perizinan_id`, `geojson`, `created_at`, `updated_at`) VALUES
(1, 8, '{\"type\": \"FeatureCollection\", \"features\": [{\"type\": \"Feature\", \"properties\": {\"name\": \"kabel pt gelas\", \"styleUrl\": \"#m_ylw-pushpin\"}, \"geometry\": {\"type\": \"LineString\", \"coordinates\": [[116.0970877647831, -8.61937082568872, 0.0], [116.0966376981612, -8.619419570210054, 0.0], [116.0962036418734, -8.619429274883936, 0.0], [116.0957416061162, -8.619448226834004, 0.0], [116.0952917234318, -8.619449411413022, 0.0], [116.0948454605462, -8.619473384269218, 0.0], [116.0944115324398, -8.619487581371667, 0.0], [116.0938559510381, -8.619487828791378, 0.0], [116.0932758926877, -8.619496559079108, 0.0], [116.092822465667, -8.619492993601014, 0.0], [116.0923602956657, -8.619500011274498, 0.0], [116.0917353827398, -8.619498853677753, 0.0], [116.0910481479743, -8.619492489636235, 0.0], [116.0902107552172, -8.619495448131334, 0.0], [116.0894931836107, -8.61950150331765, 0.0], [116.0890215609908, -8.619501658399473, 0.0], [116.0877809715426, -8.619519414820543, 0.0], [116.0870467793972, -8.619516127065985, 0.0], [116.086389496136, -8.619512451157819, 0.0], [116.0856234739041, -8.619491578164025, 0.0]]}}], \"name\": \"/tmp/outputs/\"}', '2026-05-05 21:21:42', '2026-05-06 08:24:02'),
(2, 9, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"Point\",\"coordinates\":[116.1472494498166,-8.588557592092721,0]},\"properties\":{\"name\":\"Soetikno Gani Wijaya\",\"styleUrl\":\"#40\",\"styleHash\":\"34002143\",\"styleMapHash\":{\"normal\":\"#401\",\"highlight\":\"#400\"},\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>12</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Akses Jalan Keluar Masuk</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>Soetikno Gani Wijaya</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Jend. Ahmad Yani</td></tr>\\n<tr><td>Sta Awal</td><td>5+900</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.58785, 116.14709</td></tr>\\n<tr><td>Sta Akhir</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td>Tidak</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>6287730225992</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#e3606d\",\"stroke-opacity\":1,\"No.\":\"12\",\"Jenis Perizinan\":\"Akses Jalan Keluar Masuk\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"Soetikno Gani Wijaya\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Jend. Ahmad Yani\",\"Sta Awal\":\"5+900\",\"Titik Koordinat (Awal)\":\"-8.58785, 116.14709\",\"Sta Akhir\":\"-\",\"Titik Koordinat (Akhir)\":\"-\",\"Jangka Waktu (Waktu)\":\"-\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"Tidak\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"6287730225992\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"118\"}]}', '2026-05-05 23:21:21', '2026-05-05 23:21:21'),
(3, 10, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"Point\",\"coordinates\":[116.0774760090677,-8.619030986823581,0]},\"properties\":{\"name\":\"Muhammad Arsyad\",\"styleUrl\":\"#241\",\"styleHash\":\"448b71d7\",\"styleMapHash\":{\"normal\":\"#24\",\"highlight\":\"#240\"},\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>10</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Akses Jalan Keluar Masuk</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>Muhammad Arsyad</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Dr. Sudjono</td></tr>\\n<tr><td>Sta Awal</td><td>0+180</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.56632, 116.13353</td></tr>\\n<tr><td>Sta Akhir</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td>Tidak</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>+62 852-3812-8034</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#ca36cd\",\"stroke-opacity\":1,\"No.\":\"10\",\"Jenis Perizinan\":\"Akses Jalan Keluar Masuk\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"Muhammad Arsyad\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Dr. Sudjono\",\"Sta Awal\":\"0+180\",\"Titik Koordinat (Awal)\":\"-8.56632, 116.13353\",\"Sta Akhir\":\"-\",\"Titik Koordinat (Akhir)\":\"-\",\"Jangka Waktu (Waktu)\":\"-\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"Tidak\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"+62 852-3812-8034\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"54\"}]}', '2026-05-05 23:29:28', '2026-05-07 02:10:38'),
(4, 11, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"Point\",\"coordinates\":[116.0491728758871,-8.497855008948527,0]},\"properties\":{\"name\":\"Sdri. Lily\",\"styleUrl\":\"#321\",\"styleHash\":\"-7190ee04\",\"styleMapHash\":{\"normal\":\"#32\",\"highlight\":\"#320\"},\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>2</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Akses Jalan Keluar Masuk</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>Sdri. Lily</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Ampenan - Pemenang</td></tr>\\n<tr><td>Sta Awal</td><td>7+425</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.497855008948527, 116.04917287588711</td></tr>\\n<tr><td>Sta Akhir</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>6282342352020</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#8844c8\",\"stroke-opacity\":1,\"No.\":\"2\",\"Jenis Perizinan\":\"Akses Jalan Keluar Masuk\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"Sdri. Lily\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Ampenan - Pemenang\",\"Sta Awal\":\"7+425\",\"Titik Koordinat (Awal)\":\"-8.497855008948527, 116.04917287588711\",\"Sta Akhir\":\"-\",\"Titik Koordinat (Akhir)\":\"-\",\"Jangka Waktu (Waktu)\":\"-\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"6282342352020\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"310\"}]}', '2026-05-06 21:10:33', '2026-05-06 21:10:33'),
(5, 12, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"Point\",\"coordinates\":[116.12374,-8.56546,0]},\"properties\":{\"name\":\"Baiq Endang Suprihartini, Ssi, Msi, Apt\",\"styleUrl\":\"#42\",\"styleHash\":\"772449e5\",\"styleMapHash\":{\"normal\":\"#4\",\"highlight\":\"#41\"},\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>3</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Akses Jalan Keluar Masuk</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>Baiq Endang Suprihartini, Ssi, Msi, Apt</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Jend. Sudirman</td></tr>\\n<tr><td>Sta Awal</td><td>1+600</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.56546, 116.12374</td></tr>\\n<tr><td>Sta Akhir</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>+62 819-0771-6934</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#64ceab\",\"stroke-opacity\":1,\"No.\":\"3\",\"Jenis Perizinan\":\"Akses Jalan Keluar Masuk\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"Baiq Endang Suprihartini, Ssi, Msi, Apt\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Jend. Sudirman\",\"Sta Awal\":\"1+600\",\"Titik Koordinat (Awal)\":\"-8.56546, 116.12374\",\"Sta Akhir\":\"-\",\"Titik Koordinat (Akhir)\":\"-\",\"Jangka Waktu (Waktu)\":\"-\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"+62 819-0771-6934\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"342\"}]}', '2026-05-06 21:14:47', '2026-05-06 21:14:47'),
(6, 13, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"LineString\",\"coordinates\":[[117.4049348620969,-8.475334350485603,0],[117.4050079109864,-8.475424917126197,-0.00002349999476791709],[117.4055767609589,-8.476130178065205,-0.00002349999476791709],[117.4057918877854,-8.476396891803066,-0.00002349999476791709],[117.4060449030499,-8.476735467667659,-0.00002349999476791709],[117.4061291056737,-8.476848142827464,-0.00002349999476791709],[117.4061891111386,-8.476928438796392,-0.00002349999476791709],[117.4064198151224,-8.477237142779876,-0.00002349999476791709],[117.4064200246645,-8.477237426066317,-0.00002349999476791709],[117.4064760137571,-8.477312351283956,-0.00002349999476791709],[117.4066739167679,-8.477571960377647,-0.00002349999476791709],[117.406874747072,-8.477835404180501,-0.00002349999476791709],[117.4072158329444,-8.478297911119684,-0.00002349999476791709],[117.4072498093313,-8.47834398158943,-0.00002349999476791709],[117.4072498749819,-8.47834407242095,-0.00002349999476791709],[117.4076904501542,-8.47894149565343,-0.00002349999476791709],[117.4077535124147,-8.479027006790893,-0.00002349999476791709],[117.4078388967478,-8.479142790907016,-0.00002349999476791709],[117.4080203520576,-8.479388838224274,-0.00002349999476791709],[117.4082990087923,-8.479750264962714,-0.00002349999476791709],[117.408588944824,-8.480126315576964,-0.00002349999476791709],[117.4088502842133,-8.480469227972435,-0.00002349999476791709],[117.409234079789,-8.48097282313722,-0.00002349999476791709],[117.4092944575732,-8.481055042756,-0.00002349999476791709],[117.409307969887,-8.481073443784341,-0.00002349999476791709],[117.4093955359752,-8.481192694787017,-0.00002349999476791709],[117.4094738678248,-8.48129937416672,-0.00002349999476791709],[117.4097407587291,-8.481677218928441,-0.00002349999476791709],[117.4097802731411,-8.481733161256338,-0.00002349999476791709],[117.4098091782511,-8.48177244094535,-0.00002349999476791709],[117.4098548431266,-8.481834577803458,-0.00002349999476791709],[117.409927394134,-8.481925815823669,-0.00002349999476791709],[117.4100535087625,-8.482084422058392,-0.00002349999476791709],[117.4100707091959,-8.482106948277025,-0.00002349999476791709],[117.4102662164128,-8.482362981666192,-0.00002349999476791709],[117.4104524390289,-8.48266353689268,-0.00002349999476791709],[117.4104889020412,-8.4827164709883,-0.00002349999476791709],[117.410583389312,-8.483086136416038,-0.00002349999476791709],[117.4108257368183,-8.484317721985231,-0.00002349999476791709],[117.4108341158019,-8.484373254222245,-0.00002349999476791709],[117.4109982798468,-8.485094238008633,-0.00002349999476791709],[117.4110481841264,-8.485299364374093,-0.00002349999476791709],[117.4110719370203,-8.485397000171359,-0.00002349999476791709],[117.4110720242546,-8.48539738687987,-0.00002349999476791709],[117.4110724478353,-8.485399261067009,-0.00002349999476791709],[117.4111721089058,-8.485839300243464,-0.00002349999476791709],[117.4112399123921,-8.48621620341499,-0.00002349999476791709],[117.4113252940272,-8.486520891925585,-0.00002349999476791709],[117.4113706045698,-8.48661770034652,-0.00002349999476791709],[117.4113709975735,-8.48661853941399,-0.00002349999476791709],[117.4114799342516,-8.486786492302706,-0.00002349999476791709],[117.4119028296519,-8.487019083963498,-0.00002349999476791709],[117.412257324416,-8.487175911339188,-0.00002349999476791709],[117.4124897371117,-8.487286356180332,-0.00002349999476791709],[117.4125536771106,-8.487313217131204,-0.00002349999476791709],[117.4127800652478,-8.487408324034895,-0.00002349999476791709],[117.4129621788615,-8.487484830260648,-0.00002349999476791709],[117.4133813528672,-8.48768558322303,-0.00002349999476791709],[117.4134008339813,-8.487694912789951,-0.00002349999476791709],[117.4134010057519,-8.487694996426914,-0.00002349999476791709],[117.4134759561505,-8.487730882074512,-0.00002349999476791709],[117.4146516641431,-8.48825394486363,-0.00002349999476791709],[117.415161489811,-8.488552711338517,-0.00002349999476791709],[117.4152310352841,-8.488592261723461,-0.00002349999476791709],[117.4154551679218,-8.488740275743119,-0.00002349999476791709],[117.4154965405553,-8.488766254141007,0]]},\"properties\":{\"name\":\"Dinas PRKP Kab. Sumbawa\",\"styleUrl\":\"#3\",\"styleHash\":\"f4bacfb\",\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>5</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Pemanfaatan Lahan</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>Dinas PRKP Kab. Sumbawa</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Proses</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Jl. Garuda</td></tr>\\n<tr><td>Sta Awal</td><td>0+210</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.475342668218902, 117.40492445835487</td></tr>\\n<tr><td>Sta Akhir</td><td>4+100</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-8.488634476837815, 117.4155800239543</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>5 Tahun</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>+62 852-5300-0280</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#9b62ef\",\"stroke-opacity\":1,\"stroke-width\":2.52,\"fill\":\"#9b62ef\",\"fill-opacity\":1,\"No.\":\"5\",\"Jenis Perizinan\":\"Pemanfaatan Lahan\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"Dinas PRKP Kab. Sumbawa\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Proses\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Jl. Garuda\",\"Sta Awal\":\"0+210\",\"Titik Koordinat (Awal)\":\"-8.475342668218902, 117.40492445835487\",\"Sta Akhir\":\"4+100\",\"Titik Koordinat (Akhir)\":\"-8.488634476837815, 117.4155800239543\",\"Jangka Waktu (Waktu)\":\"5 Tahun\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"+62 852-5300-0280\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"54\"}]}', '2026-05-06 21:20:28', '2026-05-06 21:22:02'),
(7, 14, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"Point\",\"coordinates\":[116.13138,-8.66569,0]},\"properties\":{\"name\":\"Dinas PUPR Kab. Lombok Barat\",\"styleUrl\":\"#81\",\"styleHash\":\"-390fc71e\",\"styleMapHash\":{\"normal\":\"#84\",\"highlight\":\"#83\"},\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>7</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Pemanfaatan Lahan</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>Dinas PUPR Kab. Lombok Barat</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Mataram - Gerung</td></tr>\\n<tr><td>Sta Awal</td><td>7+250</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.66569, 116.13138</td></tr>\\n<tr><td>Sta Akhir</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>+62 819-4660-4298</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#dc5810\",\"stroke-opacity\":1,\"No.\":\"7\",\"Jenis Perizinan\":\"Pemanfaatan Lahan\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"Dinas PUPR Kab. Lombok Barat\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Mataram - Gerung\",\"Sta Awal\":\"7+250\",\"Titik Koordinat (Awal)\":\"-8.66569, 116.13138\",\"Sta Akhir\":\"-\",\"Titik Koordinat (Akhir)\":\"-\",\"Jangka Waktu (Waktu)\":\"-\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"+62 819-4660-4298\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"182\"}]}', '2026-05-06 21:25:11', '2026-05-06 21:25:11'),
(8, 15, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"Point\",\"coordinates\":[116.157469,-8.355347,0]},\"properties\":{\"name\":\"I Nengah Sekartana\",\"styleUrl\":\"#200\",\"styleHash\":\"4eb70656\",\"styleMapHash\":{\"normal\":\"#201\",\"highlight\":\"#20\"},\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>8</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Akses Jalan Keluar Masuk</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>I Nengah Sekartana</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Pemenang - Bayan</td></tr>\\n<tr><td>Sta Awal</td><td>9+000</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.355347, 116.157469</td></tr>\\n<tr><td>Sta Akhir</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>6287864123950</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#5bbbca\",\"stroke-opacity\":1,\"No.\":\"8\",\"Jenis Perizinan\":\"Akses Jalan Keluar Masuk\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"I Nengah Sekartana\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Pemenang - Bayan\",\"Sta Awal\":\"9+000\",\"Titik Koordinat (Awal)\":\"-8.355347, 116.157469\",\"Sta Akhir\":\"-\",\"Titik Koordinat (Akhir)\":\"-\",\"Jangka Waktu (Waktu)\":\"-\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"6287864123950\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"214\"}]}', '2026-05-06 21:27:34', '2026-05-06 21:27:34'),
(9, 16, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"LineString\",\"coordinates\":[[118.3439842068363,-8.538178808369036,0],[118.3440227550461,-8.538330587686392,-0.00002349999476791709],[118.3440103722809,-8.53848328087866,-0.00002349999476791709],[118.3439773572693,-8.538619467914083,-0.00002349999476791709],[118.3439129370324,-8.538910965168833,-0.00002349999476791709],[118.3439046299948,-8.539017595085905,-0.00002349999476791709],[118.343878177336,-8.539121896658179,-0.00002349999476791709],[118.3436565430151,-8.539775423197174,-0.00002349999476791709],[118.34336597476,-8.540632204612498,-0.00002349999476791709],[118.3433380031464,-8.540714692229237,-0.00002349999476791709],[118.343164365643,-8.541260659851496,-0.00002349999476791709],[118.3431342041803,-8.541342730182809,-0.00002349999476791709],[118.3430500627103,-8.541571683186593,-0.00002349999476791709],[118.3430499952611,-8.541571865748944,-0.00002349999476791709],[118.3428511371703,-8.542028552275838,-0.00002349999476791709],[118.3427982480408,-8.542131950029441,-0.00002349999476791709],[118.3427047698093,-8.542314711154965,-0.00002349999476791709],[118.3426422444441,-8.542436964993891,-0.00002349999476791709],[118.342488635742,-8.542796800831635,-0.00002349999476791709],[118.3424770956415,-8.542823832653717,-0.00002349999476791709],[118.3423885699766,-8.543032443892116,-0.00002349999476791709],[118.3423464969933,-8.543129010395496,-0.00002349999476791709],[118.3423404859248,-8.543142805096348,-0.00002349999476791709],[118.3422725016746,-8.54329884286727,-0.00002349999476791709],[118.3421874168159,-8.543563769652229,-0.00002349999476791709],[118.3420838041243,-8.543770376301783,-0.00002349999476791709],[118.3420007670219,-8.543935945087759,-0.00002349999476791709],[118.341992867377,-8.543948621931293,-0.00002349999476791709],[118.3419795340284,-8.543970017702126,-0.00002349999476791709],[118.3419744888316,-8.543978115197827,-0.00002349999476791709],[118.3419055342131,-8.544088732708818,-0.00002349999476791709],[118.3418246347991,-8.544218513873266,-0.00002349999476791709],[118.3417811049142,-8.544269925416698,-0.00002349999476791709],[118.341781065344,-8.544269962288856,-0.00002349999476791709],[118.3417750272957,-8.54427447598624,-0.00002349999476791709],[118.3416601919644,-8.544360325268773,-0.00002349999476791709],[118.3412936489827,-8.54450922691921,-0.00002349999476791709],[118.3409537592087,-8.544647301631633,-0.00002349999476791709],[118.3405663843313,-8.544804665003253,-0.00002349999476791709],[118.3404543575826,-8.544855107976778,-0.00002349999476791709],[118.3403527018158,-8.544900879871644,-0.00002349999476791709],[118.3402592820403,-8.544942943861711,-0.00002349999476791709],[118.33995430125,-8.545090224933912,-0.00002349999476791709],[118.3396249704161,-8.545223134839768,-0.00002349999476791709],[118.3395373161942,-8.545258507873758,-0.00002349999476791709],[118.3392659421695,-8.545438161841679,-0.00002349999476791709],[118.3390511687765,-8.545621241327186,-0.00002349999476791709],[118.3388953117693,-8.545754098173068,-0.00002349999476791709],[118.3386880558091,-8.545930768190381,-0.00002349999476791709],[118.3385425940655,-8.546040705813882,-0.00002349999476791709],[118.3385212207778,-8.54605686033579,-0.00002349999476791709],[118.3385210301215,-8.546056979945602,-0.00002349999476791709],[118.3383302191644,-8.546182456954787,-0.00002349999476791709],[118.3382539557555,-8.546215711186054,-0.00002349999476791709],[118.338224046103,-8.546228753154375,-0.00002349999476791709],[118.3382109987386,-8.54624574584444,-0.00002349999476791709],[118.3381860308607,-8.546278268926931,-0.00002349999476791709],[118.338185897761,-8.546278439798131,-0.00002349999476791709],[118.3381667350069,-8.546419237657634,-0.00002349999476791709],[118.3381749889846,-8.546604945861539,-0.00002349999476791709],[118.338287082283,-8.546720308195624,-0.00002349999476791709],[118.3384173841551,-8.546723667163457,-0.00002349999476791709],[118.3385959292588,-8.54672875193028,-0.00002349999476791709],[118.3388022733063,-8.546666848895942,-0.00002349999476791709],[118.3390828986569,-8.546584311816504,-0.00002349999476791709],[118.3394351568071,-8.546486059983641,-0.00002349999476791709],[118.339634883643,-8.54651191459311,-0.00002349999476791709],[118.3400733454086,-8.546584311816499,-0.00002349999476791709],[118.3405269157846,-8.546672910326487,-0.00002349999476791709],[118.3409730020026,-8.546823669976334,-0.00002349999476791709],[118.3412825162752,-8.546963983101307,-0.00002349999476791709],[118.3413606107037,-8.54701964214274,-0.00002349999476791709],[118.3414246082591,-8.54706525215959,-0.00002349999476791709],[118.3414686507577,-8.547112031295176,-0.00002349999476791709],[118.3415267028951,-8.547154232881491,-0.00002349999476791709],[118.3416415553136,-8.547256990317798,-0.00002349999476791709],[118.3417942476064,-8.547335401307684,-0.00002349999476791709],[118.3419657042539,-8.54738889118431,-0.00002349999476791709],[118.3421161428457,-8.547438574230789,-0.00002349999476791709],[118.3421641963207,-8.547450320276022,-0.00002349999476791709],[118.3423018510497,-8.547483969309662,-0.00002349999476791709],[118.3425373286349,-8.547394946319688,-0.00002349999476791709],[118.342673269256,-8.547368417218657,-0.00002349999476791709],[118.3430116715515,-8.547426189666853,-0.00002349999476791709],[118.3433294398019,-8.547529365287918,-0.00002349999476791709],[118.3435976859845,-8.54753761926565,-0.00002349999476791709],[118.3439927033008,-8.547472165707859,-0.00002349999476791709],[118.3440103722809,-8.547467459555547,-0.00002349999476791709],[118.3446187069878,-8.547273928149194,-0.00002349999476791709],[118.3446425651024,-8.547264309899955,-0.00002349999476791709],[118.3448192430872,-8.547193085998462,0]]},\"properties\":{\"name\":\"Meyla Kusumadiarti Rr.St (PT. Telekomunikasi Indonesia)\",\"styleUrl\":\"#6\",\"styleHash\":\"-56ee6342\",\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>9</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Ultilitas kabel fiber optik</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>Meyla Kusumadiarti Rr.St (PT. Telekomunikasi Indonesia)</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Sp. Bomggo - BTS. Kota Dompu</td></tr>\\n<tr><td>Sta Awal</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.5382,118.3439</td></tr>\\n<tr><td>Sta Akhir</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-8.5469,1183447</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>5 Tahun</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>Rp 637.000.00,-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td>Ada</td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td>Ada</td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>+62 81393881979</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td>18/02/2025</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td>10/02/2030</td></tr>\\n</table>\",\"stroke\":\"#3da4e8\",\"stroke-opacity\":1,\"stroke-width\":2.52,\"fill\":\"#3da4e8\",\"fill-opacity\":1,\"No.\":\"9\",\"Jenis Perizinan\":\"Ultilitas kabel fiber optik\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"Meyla Kusumadiarti Rr.St (PT. Telekomunikasi Indonesia)\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Sp. Bomggo - BTS. Kota Dompu\",\"Sta Awal\":\"\",\"Titik Koordinat (Awal)\":\"-8.5382,118.3439\",\"Sta Akhir\":\"\",\"Titik Koordinat (Akhir)\":\"-8.5469,1183447\",\"Jangka Waktu (Waktu)\":\"5 Tahun\",\"Nilai Sewa (Rp)\":\"Rp 637.000.00,-\",\"Jaminan/Garansi/Asuransi\":\"Ada\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"Ada\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"Ada\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"+62 81393881979\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"18/02/2025\",\"Selesai Berlaku\":\"10/02/2030\"},\"id\":\"88\"}]}', '2026-05-06 21:32:07', '2026-05-06 21:32:07'),
(10, 17, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"Point\",\"coordinates\":[116.098637,-8.6266774,0]},\"properties\":{\"name\":\"Yuda Waskita Pandar Widi, ST\",\"styleUrl\":\"#44\",\"styleHash\":\"-3b13e97a\",\"styleMapHash\":{\"normal\":\"#441\",\"highlight\":\"#440\"},\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>11</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Akses Jalan Keluar Masuk</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>Yuda Waskita Pandar Widi, ST</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Mataram - Gerung</td></tr>\\n<tr><td>Sta Awal</td><td>0+760</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.6266774, 116.098637</td></tr>\\n<tr><td>Sta Akhir</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td>Tidak</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>628776588808</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#e3d385\",\"stroke-opacity\":1,\"No.\":\"11\",\"Jenis Perizinan\":\"Akses Jalan Keluar Masuk\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"Yuda Waskita Pandar Widi, ST\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Mataram - Gerung\",\"Sta Awal\":\"0+760\",\"Titik Koordinat (Awal)\":\"-8.6266774, 116.098637\",\"Sta Akhir\":\"-\",\"Titik Koordinat (Akhir)\":\"-\",\"Jangka Waktu (Waktu)\":\"-\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"Tidak\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"628776588808\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"86\"}]}', '2026-05-06 21:35:11', '2026-05-06 21:35:11'),
(11, 18, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"Point\",\"coordinates\":[116.1277859248708,-8.37595330878685,0]},\"properties\":{\"name\":\"PT. Berlian Lombok Properti\",\"styleUrl\":\"#28\",\"styleHash\":\"bfef34\",\"styleMapHash\":{\"normal\":\"#280\",\"highlight\":\"#281\"},\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>14</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Akses Jalan Keluar Masuk</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>PT. Berlian Lombok Properti</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Pemenang - Bayan</td></tr>\\n<tr><td>Sta Awal</td><td>0+230</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.403905176427488, 116.10473822861277</td></tr>\\n<tr><td>Sta Akhir</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td>Tidak</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>6285172211944</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#e02991\",\"stroke-opacity\":1,\"No.\":\"14\",\"Jenis Perizinan\":\"Akses Jalan Keluar Masuk\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"PT. Berlian Lombok Properti\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Pemenang - Bayan\",\"Sta Awal\":\"0+230\",\"Titik Koordinat (Awal)\":\"-8.403905176427488, 116.10473822861277\",\"Sta Akhir\":\"-\",\"Titik Koordinat (Akhir)\":\"-\",\"Jangka Waktu (Waktu)\":\"-\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"Tidak\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"6285172211944\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"406\"}]}', '2026-05-06 22:05:19', '2026-05-06 22:05:19');
INSERT INTO `perizinan_geo` (`id`, `perizinan_id`, `geojson`, `created_at`, `updated_at`) VALUES
(12, 19, '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"geometry\":{\"type\":\"LineString\",\"coordinates\":[[117.3675248988985,-8.463768985179213,0],[117.3675348007905,-8.463584443966848,-0.00002349999476791709],[117.3675160706103,-8.463364742289002,-0.00002349999476791709],[117.3674735659524,-8.46321456090323,-0.00002349999476791709],[117.3673658892245,-8.462996370985698,-0.00002349999476791709],[117.3673092148484,-8.462868859710051,-0.00002349999476791709],[117.3671835858539,-8.46267650911358,-0.00002349999476791709],[117.3665866437586,-8.461994662923304,-0.00002349999476791709],[117.3664482012235,-8.461836526134727,-0.00002349999476791709],[117.36638990537,-8.461772757906434,-0.00002349999476791709],[117.3663525394383,-8.461734332573323,-0.00002349999476791709],[117.3661884176614,-8.461576150818647,-0.00002349999476791709],[117.3658669082313,-8.461253697999721,-0.00002349999476791709],[117.3653625216631,-8.460814487998164,-0.00002349999476791709],[117.3652349429383,-8.46070610170517,-0.00002349999476791709],[117.3648042998786,-8.460375277097345,-0.00002349999476791709],[117.3645974459155,-8.460227930374623,-0.00002349999476791709],[117.3644359312733,-8.460162755606627,-0.00002349999476791709],[117.3642999160084,-8.460157088978418,-0.00002349999476791709],[117.3641724038334,-8.46020526206313,-0.00002349999476791709],[117.3639032097653,-8.460352608785799,-0.00002349999476791709],[117.3637192102734,-8.460524122090527,-0.00002349999476791709],[117.3636961849311,-8.460559646210639,-0.00002349999476791709],[117.3634299963973,-8.460970336911492,-0.00002349999476791709],[117.3633874917394,-8.461078016337359,-0.00002349999476791709],[117.3633081508506,-8.461219694633138,-0.00002349999476791709],[117.36319791735,-8.461268474760235,-0.00002349999476791709],[117.3631541140712,-8.461261711858471,-0.00002349999476791709],[117.3631097972794,-8.461211196039812,-0.00002349999476791709],[117.3630670318181,-8.461159690966891,-0.00002349999476791709],[117.3630332910536,-8.461061011956076,-0.00002349999476791709],[117.363017983693,-8.460925813275587,-0.00002349999476791709],[117.3629964530239,-8.460715311662286,-0.00002349999476791709],[117.3630300121255,-8.460510535133036,-0.00002349999476791709],[117.3630537020668,-8.460440864654114,-0.00002349999476791709],[117.3630948010842,-8.460320002066405,-0.00002349999476791709],[117.3631608023293,-8.460035243431722,-0.00002349999476791709],[117.3631891390676,-8.459879396317026,-0.00002349999476791709],[117.3631609120465,-8.459770329237248,-0.00002349999476791709],[117.3631239660982,-8.45968104184657,-0.00002349999476791709],[117.3630672917222,-8.459596034329474,-0.00002349999476791709],[117.3629397804466,-8.459477020747835,-0.00002349999476791709],[117.3626847551974,-8.459326839362127,-0.00002349999476791709],[117.3626249152078,-8.459285863551713,-0.00002349999476791709],[117.3623843924259,-8.459213496005978,-0.00002349999476791709],[117.3622143728951,-8.459190825895863,-0.00002349999476791709],[117.3619876852836,-8.459190825895863,-0.00002349999476791709],[117.3618601020621,-8.459235562671035,-0.00002349999476791709],[117.3617430094324,-8.459342278023655,-0.00002349999476791709],[117.3617427711121,-8.459342494760277,-0.00002349999476791709],[117.3616108162862,-8.459536527089028,-0.00002349999476791709],[117.3614833023126,-8.459686709374061,-0.00002349999476791709],[117.3609739110173,-8.459988225076463,-0.00002349999476791709],[117.3607332470418,-8.460093513205045,-0.00002349999476791709],[117.3607223832315,-8.460098266122037,-0.00002349999476791709],[117.3602381297859,-8.460339552428373,-0.00002349999476791709],[117.360105563421,-8.460397758349815,-0.00002349999476791709],[117.3600986233529,-8.46039974495221,-0.00002349999476791709],[117.3599610792405,-8.460459807074422,-0.00002349999476791709],[117.3599416745686,-8.460468278688047,-0.00002349999476791709],[117.3599401951839,-8.460469023326711,-0.00002349999476791709],[117.3599401187415,-8.460469061997568,-0.00002349999476791709],[117.3599016592342,-8.460488463072064,-0.00002349999476791709],[117.3598114689243,-8.460533959774349,-0.00002349999476791709],[117.3595564427757,-8.460633136110289,-0.00002349999476791709],[117.3594232630732,-8.460652971557357,-0.00002349999476791709],[117.359295749999,-8.460633136110289,-0.00002349999476791709],[117.359161256387,-8.460557962679779,-0.00002349999476791709],[117.3591092126202,-8.460487192329998,-0.00002349999476791709],[117.3590258427685,-8.46038408505745,-0.00002349999476791709],[117.3590255828644,-8.460383763999461,-0.00002349999476791709],[117.358886133988,-8.460140241079223,-0.00002349999476791709],[117.3587859845856,-8.459897686728816,-0.00002349999476791709],[117.3587841328815,-8.45987802485092,-0.00002349999476791709],[117.3587727177868,-8.45975685109778,-0.00002349999476791709],[117.3587606371938,-8.459628607773997,-0.00002349999476791709],[117.3587606147107,-8.459628382044171,-0.00002349999476791709],[117.3587975651556,-8.459441874342989,-0.00002349999476791709],[117.3588181002751,-8.45933820499413,-0.00002349999476791709],[117.3589646259168,-8.45888120999978,-0.00002349999476791709],[117.3590310561384,-8.458674025086168,-0.00002349999476791709],[117.359031166755,-8.458673677947845,-0.00002349999476791709],[117.3590963010536,-8.458384643036814,-0.00002349999476791709],[117.3591263096315,-8.458186792186662,-0.00002349999476791709],[117.3591455677139,-8.458017708850145,-0.00002349999476791709],[117.3591455047613,-8.457851640041106,-0.00002349999476791709],[117.3591455668145,-8.457850525781115,-0.00002349999476791709],[117.359091272944,-8.457693994282344,-0.00002349999476791709],[117.3589368881269,-8.45740022084345,-0.00002349999476791709],[117.3587535631265,-8.457203782828284,-0.00002349999476791709],[117.3585401378172,-8.456975090627905,-0.00002349999476791709],[117.3582158108111,-8.456730219623863,-0.00002349999476791709],[117.3582155607995,-8.456729984900853,-0.00002349999476791709],[117.3580704183153,-8.456594141406466,-0.00002349999476791709],[117.3579837839245,-8.45651305673214,-0.00002349999476791709],[117.3577287622726,-8.456252365754045,-0.00002349999476791709],[117.3574351408191,-8.455999843318068,-0.00002349999476791709],[117.3571110332476,-8.455815987717592,-0.00002349999476791709],[117.3569654896656,-8.45576322089591,-0.00002349999476791709],[117.3568804039076,-8.455759916786686,-0.00002349999476791709],[117.3568803517469,-8.455759932075189,-0.00002349999476791709],[117.3567653311552,-8.455767817330857,-0.00002349999476791709],[117.3566944906583,-8.455832992098863,-0.00002349999476791709],[117.3566235818129,-8.455947762679045,-0.00002349999476791709],[117.3566234810888,-8.455948080139763,-0.00002349999476791709],[117.3566169241318,-8.455968655728839,-0.00002349999476791709],[117.3565941290159,-8.456040184207096,-0.00002349999476791709],[117.3565782811628,-8.456089913118946,-0.00002349999476791709],[117.3566111351957,-8.456326674936065,-0.00002349999476791709],[117.3566556129663,-8.456647242776008,-0.00002349999476791709],[117.3566630413664,-8.456804457759453,-0.00002349999476791709],[117.3566657438291,-8.456861686117861,-0.00002349999476791709],[117.3566661467254,-8.45687020809357,-0.00002349999476791709],[117.3566091171172,-8.457197875181755,-0.00002349999476791709],[117.3564349400206,-8.457733960154078,-0.00002349999476791709],[117.3563634322267,-8.457954058433007,-0.00002349999476791709],[117.3562127759989,-8.458513591429034,-0.00002349999476791709],[117.3562123811965,-8.458514007815154,-0.00002349999476791709],[117.3561809705753,-8.458547092973896,-0.00002349999476791709],[117.3561451487797,-8.458584825829004,-0.00002349999476791709],[117.3561164253329,-8.458615081720605,-0.00002349999476791709],[117.3560643887606,-8.458636865998526,-0.00002349999476791709],[117.3559612122402,-8.458680054141157,-0.00002349999476791709],[117.3559609541348,-8.45868016205981,-0.00002349999476791709],[117.3558160688566,-8.458649604895294,-0.00002349999476791709],[117.355617717084,-8.458584431925942,-0.00002349999476791709],[117.3552833500467,-8.458394577847459,-0.00002349999476791709],[117.3549886539033,-8.458182058155376,-0.00002349999476791709],[117.3544832969667,-8.457720654684408,-0.00002349999476791709],[117.3544832322154,-8.45772059982579,-0.00002349999476791709],[117.3543260801845,-8.457586661095492,-0.00002349999476791709],[117.3543088158992,-8.457571949086118,-0.00002349999476791709],[117.354133171109,-8.457422237146499,-0.00002349999476791709],[117.3539446012623,-8.457286277639579,-0.00002349999476791709],[117.3539033790378,-8.457261131695807,-0.00002349999476791709],[117.3539032468375,-8.45726101208601,-0.00002349999476791709],[117.3539028142635,-8.45726060918969,-0.00002349999476791709],[117.3536008102293,-8.457017457689455,-0.00002349999476791709],[117.3536002085828,-8.457016506206738,-0.00002349999476791709],[117.3534925048752,-8.456884262698505,-0.00002349999476791709],[117.3533986471299,-8.45669697078853,-0.00002349999476791709],[117.3533883202149,-8.456547248057008,-0.00002349999476791709],[117.3533989960669,-8.45629770417565,-0.00002349999476791709],[117.3533706593286,-8.456107851895805,-0.00002349999476791709],[117.3532393556119,-8.455900310850604,-0.00002349999476791709],[117.3531099665517,-8.455711143854154,-0.00002349999476791709],[117.3529364288731,-8.455485134331406,-0.00002349999476791709],[117.3528104410491,-8.455398267915706,-0.00002349999476791709],[117.3524554480606,-8.455260921654151,-0.00002349999476791709],[117.3523687776969,-8.455224945175022,-0.00002349999476791709],[117.3517692329638,-8.454976064094017,-0.00002349999476791709],[117.351541215255,-8.454853975730378,-0.00002349999476791709],[117.3515331717185,-8.454849669776358,-0.00002349999476791709],[117.3510402497078,-8.454601544125914,-0.00002349999476791709],[117.3507425831038,-8.45442399097437,-0.00002349999476791709],[117.3504421573797,-8.454244792962765,-0.00002349999476791709],[117.3503707709944,-8.454201799073696,-0.00002349999476791709],[117.3502687725859,-8.454140368183403,-0.00002349999476791709],[117.3499800641288,-8.453962920252547,-0.00002349999476791709],[117.3499661696031,-8.453954382988346,-0.00002349999476791709],[117.3497241809263,-8.453805646813118,-0.00002349999476791709],[117.3492084997725,-8.453455673840203,-0.00002349999476791709],[117.3490654518092,-8.453358591126014,-0.00002349999476791709],[117.3486300198596,-8.453090050865121,-0.00002349999476791709],[117.3481182768371,-8.452838082411516,-0.00002349999476791709],[117.3481032959304,-8.452829254666309,-0.00002349999476791709],[117.3481029883623,-8.45282904962091,-0.00002349999476791709],[117.3480815260417,-8.452815272007179,-0.00002349999476791709],[117.3478855062112,-8.452722167893674,-0.00002349999476791709],[117.347602779145,-8.452573878681502,-0.00002349999476791709],[117.3475334099389,-8.45253749570884,-0.00002349999476791709],[117.347158666939,-8.452340812178708,-0.00002349999476791709],[117.3469019320786,-8.452173549969304,-0.00002349999476791709],[117.3468378221079,-8.452131155928038,-0.00002349999476791709],[117.3465039272145,-8.451910359775246,-0.00002349999476791709],[117.3463364239868,-8.451888536826452,-0.00002349999476791709],[117.3462271131908,-8.451888597081053,-0.00002349999476791709],[117.3460769309057,-8.45190276500057,-0.00002349999476791709],[117.3459370431601,-8.45194162290749,-0.00002349999476791709],[117.3456884840364,-8.452049180924908,-0.00002349999476791709],[117.3453269529765,-8.452317257135677,-0.00002349999476791709],[117.3452495159524,-8.452390147187483,-0.00002349999476791709],[117.3448669767297,-8.452701845014076,-0.00002349999476791709],[117.3445779490132,-8.452973871946595,-0.00002349999476791709],[117.3443970809606,-8.453160031610137,-0.00002349999476791709],[117.3443964280527,-8.45316076455759,-0.00002349999476791709],[117.3440132871836,-8.453711420345714,-0.00002349999476791709],[117.3436825282262,-8.454161157811143,-0.00002349999476791709],[117.3435235721552,-8.454450951749996,-0.00002349999476791709],[117.343481655654,-8.454562652044729,-0.00002349999476791709],[117.3434814676956,-8.454563152067744,-0.00002349999476791709],[117.3433254901793,-8.455002741583193,-0.00002349999476791709],[117.3432528951051,-8.455209654901578,-0.00002349999476791709],[117.3431979780044,-8.45534277704746,-0.00002349999476791709],[117.3430931341409,-8.45547595495134,-0.00002349999476791709],[117.3429844870445,-8.455588595037563,-0.00002349999476791709],[117.3429842568181,-8.455588661587392,-0.00002349999476791709],[117.3428522768112,-8.45565447307541,-0.00002349999476791709],[117.3426840100591,-8.455690019678634,-0.00002349999476791709],[117.3425207417389,-8.455696977733286,-0.00002349999476791709],[117.3423451841829,-8.455666012276591,-0.00002349999476791709],[117.3423451122372,-8.455665958317303,-0.00002349999476791709],[117.3421262604187,-8.455500025305872,-0.00002349999476791709],[117.3420531967977,-8.455399447826217,-0.00002349999476791709],[117.3420106921398,-8.455314441208428,-0.00002349999476791709],[117.3419650272642,-8.45515190403711,-0.00002349999476791709],[117.341965018271,-8.455151844681843,-0.00002349999476791709],[117.3419832790052,-8.455054866289007,-0.00002349999476791709],[117.3419872567066,-8.455033737616876,-0.00002349999476791709],[117.3420190819152,-8.454864729823386,-0.00002349999476791709],[117.3420260903318,-8.454838927374574,-0.00002349999476791709],[117.3420956996569,-8.454569198209875,-0.00002349999476791709],[117.342144588602,-8.454221752331925,-0.00002349999476791709],[117.3421438727416,-8.454144156127882,-0.00002349999476791709],[117.3421297048222,-8.453676609387966,-0.00002349999476791709],[117.3420872001642,-8.45332240690351,-0.00002349999476791709],[117.3420747058831,-8.453261931093214,-0.00002349999476791709],[117.3420607466062,-8.453194356034663,-0.00002349999476791709],[117.3420607133313,-8.453194303874008,-0.00002349999476791709],[117.3419653546175,-8.453024876996436,-0.00002349999476791709],[117.3418569719218,-8.452922615086543,-0.00002349999476791709],[117.3416819926299,-8.452769851747238,-0.00002349999476791709],[117.3414864737218,-8.452656509290414,-0.00002349999476791709],[117.3411770547773,-8.452544350341444,-0.00002349999476791709],[117.3409423479111,-8.452503164989018,-0.00002349999476791709],[117.3409302331438,-8.452501039891049,-0.00002349999476791709],[117.3409300649706,-8.452501029099146,-0.00002349999476791709],[117.3409300361923,-8.452501021904594,-0.00002349999476791709],[117.3405566097998,-8.452457064841603,-0.00002349999476791709],[117.3405269150851,-8.4524547023226,-0.00002349999476791709],[117.3405265958259,-8.452454678040912,-0.00002349999476791709],[117.3399194841969,-8.452409982634554,-0.00002349999476791709],[117.3399192791514,-8.452409967346064,-0.00002349999476791709],[117.3399182431325,-8.45240989719894,-0.00002349999476791709],[117.3396172697213,-8.452389157933235,-0.00002349999476791709],[117.339500594377,-8.452375045771698,-0.00002349999476791709],[117.3391997675553,-8.452338662798976,-0.00002349999476791709],[117.3382814068615,-8.452193422288584,-0.00002349999476791709],[117.3382016567811,-8.452179081699224,-0.00002349999476791709],[117.3381979605675,-8.452177989022918,-0.00002349999476791709],[117.3378742280132,-8.452082292163995,-0.00002349999476791709],[117.3378345139518,-8.452068250149582,-0.00002349999476791709],[117.3372837826205,-8.451766535697002,-0.00002349999476791709],[117.3370614890965,-8.451641501154304,-0.00002349999476791709],[117.3366838286958,-8.451436353205116,-0.00002349999476791709],[117.3365089878995,-8.451338442215272,-0.00002349999476791709],[117.336315497862,-8.451261509710836,-0.00002349999476791709],[117.33610801887,-8.451219549142824,-0.00002349999476791709],[117.3360256801583,-8.451233271569668,0]]},\"properties\":{\"name\":\"Abhijeet Vikram Singh (PT. Total Movements Internasional)\",\"styleUrl\":\"#0\",\"styleHash\":\"26cec778\",\"description\":\"<table>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>No.</td><td>15</td></tr>\\n<tr><td>Jenis Perizinan</td><td>Permohonan Dispensasi</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Tahun</td><td>2025</td></tr>\\n<tr><td>Identitas Pemohon</td><td>Abhijeet Vikram Singh (PT. Total Movements Internasional)</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Prosedur Perizinan</td><td>OKSiP</td></tr>\\n<tr><td>Kategori Permohonan</td><td>Permohonan Baru</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Status Pengajuan</td><td>Selesai</td></tr>\\n<tr><td>Surat Permohon</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Undangan Expose dan Survey</td><td>Ada</td></tr>\\n<tr><td>BA Survey Lapangan</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>BA Evaluasi</td><td>Ada</td></tr>\\n<tr><td>Surat Persetujuan Rekomendasi Teknis</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Lokasi/Ruas Jalan (km)</td><td>Sp. Negara - Bts. Kota Sumbawa Besar</td></tr>\\n<tr><td>Sta Awal</td><td>71+600</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Awal)</td><td>-8.46379, 117.36792</td></tr>\\n<tr><td>Sta Akhir</td><td>66+100</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Titik Koordinat (Akhir)</td><td>-8.45114,117.33601</td></tr>\\n<tr><td>Jangka Waktu (Waktu)</td><td>-</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Nilai Sewa (Rp)</td><td>-</td></tr>\\n<tr><td>Jaminan/Garansi/Asuransi</td><td>Ada</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Bayar_Biling  (Sewa Sementara)</td><td></td></tr>\\n<tr><td>Bukti Bayar_Biling</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>SPK Sementara</td><td></td></tr>\\n<tr><td>SPK Final</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Pakta Integritas</td><td>Ada</td></tr>\\n<tr><td>Kontak Pemohon</td><td>6281316742330</td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Bukti Dukung</td><td></td></tr>\\n<tr><td>Mulai Berlaku</td><td></td></tr>\\n<tr style=\\\"background-color:#DDDDFF;\\\"><td>Selesai Berlaku</td><td></td></tr>\\n</table>\",\"stroke\":\"#a5dc4d\",\"stroke-opacity\":1,\"stroke-width\":2.52,\"fill\":\"#a5dc4d\",\"fill-opacity\":1,\"No.\":\"15\",\"Jenis Perizinan\":\"Permohonan Dispensasi\",\"Tahun\":\"2025\",\"Identitas Pemohon\":\"Abhijeet Vikram Singh (PT. Total Movements Internasional)\",\"Prosedur Perizinan\":\"OKSiP\",\"Kategori Permohonan\":\"Permohonan Baru\",\"Status Pengajuan\":\"Selesai\",\"Surat Permohon\":\"Ada\",\"Undangan Expose dan Survey\":\"Ada\",\"BA Survey Lapangan\":\"Ada\",\"BA Evaluasi\":\"Ada\",\"Surat Persetujuan Rekomendasi Teknis\":\"Ada\",\"Lokasi/Ruas Jalan (km)\":\"Sp. Negara - Bts. Kota Sumbawa Besar\",\"Sta Awal\":\"71+600\",\"Titik Koordinat (Awal)\":\"-8.46379, 117.36792\",\"Sta Akhir\":\"66+100\",\"Titik Koordinat (Akhir)\":\"-8.45114,117.33601\",\"Jangka Waktu (Waktu)\":\"-\",\"Nilai Sewa (Rp)\":\"-\",\"Jaminan/Garansi/Asuransi\":\"Ada\",\"Bukti Bayar_Biling  (Sewa Sementara)\":\"\",\"Bukti Bayar_Biling\":\"\",\"SPK Sementara\":\"\",\"SPK Final\":\"\",\"Pakta Integritas\":\"Ada\",\"Kontak Pemohon\":\"6281316742330\",\"Bukti Dukung\":\"\",\"Mulai Berlaku\":\"\",\"Selesai Berlaku\":\"\"},\"id\":\"122\"}]}', '2026-05-06 22:08:17', '2026-05-06 22:08:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perizinan_lokasi`
--

CREATE TABLE `perizinan_lokasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perizinan_id` bigint(20) UNSIGNED NOT NULL,
  `satker_id` bigint(20) UNSIGNED NOT NULL,
  `ppk_id` bigint(20) UNSIGNED NOT NULL,
  `nama_ruas_jalan` text NOT NULL,
  `sta_awal` varchar(255) DEFAULT NULL,
  `sta_akhir` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `perizinan_lokasi`
--

INSERT INTO `perizinan_lokasi` (`id`, `perizinan_id`, `satker_id`, `ppk_id`, `nama_ruas_jalan`, `sta_awal`, `sta_akhir`, `keterangan`, `created_at`, `updated_at`) VALUES
(10, 9, 1, 3, 'JLN. JEND. A. YANI (MATARAM)', '5+900 (R)', '5+900 (R)', NULL, '2026-05-05 23:21:21', '2026-05-05 23:21:21'),
(14, 8, 1, 3, 'JLN. DR. SUJONO (MATARAM)', '10+300', '10+500', NULL, '2026-05-06 08:24:02', '2026-05-06 08:24:02'),
(15, 11, 1, 2, 'AMPENAN - PAMENANG', '7+425', '7+425', NULL, '2026-05-06 21:10:33', '2026-05-06 21:10:33'),
(16, 12, 1, 3, 'JLN. SUDIRMAN (MATARAM)', '1+600', '1+600', NULL, '2026-05-06 21:14:47', '2026-05-06 21:14:47'),
(18, 13, 2, 5, 'SIMPANG NEGARA/SIMPANG JLN. GARUDA - SERING - SP. TERMINAL', '0+210', '0+210', NULL, '2026-05-06 21:22:02', '2026-05-06 21:22:02'),
(19, 14, 1, 1, 'MATARAM - GERUNG', '7+250', '7+250', NULL, '2026-05-06 21:25:11', '2026-05-06 21:25:11'),
(20, 15, 1, 2, 'PEMENANG - BAYAN', '9+000', '9+000', NULL, '2026-05-06 21:27:34', '2026-05-06 21:27:34'),
(21, 16, 3, 7, 'SP. BANGGO - BTS. KOTA DOMPU', '8°41\'43.21\"S 118° 1\'19.86\"E', '8°40\'15.42\"S 118° 2\'52.19\"E', NULL, '2026-05-06 21:32:07', '2026-05-06 21:32:07'),
(22, 17, 1, 1, 'MATARAM - GERUNG', '0+760', '0+760', NULL, '2026-05-06 21:35:11', '2026-05-06 21:35:11'),
(23, 18, 1, 2, 'PEMENANG - BAYAN', '0+230', '0+230', NULL, '2026-05-06 22:05:19', '2026-05-06 22:05:19'),
(24, 19, 2, 5, 'SIMPANG NEGARA - BTS. KOTA SUMBAWA BESAR', '71+600', '66+100', NULL, '2026-05-06 22:08:17', '2026-05-06 22:08:17'),
(26, 10, 1, 3, 'JLN. DR. SUJONO (MATARAM)', '1+840 (L)', '1+840 (L)', NULL, '2026-05-07 02:10:38', '2026-05-07 02:10:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pnbp`
--

CREATE TABLE `pnbp` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perizinan_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ppk`
--

CREATE TABLE `ppk` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_ppk` varchar(255) NOT NULL,
  `satker_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ppk`
--

INSERT INTO `ppk` (`id`, `nama_ppk`, `satker_id`, `created_at`, `updated_at`) VALUES
(1, 'PPK 1.1', 1, '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(2, 'PPK 1.2', 1, '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(3, 'PPK 1.3', 1, '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(4, 'PPK 2.1', 2, '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(5, 'PPK 2.2', 2, '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(6, 'PPK 2.3', 2, '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(7, 'PPK 3.1', 3, '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(8, 'PPK 3.2', 3, '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(9, 'PPK 3.3', 3, '2026-05-05 21:15:12', '2026-05-05 21:15:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ruas_jalan`
--

CREATE TABLE `ruas_jalan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_ruas` varchar(255) NOT NULL,
  `kode_ruas` varchar(255) NOT NULL,
  `panjang_km` decimal(8,2) DEFAULT NULL,
  `satker_id` bigint(20) UNSIGNED NOT NULL,
  `ppk_id` bigint(20) UNSIGNED NOT NULL,
  `geom` geometry DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ruas_jalan`
--

INSERT INTO `ruas_jalan` (`id`, `nama_ruas`, `kode_ruas`, `panjang_km`, `satker_id`, `ppk_id`, `geom`, `created_at`, `updated_at`) VALUES
(1, 'JLN. SUDIRMAN (PRAYA)', 'R-001', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(2, 'KOPANG - BTS. KOTA PRAYA', 'R-002', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(3, 'TANAH AWU - SENGKOL', 'R-003', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(4, 'JLN. TGH. LOPAN (PRAYA)', 'R-004', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(5, 'KURIPAN - SULIN', 'R-005', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(6, 'PRAYA - SP. PENUJAK', 'R-006', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(7, 'SENGKOL - KUTA', 'R-007', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(8, 'BYPASS BIL - MANDALIKA', 'R-008', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(9, 'MATARAM - GERUNG', 'R-009', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(10, 'SULIN - SP. PENUJAK', 'R-010', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(11, 'GERUNG - KURIPAN', 'R-011', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(12, 'SP. PENUJAK - TANAH AWU (BANDARA INTERNASIONAL LOMBOK)', 'R-012', NULL, 1, 1, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(13, 'PEMENANG - BAYAN', 'R-013', NULL, 1, 2, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(14, 'BAYAN - SEMBALUN BUBUNG', 'R-014', NULL, 1, 2, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(15, 'AMPENAN - PAMENANG', 'R-015', NULL, 1, 2, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(16, 'DASAN CERMEN - RUMAK', 'R-016', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(17, 'JLN. SANDUBAYA (MATARAM)', 'R-017', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(18, 'MANTANG - KOPANG', 'R-018', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(19, 'LABUHAN LOMBOK - LABUHAN KAYANGAN', 'R-019', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(20, 'JLN. A. YANI 2  (GERUNG)', 'R-020', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(21, 'LINGKAR KOTA GERUNG / JLN. IMAM BONJOL', 'R-021', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(22, 'CAKRANEGARA (BTS. KOTA MATARAM) - MANTANG', 'R-022', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(23, 'BTS. KOTA GERUNG - LEMBAR', 'R-023', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(24, 'KOPANG - MASBAGIK', 'R-024', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(25, 'RUMAK - BTS. KOTA GERUNG', 'R-025', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(26, 'MASBAGIK - REMPUNG', 'R-026', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(27, 'JLN. GATOT SUBROTO 1  (GERUNG)', 'R-027', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(28, 'REMPUNG - LABUHAN  LOMBOK', 'R-028', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(29, 'PL. POTOTANO - SIMPANG NEGARA', 'R-029', NULL, 2, 4, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(30, 'TALIWANG - JEREWEH', 'R-030', NULL, 2, 4, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(31, 'SIMPANG NEGARA - TALIWANG', 'R-031', NULL, 2, 4, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(32, 'JEREWEH - BENETE (PELABUHAN)', 'R-032', NULL, 2, 4, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(33, 'JLN. HASANUDIN (SUMBAWA BESAR)', 'R-033', NULL, 2, 5, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(34, 'JLN. GARUDA 2 (SUMBAWA BESAR)', 'R-034', NULL, 2, 5, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(35, 'SIMPANG NEGARA - BTS. KOTA SUMBAWA BESAR', 'R-035', NULL, 2, 5, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(36, 'JLN. DR. SUTOMO (SUMBAWA BESAR - PAL IV)', 'R-036', NULL, 2, 5, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(37, 'JLN. DR. SUTOMO (SP. TERMINAL - PAL IV)', 'R-037', NULL, 2, 5, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(38, 'JLN. GARUDA 1 (SUMBAWA BESAR)', 'R-038', NULL, 2, 5, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(39, 'JLN. KARTINI (SUMBAWA BESAR)', 'R-039', NULL, 2, 5, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(40, 'SIMPANG NEGARA/SIMPANG JLN. GARUDA - SERING - SP. TERMINAL', 'R-040', NULL, 2, 5, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(41, 'PAL IV (KM 4.00) - KM 70.00', 'R-041', NULL, 2, 6, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(42, 'KM. 70.00 - BTS. KAB. DOMPU (KM.130. SBW)', 'R-042', NULL, 2, 6, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(43, 'DOMPU - HU\'U', 'R-043', NULL, 3, 7, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(44, 'JLN. ACHMAD YANI 2 (DOMPU)', 'R-044', NULL, 3, 7, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(45, 'JLN. ACHMAD YANI 1 (DOMPU)', 'R-045', NULL, 3, 7, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(46, 'BTS. KAB. DOMPU (KM.130.SBW) - SP. BANGGO', 'R-046', NULL, 3, 7, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(47, 'JLN. SYEH MUHAMAD (DOMPU)', 'R-047', NULL, 3, 7, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(48, 'MADAPRAMA (DOMPU) - JLN. BALIBUNGA', 'R-048', NULL, 3, 7, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(49, 'SP. BANGGO - BTS. KOTA DOMPU', 'R-049', NULL, 3, 7, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(50, 'SP. BANGGO - KEMPO', 'R-050', NULL, 3, 8, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(51, 'DOROPATI - LB. KENANGA', 'R-051', NULL, 3, 8, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(52, 'HODO - DOROPATI', 'R-052', NULL, 3, 8, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(53, 'KEMPO - KESI - HODO', 'R-053', NULL, 3, 8, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(54, 'BTS. KOTA DOMPU - SILA', 'R-054', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(55, 'SILA - TALABIU', 'R-055', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(56, 'TALABIU - BTS. KOTA BIMA', 'R-056', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(57, 'JLN. SUTAMI (RABA)', 'R-057', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(58, 'JLN. SULTAN KAHARUDIN (BIMA)', 'R-058', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(59, 'SONCO TENGGE - KUMBE (BIMA)', 'R-059', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(60, 'JLN. PADOLO III (AKSES PELABUHAN BIMA)', 'R-060', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(61, 'JLN. MARTADINATA (BIMA)', 'R-061', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(62, 'BIMA - RABA (JL. SOEKARNO HATTA)', 'R-062', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(63, 'JLN. SULTAN SALAHUDIN (BIMA)', 'R-063', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(64, 'RABA - SAPE (LABUHAN BAJO)', 'R-064', NULL, 3, 9, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(65, 'JLN. ARYA BANJAR GETAS (MATARAM)', 'R-065', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(66, 'JLN. DR. SUJONO (MATARAM)', 'R-066', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(67, 'JLN. SUDIRMAN (MATARAM)', 'R-067', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(68, 'JLN. ENERGI (MATARAM)', 'R-068', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(69, 'JLN. SALEH SUNGKAR 2 (MATARAM)', 'R-069', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(70, 'JLN. TGH. SALEH HAMBALI (DASAN CERMEN - BENGKEL)', 'R-070', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(71, 'JLN. JEND. A. YANI (MATARAM)', 'R-071', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(72, 'JLN. ADI SUCIPTO / SELAPARANG - REMBIGA (JLN.SUDIRMAN)', 'R-072', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(73, 'JLN. SALEH SUNGKAR 1 (MATARAM)', 'R-073', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(74, 'JLN. TGH FAESAL (MATARAM)', 'R-074', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13'),
(75, 'JLN. ADI SUCIPTO  /  AMPENAN  -  SELAPARANG', 'R-075', NULL, 1, 3, NULL, '2026-05-05 21:15:13', '2026-05-05 21:15:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `satker`
--

CREATE TABLE `satker` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_satker` varchar(255) NOT NULL,
  `kode_satker` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `satker`
--

INSERT INTO `satker` (`id`, `nama_satker`, `kode_satker`, `created_at`, `updated_at`) VALUES
(1, 'Satker PJN Wilayah I Provinsi NTB', 'PJN-I-NTB', '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(2, 'Satker PJN Wilayah II Provinsi NTB', 'PJN-II-NTB', '2026-05-05 21:15:12', '2026-05-05 21:15:12'),
(3, 'Satker PJN Wilayah III Provinsi NTB', 'PJN-III-NTB', '2026-05-05 21:15:12', '2026-05-05 21:15:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'satker',
  `satker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `satker_id`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin', '$2y$12$EWZuoQlO9282IDOT6lMHV.34Pf20a8taNP/c1JM6ZGAU2teB/a6hm', 'superadmin', NULL, '2026-05-05 21:01:56', '2026-05-05 21:01:56'),
(2, 'Herlambang', 'satkerpjn1', '$2y$12$95zvvl82QANDOknNbSECqeau220RSQxG7ziVAkr4PtCfSz/PG9iAy', 'viewer', NULL, '2026-05-05 21:02:28', '2026-05-05 21:02:28');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `data_teknis`
--
ALTER TABLE `data_teknis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_teknis_perizinan_id_foreign` (`perizinan_id`);

--
-- Indeks untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dokumen_perizinan_id_foreign` (`perizinan_id`);

--
-- Indeks untuk tabel `geojson_layer`
--
ALTER TABLE `geojson_layer`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_aktivitas_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifikasi_perizinan_id_foreign` (`perizinan_id`);

--
-- Indeks untuk tabel `perizinan`
--
ALTER TABLE `perizinan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perizinan_nomor_izin_unique` (`nomor_izin`),
  ADD KEY `perizinan_satker_id_foreign` (`satker_id`);

--
-- Indeks untuk tabel `perizinan_geo`
--
ALTER TABLE `perizinan_geo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perizinan_geo_perizinan_id_foreign` (`perizinan_id`);

--
-- Indeks untuk tabel `perizinan_lokasi`
--
ALTER TABLE `perizinan_lokasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perizinan_lokasi_perizinan_id_foreign` (`perizinan_id`),
  ADD KEY `perizinan_lokasi_satker_id_foreign` (`satker_id`),
  ADD KEY `perizinan_lokasi_ppk_id_foreign` (`ppk_id`);

--
-- Indeks untuk tabel `pnbp`
--
ALTER TABLE `pnbp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pnbp_perizinan_id_foreign` (`perizinan_id`);

--
-- Indeks untuk tabel `ppk`
--
ALTER TABLE `ppk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ppk_satker_id_foreign` (`satker_id`);

--
-- Indeks untuk tabel `ruas_jalan`
--
ALTER TABLE `ruas_jalan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ruas_jalan_kode_ruas_unique` (`kode_ruas`),
  ADD KEY `ruas_jalan_satker_id_foreign` (`satker_id`),
  ADD KEY `ruas_jalan_ppk_id_foreign` (`ppk_id`);

--
-- Indeks untuk tabel `satker`
--
ALTER TABLE `satker`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `satker_kode_satker_unique` (`kode_satker`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_satker_id_foreign` (`satker_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `data_teknis`
--
ALTER TABLE `data_teknis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `geojson_layer`
--
ALTER TABLE `geojson_layer`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `perizinan`
--
ALTER TABLE `perizinan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `perizinan_geo`
--
ALTER TABLE `perizinan_geo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `perizinan_lokasi`
--
ALTER TABLE `perizinan_lokasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `pnbp`
--
ALTER TABLE `pnbp`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `ppk`
--
ALTER TABLE `ppk`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `ruas_jalan`
--
ALTER TABLE `ruas_jalan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT untuk tabel `satker`
--
ALTER TABLE `satker`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `data_teknis`
--
ALTER TABLE `data_teknis`
  ADD CONSTRAINT `data_teknis_perizinan_id_foreign` FOREIGN KEY (`perizinan_id`) REFERENCES `perizinan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD CONSTRAINT `dokumen_perizinan_id_foreign` FOREIGN KEY (`perizinan_id`) REFERENCES `perizinan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_perizinan_id_foreign` FOREIGN KEY (`perizinan_id`) REFERENCES `perizinan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `perizinan`
--
ALTER TABLE `perizinan`
  ADD CONSTRAINT `perizinan_satker_id_foreign` FOREIGN KEY (`satker_id`) REFERENCES `satker` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `perizinan_geo`
--
ALTER TABLE `perizinan_geo`
  ADD CONSTRAINT `perizinan_geo_perizinan_id_foreign` FOREIGN KEY (`perizinan_id`) REFERENCES `perizinan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `perizinan_lokasi`
--
ALTER TABLE `perizinan_lokasi`
  ADD CONSTRAINT `perizinan_lokasi_perizinan_id_foreign` FOREIGN KEY (`perizinan_id`) REFERENCES `perizinan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `perizinan_lokasi_ppk_id_foreign` FOREIGN KEY (`ppk_id`) REFERENCES `ppk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `perizinan_lokasi_satker_id_foreign` FOREIGN KEY (`satker_id`) REFERENCES `satker` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pnbp`
--
ALTER TABLE `pnbp`
  ADD CONSTRAINT `pnbp_perizinan_id_foreign` FOREIGN KEY (`perizinan_id`) REFERENCES `perizinan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ppk`
--
ALTER TABLE `ppk`
  ADD CONSTRAINT `ppk_satker_id_foreign` FOREIGN KEY (`satker_id`) REFERENCES `satker` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ruas_jalan`
--
ALTER TABLE `ruas_jalan`
  ADD CONSTRAINT `ruas_jalan_ppk_id_foreign` FOREIGN KEY (`ppk_id`) REFERENCES `ppk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ruas_jalan_satker_id_foreign` FOREIGN KEY (`satker_id`) REFERENCES `satker` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_satker_id_foreign` FOREIGN KEY (`satker_id`) REFERENCES `satker` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
