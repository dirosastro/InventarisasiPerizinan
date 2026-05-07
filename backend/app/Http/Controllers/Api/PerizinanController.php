<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use App\Models\PerizinanLokasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PerizinanController extends Controller
{
    public function index()
    {
        $data = Perizinan::with(['lokasi', 'satker', 'dokumen'])
            ->leftJoin('perizinan_geo', 'perizinan.id', '=', 'perizinan_geo.perizinan_id')
            ->select('perizinan.*', 'perizinan_geo.geojson')
            ->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Perizinan',
            'data'    => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_izin'     => 'required|unique:perizinan,nomor_izin',
            'pemohon'        => 'required|string',
            'no_hp'          => 'nullable|string',
            'jenis_izin'     => 'required|in:rekomendasi,izin,dispensasi',
            'sub_jenis'      => 'nullable|string',
            'icon'           => 'nullable|string',
            'satker_id'      => 'required|integer',
            'tanggal_terbit' => 'required|date',
            'tanggal_akhir'  => 'nullable|date',
            'pnbp'           => 'nullable|numeric',
            'geojson'        => 'nullable|string',
            'dokumen'         => 'nullable|array',
            'dokumen.*'       => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'lokasi'         => 'required|string', // Karena dikirim via FormData, ini akan di-decode
        ]);

        DB::beginTransaction();
        try {
            $geojson = $request->input('geojson');
            
            // Handle Lokasi Data (Decode dari string JSON karena via FormData)
            $lokasiDataRaw = $request->input('lokasi');
            $lokasiData = is_array($lokasiDataRaw) ? $lokasiDataRaw : json_decode($lokasiDataRaw, true);

            if (!$lokasiData) {
                throw new \Exception("Data lokasi tidak valid");
            }

            // Hapus geojson dan lokasi dari data perizinan utama
            $perizinanData = collect($validated)->except(['geojson', 'lokasi', 'dokumen'])->toArray();
            $perizinan = Perizinan::create($perizinanData);

            // Handle Multiple Files Upload (Dokumen Pendukung)
            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public/dokumen', $filename);
                    
                    DB::table('dokumen')->insert([
                        'perizinan_id' => $perizinan->id,
                        'nama_file'    => $file->getClientOriginalName(),
                        'file_path'    => $filename,
                        'tipe_dokumen' => 'lainnya',
                        'ukuran_file'  => round($file->getSize() / 1024), // KB
                        'created_at'   => now(),
                        'updated_at'   => now()
                    ]);
                }
            }

            // Simpan setiap lokasi
            foreach ($lokasiData as $lokasi) {
                PerizinanLokasi::create([
                    'perizinan_id'    => $perizinan->id,
                    'satker_id'       => $lokasi['satker_id'],
                    'ppk_id'          => $lokasi['ppk_id'],
                    'nama_ruas_jalan' => $lokasi['nama_ruas_jalan'],
                    'sta_awal'        => $lokasi['sta_awal'] ?? null,
                    'sta_akhir'       => $lokasi['sta_akhir'] ?? null,
                    'keterangan'      => $lokasi['keterangan'] ?? null,
                ]);
            }

            // Simpan GeoJSON jika ada
            if ($geojson) {
                DB::table('perizinan_geo')->insert([
                    'perizinan_id' => $perizinan->id,
                    'geojson'      => $geojson,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Perizinan Berhasil Disimpan',
                'data'    => $perizinan->load('lokasi')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $perizinan = Perizinan::with('lokasi')->findOrFail($id);
            $geo = DB::table('perizinan_geo')->where('perizinan_id', $id)->first();
            $dokumen = DB::table('dokumen')->where('perizinan_id', $id)->get();
            
            return response()->json([
                'success' => true,
                'data'    => array_merge($perizinan->toArray(), [
                    'geojson' => $geo ? $geo->geojson : null,
                    'dokumen' => $dokumen
                ])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nomor_izin'     => 'required|unique:perizinan,nomor_izin,' . $id,
            'pemohon'        => 'required|string',
            'no_hp'          => 'nullable|string',
            'jenis_izin'     => 'required|in:rekomendasi,izin,dispensasi',
            'sub_jenis'      => 'nullable|string',
            'icon'           => 'nullable|string',
            'satker_id'      => 'required|integer',
            'tanggal_terbit' => 'required|date',
            'tanggal_akhir'  => 'nullable|date',
            'pnbp'           => 'nullable|numeric',
            'geojson'        => 'nullable|string',
            'lokasi'         => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $perizinan = Perizinan::findOrFail($id);
            
            // Update data utama
            $perizinanData = collect($validated)->except(['geojson', 'lokasi'])->toArray();
            $perizinan->update($perizinanData);

            // Update Lokasi (Delete & Re-insert)
            PerizinanLokasi::where('perizinan_id', $id)->delete();
            $lokasiDataRaw = $request->input('lokasi');
            $lokasiData = is_array($lokasiDataRaw) ? $lokasiDataRaw : json_decode($lokasiDataRaw, true);
            
            foreach ($lokasiData as $lokasi) {
                PerizinanLokasi::create([
                    'perizinan_id'    => $perizinan->id,
                    'satker_id'       => $lokasi['satker_id'],
                    'ppk_id'          => $lokasi['ppk_id'],
                    'nama_ruas_jalan' => $lokasi['nama_ruas_jalan'],
                    'sta_awal'        => $lokasi['sta_awal'] ?? null,
                    'sta_akhir'       => $lokasi['sta_akhir'] ?? null,
                ]);
            }

            // Update GeoJSON
            $geojson = $request->input('geojson');
            if ($geojson) {
                DB::table('perizinan_geo')->updateOrInsert(
                    ['perizinan_id' => $id],
                    ['geojson' => $geojson, 'updated_at' => now()]
                );
            }

            // Handle New Dokumen
            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public/dokumen', $filename);
                    
                    DB::table('dokumen')->insert([
                        'perizinan_id' => $perizinan->id,
                        'nama_file'    => $file->getClientOriginalName(),
                        'file_path'    => $filename,
                        'tipe_dokumen' => 'lainnya',
                        'ukuran_file'  => round($file->getSize() / 1024),
                        'created_at'   => now(),
                        'updated_at'   => now()
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data Perizinan Berhasil Diperbarui',
                'data'    => $perizinan->load('lokasi')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $perizinan = Perizinan::findOrFail($id);

            // Hapus file berkas jika ada
            if ($perizinan->berkas) {
                $filePath = 'public/berkas/' . $perizinan->berkas;
                if (\Illuminate\Support\Facades\Storage::exists($filePath)) {
                    \Illuminate\Support\Facades\Storage::delete($filePath);
                }
            }

            // Hapus data GeoJSON terkait
            DB::table('perizinan_geo')->where('perizinan_id', $id)->delete();

            // Hapus data perizinan (Relasi lokasi akan terhapus otomatis karena CASCADE di DB)
            $perizinan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Perizinan Berhasil Dihapus'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
