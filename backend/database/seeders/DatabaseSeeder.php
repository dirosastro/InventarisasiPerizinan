<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key checks untuk truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('satker')->truncate();
        DB::table('ppk')->truncate();
        DB::table('ruas_jalan')->truncate();
        DB::table('perizinan')->truncate();
        DB::table('perizinan_lokasi')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 1. Seed Satker (3 Satker)
        $satker1 = DB::table('satker')->insertGetId([
            'nama_satker' => 'Satker PJN Wilayah I Provinsi NTB',
            'kode_satker' => 'PJN-I-NTB',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
        $satker2 = DB::table('satker')->insertGetId([
            'nama_satker' => 'Satker PJN Wilayah II Provinsi NTB',
            'kode_satker' => 'PJN-II-NTB',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
        $satker3 = DB::table('satker')->insertGetId([
            'nama_satker' => 'Satker PJN Wilayah III Provinsi NTB',
            'kode_satker' => 'PJN-III-NTB',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        // 2. Seed PPK (9 PPK)
        $ppk11 = DB::table('ppk')->insertGetId(['nama_ppk' => 'PPK 1.1', 'satker_id' => $satker1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $ppk12 = DB::table('ppk')->insertGetId(['nama_ppk' => 'PPK 1.2', 'satker_id' => $satker1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $ppk13 = DB::table('ppk')->insertGetId(['nama_ppk' => 'PPK 1.3', 'satker_id' => $satker1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $ppk21 = DB::table('ppk')->insertGetId(['nama_ppk' => 'PPK 2.1', 'satker_id' => $satker2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $ppk22 = DB::table('ppk')->insertGetId(['nama_ppk' => 'PPK 2.2', 'satker_id' => $satker2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $ppk23 = DB::table('ppk')->insertGetId(['nama_ppk' => 'PPK 2.3', 'satker_id' => $satker2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $ppk31 = DB::table('ppk')->insertGetId(['nama_ppk' => 'PPK 3.1', 'satker_id' => $satker3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $ppk32 = DB::table('ppk')->insertGetId(['nama_ppk' => 'PPK 3.2', 'satker_id' => $satker3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $ppk33 = DB::table('ppk')->insertGetId(['nama_ppk' => 'PPK 3.3', 'satker_id' => $satker3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        // Map PPK name => ID
        $ppkMap    = ['PPK 1.1'=>$ppk11,'PPK 1.2'=>$ppk12,'PPK 1.3'=>$ppk13,'PPK 2.1'=>$ppk21,'PPK 2.2'=>$ppk22,'PPK 2.3'=>$ppk23,'PPK 3.1'=>$ppk31,'PPK 3.2'=>$ppk32,'PPK 3.3'=>$ppk33];
        $satkerMap = ['PPK 1.1'=>$satker1,'PPK 1.2'=>$satker1,'PPK 1.3'=>$satker1,'PPK 2.1'=>$satker2,'PPK 2.2'=>$satker2,'PPK 2.3'=>$satker2,'PPK 3.1'=>$satker3,'PPK 3.2'=>$satker3,'PPK 3.3'=>$satker3];

        // 3. Seed Ruas Jalan dari GeoJSON
        $geojsonPath = 'C:/xampp/htdocs/InventarisasiPerizinan/api/Peta Jalan Nasional.geojson';
        if (file_exists($geojsonPath)) {
            $geojson = json_decode(file_get_contents($geojsonPath), true);
            $kodeCounter = 1;
            foreach ($geojson['features'] as $feature) {
                $ppkName  = $feature['properties']['PPK'] ?? null;
                $namaRuas = $feature['properties']['Nama Ruas'] ?? $feature['properties']['LINK_NAME'] ?? null;
                if (!$ppkName || !$namaRuas || $ppkName === 'SKPD') continue;
                if (!isset($ppkMap[$ppkName])) continue;
                $kodeRuas = 'R-' . str_pad($kodeCounter++, 3, '0', STR_PAD_LEFT);
                DB::table('ruas_jalan')->insertOrIgnore([
                    'nama_ruas'  => $namaRuas,
                    'kode_ruas'  => $kodeRuas,
                    'panjang_km' => null,
                    'satker_id'  => $satkerMap[$ppkName],
                    'ppk_id'     => $ppkMap[$ppkName],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // 4. Seed Perizinan (Lebih Lengkap)
        $izData = [
            [
                'nomor_izin'     => 'IZN/BPJN-NTB/2025/0045',
                'jenis_izin'     => 'izin',
                'sub_jenis'      => 'Izin Penempatan Jaringan Utilitas',
                'pemohon'        => 'PT. Telekomunikasi Indonesia',
                'satker_id'      => $satker1,
                'tanggal_terbit' => '2025-01-15',
                'tanggal_akhir'  => '2026-01-15',
                'status'         => 'aktif',
                'icon'           => 'ph-wifi-high',
                'pnbp'           => 15000000,
            ],
            [
                'nomor_izin'     => 'IZN/BPJN-NTB/2025/0042',
                'jenis_izin'     => 'izin',
                'sub_jenis'      => 'Izin Penempatan Jaringan Utilitas',
                'pemohon'        => 'PDAM Giri Menang',
                'satker_id'      => $satker1,
                'tanggal_terbit' => '2025-01-12',
                'tanggal_akhir'  => '2025-02-12',
                'status'         => 'hampir_habis',
                'icon'           => 'ph-drop',
                'pnbp'           => 5000000,
            ],
            [
                'nomor_izin'     => 'IZN/BPJN-NTB/2025/0038',
                'jenis_izin'     => 'izin',
                'sub_jenis'      => 'Izin Penempatan Iklan/Reklame',
                'pemohon'        => 'CV. Advertising Jaya',
                'satker_id'      => $satker2,
                'tanggal_terbit' => '2025-01-08',
                'tanggal_akhir'  => '2026-01-08',
                'status'         => 'aktif',
                'icon'           => 'ph-signpost',
                'pnbp'           => 12500000,
            ],
            [
                'nomor_izin'     => 'REK/BPJN-NTB/2025/0012',
                'jenis_izin'     => 'rekomendasi',
                'sub_jenis'      => 'Akses Jalan Keluar/Masuk',
                'pemohon'        => 'SPBU 54.832.01 Mataram',
                'satker_id'      => $satker1,
                'tanggal_terbit' => '2025-02-20',
                'tanggal_akhir'  => '2030-02-20',
                'status'         => 'aktif',
                'icon'           => 'ph-car',
                'pnbp'           => 0,
            ],
            [
                'nomor_izin'     => 'DIS/BPJN-NTB/2025/0005',
                'jenis_izin'     => 'dispensasi',
                'sub_jenis'      => '-',
                'pemohon'        => 'PT. Transportasi Maju',
                'satker_id'      => $satker3,
                'tanggal_terbit' => '2025-03-01',
                'tanggal_akhir'  => '2025-03-07',
                'status'         => 'aktif',
                'icon'           => 'ph-truck',
                'pnbp'           => 2500000,
            ],
            [
                'nomor_izin'     => 'IZN/BPJN-NTB/2024/0099',
                'jenis_izin'     => 'izin',
                'sub_jenis'      => 'Izin Penempatan Jaringan Utilitas',
                'pemohon'        => 'PT. PLN (Persero) UIW NTB',
                'satker_id'      => $satker2,
                'tanggal_terbit' => '2024-05-10',
                'tanggal_akhir'  => '2025-05-10',
                'status'         => 'hampir_habis',
                'icon'           => 'ph-lightning',
                'pnbp'           => 8750000,
            ],
            [
                'nomor_izin'     => 'IZN/BPJN-NTB/2023/0150',
                'jenis_izin'     => 'izin',
                'sub_jenis'      => 'Izin Penempatan Iklan/Reklame',
                'pemohon'        => 'Bank NTB Syariah',
                'satker_id'      => $satker1,
                'tanggal_terbit' => '2023-12-01',
                'tanggal_akhir'  => '2024-12-01',
                'status'         => 'kadaluarsa',
                'icon'           => 'ph-signpost',
                'pnbp'           => 20000000,
            ],
        ];

        foreach ($izData as $data) {
            $id = DB::table('perizinan')->insertGetId(array_merge($data, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));

            // Seed Lokasi Acak untuk setiap izin
            if ($data['pemohon'] === 'PT. Telekomunikasi Indonesia') {
                DB::table('perizinan_lokasi')->insert([
                    ['perizinan_id'=>$id,'satker_id'=>$satker1,'ppk_id'=>$ppk11,'nama_ruas_jalan'=>'PRAYA - SP. PENUJAK','sta_awal'=>'Km 12+500','sta_akhir'=>'Km 13+000','created_at'=>now(),'updated_at'=>now()],
                    ['perizinan_id'=>$id,'satker_id'=>$satker1,'ppk_id'=>$ppk12,'nama_ruas_jalan'=>'KOPANG - BTS. KOTA PRAYA','sta_awal'=>'Km 25+100','sta_akhir'=>'Km 26+000','created_at'=>now(),'updated_at'=>now()],
                ]);
            } elseif ($data['jenis_izin'] === 'dispensasi') {
                DB::table('perizinan_lokasi')->insert([
                    ['perizinan_id'=>$id,'satker_id'=>$satker3,'ppk_id'=>$ppk31,'nama_ruas_jalan'=>'TANAH AWU - SENGKOL','sta_awal'=>'Km 0+000','sta_akhir'=>'Km 15+000','created_at'=>now(),'updated_at'=>now()],
                ]);
            } elseif ($data['sub_jenis'] === 'Akses Jalan Keluar/Masuk') {
                DB::table('perizinan_lokasi')->insert([
                    ['perizinan_id'=>$id,'satker_id'=>$satker1,'ppk_id'=>$ppk11,'nama_ruas_jalan'=>'MATARAM - GERUNG','sta_awal'=>'Km 4+200','sta_akhir'=>'Km 4+200','created_at'=>now(),'updated_at'=>now()],
                ]);
            } else {
                DB::table('perizinan_lokasi')->insert([
                    ['perizinan_id'=>$id,'satker_id'=>$data['satker_id'],'ppk_id'=>$ppk11,'nama_ruas_jalan'=>'RUAS JALAN CONTOH','sta_awal'=>'Km 1+000','sta_akhir'=>'Km 1+500','created_at'=>now(),'updated_at'=>now()],
                ]);
            }
        }
    }
}
