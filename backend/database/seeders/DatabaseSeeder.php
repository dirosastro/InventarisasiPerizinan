<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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

        // 4. Seed Perizinan (tanpa ppk_id & nama_ruas_jalan)
        $iz1 = DB::table('perizinan')->insertGetId([
            'nomor_izin'     => 'IZN/BPJN-NTB/2025/0045',
            'jenis_izin'     => 'izin',
            'sub_jenis'      => 'Izin Penempatan Jaringan Utilitas',
            'pemohon'        => 'PT. Telekomunikasi Indonesia',
            'satker_id'      => $satker1,
            'tanggal_terbit' => '2025-01-15',
            'tanggal_akhir'  => '2026-01-15',
            'status'         => 'aktif',
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now(),
        ]);

        $iz2 = DB::table('perizinan')->insertGetId([
            'nomor_izin'     => 'IZN/BPJN-NTB/2025/0042',
            'jenis_izin'     => 'izin',
            'sub_jenis'      => 'Izin Penempatan Jaringan Utilitas',
            'pemohon'        => 'PDAM Giri Menang',
            'satker_id'      => $satker1,
            'tanggal_terbit' => '2025-01-12',
            'tanggal_akhir'  => '2025-02-12',
            'status'         => 'hampir_habis',
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now(),
        ]);

        $iz3 = DB::table('perizinan')->insertGetId([
            'nomor_izin'     => 'IZN/BPJN-NTB/2025/0038',
            'jenis_izin'     => 'izin',
            'sub_jenis'      => 'Izin Penempatan Iklan/Reklame',
            'pemohon'        => 'CV. Advertising Jaya',
            'satker_id'      => $satker2,
            'tanggal_terbit' => '2025-01-08',
            'tanggal_akhir'  => '2026-01-08',
            'status'         => 'aktif',
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now(),
        ]);

        $iz4 = DB::table('perizinan')->insertGetId([
            'nomor_izin'     => 'IZN/BPJN-NTB/2025/0035',
            'jenis_izin'     => 'rekomendasi',
            'sub_jenis'      => 'Akses Jalan Keluar/Masuk',
            'pemohon'        => 'PT. PLN Persero',
            'satker_id'      => $satker3,
            'tanggal_terbit' => '2025-01-05',
            'tanggal_akhir'  => '2026-01-05',
            'status'         => 'aktif',
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now(),
        ]);

        // 5. Seed perizinan_lokasi
        $lokasi = [
            // iz1: PT. Telkom mencakup 2 ruas (lintas PPK)
            ['perizinan_id'=>$iz1,'satker_id'=>$satker1,'ppk_id'=>$ppk11,'nama_ruas_jalan'=>'PRAYA - SP. PENUJAK','sta_awal'=>'Km 12+500','sta_akhir'=>'Km 13+000'],
            ['perizinan_id'=>$iz1,'satker_id'=>$satker1,'ppk_id'=>$ppk12,'nama_ruas_jalan'=>'KOPANG - BTS. KOTA PRAYA','sta_awal'=>'Km 25+100','sta_akhir'=>'Km 26+000'],
            // iz2: PDAM 1 ruas
            ['perizinan_id'=>$iz2,'satker_id'=>$satker1,'ppk_id'=>$ppk11,'nama_ruas_jalan'=>'MATARAM - GERUNG','sta_awal'=>'Km 5+000','sta_akhir'=>'Km 6+500'],
            // iz3: Reklame 1 ruas
            ['perizinan_id'=>$iz3,'satker_id'=>$satker2,'ppk_id'=>$ppk21,'nama_ruas_jalan'=>'SIMPANG NEGARA - TALIWANG','sta_awal'=>'Km 2+300','sta_akhir'=>'Km 2+300'],
            // iz4: PLN 3 ruas (lintas satker & ppk)
            ['perizinan_id'=>$iz4,'satker_id'=>$satker3,'ppk_id'=>$ppk31,'nama_ruas_jalan'=>'TANAH AWU - SENGKOL','sta_awal'=>'Km 8+200','sta_akhir'=>'Km 9+000'],
            ['perizinan_id'=>$iz4,'satker_id'=>$satker3,'ppk_id'=>$ppk32,'nama_ruas_jalan'=>'SP. BANGGO - KEMPO','sta_awal'=>'Km 15+000','sta_akhir'=>'Km 17+000'],
            ['perizinan_id'=>$iz4,'satker_id'=>$satker1,'ppk_id'=>$ppk13,'nama_ruas_jalan'=>'REMPUNG - LABUHAN LOMBOK','sta_awal'=>'Km 1+000','sta_akhir'=>'Km 2+000'],
        ];

        foreach ($lokasi as $row) {
            DB::table('perizinan_lokasi')->insert(array_merge($row, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
}
