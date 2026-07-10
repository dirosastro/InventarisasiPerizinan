<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DasarHukumSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kategori' => 'uu',
                'nomor' => 'UU No. 38 Tahun 2004',
                'judul' => 'Jalan',
                'tahun' => 2004,
                'ringkasan' => 'Undang-undang yang menjadi landasan utama pengelolaan jalan di Indonesia, mengatur tentang fungsi, status, dan kewenangan atas jalan nasional, provinsi, dan kabupaten/kota.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 1,
            ],
            [
                'kategori' => 'uu',
                'nomor' => 'UU No. 2 Tahun 2022',
                'judul' => 'Perubahan Kedua atas UU No. 38 Tahun 2004 tentang Jalan',
                'tahun' => 2022,
                'ringkasan' => 'Pembaruan terhadap UU Jalan yang mengakomodir perkembangan infrastruktur modern, jalan tol, serta integrasi sistem transportasi nasional.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 2,
            ],
            [
                'kategori' => 'uu',
                'nomor' => 'UU No. 26 Tahun 2007',
                'judul' => 'Penataan Ruang',
                'tahun' => 2007,
                'ringkasan' => 'Mengatur tentang penataan ruang wilayah nasional yang berkaitan erat dengan penempatan infrastruktur jalan dan garis sempadan jalan.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 3,
            ],
            [
                'kategori' => 'pp',
                'nomor' => 'PP No. 34 Tahun 2006',
                'judul' => 'Jalan',
                'tahun' => 2006,
                'ringkasan' => 'Peraturan pelaksana UU Jalan yang mengatur secara rinci tentang penyelenggaraan jalan, penggunaan bagian-bagian jalan, pemanfaatan ruang manfaat jalan, dan persyaratan teknis.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 4,
            ],
            [
                'kategori' => 'pp',
                'nomor' => 'PP No. 15 Tahun 2005',
                'judul' => 'Jalan Tol',
                'tahun' => 2005,
                'ringkasan' => 'Mengatur tentang penyelenggaraan jalan tol, termasuk perizinan pemanfaatan bagian jalan tol oleh pihak ketiga.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 5,
            ],
            [
                'kategori' => 'pm',
                'nomor' => 'Permen PU No. 20/PRT/M/2010',
                'judul' => 'Pedoman Pemanfaatan dan Penggunaan Bagian-bagian Jalan',
                'tahun' => 2010,
                'ringkasan' => 'Pedoman teknis yang mengatur prosedur dan persyaratan pemanfaatan bagian-bagian jalan seperti Rumaja, Rumija, dan Ruwasja.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 6,
            ],
            [
                'kategori' => 'pm',
                'nomor' => 'Permen PU No. 11/PRT/M/2011',
                'judul' => 'Pedoman Penyelenggaraan Jalan Khusus',
                'tahun' => 2011,
                'ringkasan' => 'Mengatur tentang penyelenggaraan jalan khusus yang dipergunakan untuk kepentingan sendiri, tidak diperuntukan bagi lalu lintas umum.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 7,
            ],
            [
                'kategori' => 'pm',
                'nomor' => 'Permen PUPR No. 28/PRT/M/2016',
                'judul' => 'Analisis Mengenai Dampak Lalu Lintas',
                'tahun' => 2016,
                'ringkasan' => 'Mengatur tentang kewajiban penyusunan analisis dampak lalu lintas (Andalalin) bagi setiap pengembangan kawasan yang berpotensi mengganggu kelancaran lalu lintas di jalan.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 8,
            ],
            [
                'kategori' => 'pm',
                'nomor' => 'Permen PUPR No. 5 Tahun 2023',
                'judul' => 'Perizinan Berusaha Berbasis Risiko Sektor PUPR',
                'tahun' => 2023,
                'ringkasan' => 'Mengatur perizinan berusaha berbasis risiko di sektor PUPR, termasuk persyaratan dan prosedur persetujuan pemanfaatan jalan oleh pelaku usaha.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 9,
            ],
            [
                'kategori' => 'se',
                'nomor' => 'SE Dirjen Bina Marga No. 07/SE/Db/2021',
                'judul' => 'Pedoman Teknis Perizinan Pemanfaatan Bagian-bagian Jalan Nasional',
                'tahun' => 2021,
                'ringkasan' => 'Surat edaran yang memberikan petunjuk teknis pelaksanaan perizinan pemanfaatan bagian jalan nasional, termasuk alur proses, formulir, dan syarat administrasi.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 10,
            ],
            [
                'kategori' => 'se',
                'nomor' => 'Keputusan Kepala BPJN NTB',
                'judul' => 'SOP Perizinan Pemanfaatan Jalan di Wilayah BPJN NTB',
                'tahun' => 2023,
                'ringkasan' => 'SOP internal BPJN Nusa Tenggara Barat yang mengatur tata cara pengajuan, pemrosesan, dan penerbitan izin pemanfaatan bagian jalan nasional di wilayah NTB.',
                'link_file_id' => null,
                'link_file_url' => null,
                'sop_file_id' => null,
                'sop_file_url' => null,
                'urutan' => 11,
            ],
        ];

        foreach ($data as $val) {
            DB::table('dasar_hukum')->updateOrInsert(
                ['nomor' => $val['nomor']],
                $val
            );
        }
    }
}
