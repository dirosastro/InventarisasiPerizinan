<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use App\Models\PerizinanLokasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PerizinanController extends Controller
{
    public function download($id)
    {
        try {
            $doc = DB::table('dokumen')->where('id', $id)->first();
            if (!$doc) {
                return abort(404, 'Dokumen tidak ditemukan');
            }

            $perizinan = Perizinan::find($doc->perizinan_id);
            if (!$perizinan) {
                return abort(404, 'Data perizinan tidak ditemukan');
            }

            $disk = 'google';
            $filePath = $doc->file_id ?? $doc->file_path;

            // Jika path adalah URL lengkap (untuk kasus lama), coba ambil path dari URL jika mungkin, 
            // tapi idealnya kita punya path asli di file_id.
            
            if (!\Illuminate\Support\Facades\Storage::disk($disk)->exists($filePath)) {
                // Cobalah disk lokal jika di google tidak ada
                $disk = 'public';
                if (!\Illuminate\Support\Facades\Storage::disk($disk)->exists($filePath)) {
                     return abort(404, 'File fisik tidak ditemukan di storage');
                }
            }

            $displayFileName = (strpos($doc->nama_file, $perizinan->pemohon) !== false) 
                ? $doc->nama_file 
                : $perizinan->pemohon . '_' . $doc->nama_file;

            $stream = \Illuminate\Support\Facades\Storage::disk($disk)->readStream($filePath);
            $mimeType = \Illuminate\Support\Facades\Storage::disk($disk)->mimeType($filePath) ?? 'application/pdf';

            return response()->streamDownload(function () use ($stream) {
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }, $displayFileName, [
                'Content-Type' => $mimeType,
            ]);
        } catch (\Exception $e) {
            \Log::error('Download Error: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan saat mengunduh file');
        }
    }

    public function index()
    {
        $data = Perizinan::with(['lokasi', 'satker', 'dokumen'])
            ->leftJoin('perizinan_geo', 'perizinan.id', '=', 'perizinan_geo.perizinan_id')
            ->select('perizinan.*', 'perizinan_geo.geojson')
            ->get();

        // Hitung status secara real-time berdasarkan tanggal_akhir
        $now = \Carbon\Carbon::now()->startOfDay();
        $data->each(function ($item) use ($now) {
            if ($item->tanggal_akhir) {
                $tglAkhir = \Carbon\Carbon::parse($item->tanggal_akhir)->startOfDay();
                if ($tglAkhir->lt($now)) {
                    $item->status = 'kadaluarsa';
                } elseif ($tglAkhir->diffInDays($now) <= 90) {
                    $item->status = 'hampir_habis';
                } else {
                    $item->status = 'aktif';
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar Perizinan',
            'data'    => $data
        ], 200);
    }

    public function uploadTemp(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'nomor_izin' => 'required|string',
            'pemohon' => 'required|string',
        ]);

        try {
            $file = $request->file('file');
            $folderName = preg_replace('#[\\/:*?"<>|]#', '_', $request->nomor_izin . ' - ' . $request->pemohon);
            // Randomize filename for security
            $filename = $request->pemohon . '_' . \Illuminate\Support\Str::random(24) . '.' . $file->getClientOriginalExtension();
            $filePath = $folderName . '/' . $filename;

            // Simpan ke Google Drive
            $file->storeAs($folderName, $filename, 'google');
            \Illuminate\Support\Facades\Log::info('Temporary file uploaded to GDrive.', [
                'username' => auth()->user() ? auth()->user()->email : 'guest',
                'filepath' => $filePath,
                'original_name' => $file->getClientOriginalName()
            ]);

            $url = $filePath;
            try {
                $url = \Illuminate\Support\Facades\Storage::disk('google')->url($filePath);
            } catch (\Exception $e) {}

            return response()->json([
                'success' => true,
                'data' => [
                    'nama_file' => $filename,
                    'file_path' => $url,
                    'file_id' => $filePath,
                    'ukuran_file' => round($file->getSize() / 1024)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        \Log::info('Store Request Received', ['all' => $request->all(), 'has_files' => $request->hasFile('dokumen')]);
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
            'panjang'        => 'nullable|numeric',
            'lebar'          => 'nullable|numeric',
            'geojson'        => 'nullable|string',
            'dokumen'         => 'nullable|array',
            'dokumen.*'       => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'uploaded_dokumen' => 'nullable|string', // JSON string metadata
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
            $perizinanData = collect($validated)->except(['geojson', 'lokasi', 'dokumen', 'uploaded_dokumen'])->toArray();
            $perizinan = Perizinan::create($perizinanData);

            // Handle Pre-uploaded Documents Metadata
            if ($request->has('uploaded_dokumen')) {
                $uploadedDocs = json_decode($request->input('uploaded_dokumen'), true);
                if (is_array($uploadedDocs)) {
                    foreach ($uploadedDocs as $doc) {
                        \App\Models\Dokumen::create([
                            'perizinan_id' => $perizinan->id,
                            'nama_file'    => $doc['nama_file'],
                            'file_path'    => $doc['file_path'],
                            'file_id'      => $doc['file_id'],
                            'tipe_dokumen' => 'lainnya',
                            'ukuran_file'  => $doc['ukuran_file'],
                        ]);
                    }
                }
            }
 
            // Handle Multiple Files Upload (Fallback)
            if ($request->hasFile('dokumen')) {
                // Buat nama subfolder: "NomorIzin - NamaPemohon"
                $folderName = preg_replace('#[\\/:*?"<>|]#', '_', $validated['nomor_izin'] . ' - ' . $validated['pemohon']);
                 
                foreach ($request->file('dokumen') as $file) {
                    // Randomize filename for security
                    $filename = $validated['pemohon'] . '_' . \Illuminate\Support\Str::random(24) . '.' . $file->getClientOriginalExtension();
                    $filePath = $folderName . '/' . $filename;
                     
                    // Simpan ke Google Drive dalam subfolder per perizinan
                    $file->storeAs($folderName, $filename, 'google');
                    \Illuminate\Support\Facades\Log::info('Document file uploaded to GDrive.', [
                        'username' => auth()->user() ? auth()->user()->email : 'guest',
                        'filepath' => $filePath,
                        'original_name' => $file->getClientOriginalName()
                    ]);
                     
                    $url = $filePath;
                    try {
                        $url = \Illuminate\Support\Facades\Storage::disk('google')->url($filePath);
                    } catch (\Exception $e) {}
                     
                    \App\Models\Dokumen::create([
                        'perizinan_id' => $perizinan->id,
                        'nama_file'    => $filename,
                        'file_path'    => $url,
                        'file_id'      => $filePath, // Simpan path asli untuk penghapusan
                        'tipe_dokumen' => 'lainnya',
                        'ukuran_file'  => round($file->getSize() / 1024), // KB
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
            \Log::error('Upload Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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
            'panjang'        => 'nullable|numeric',
            'lebar'          => 'nullable|numeric',
            'geojson'        => 'nullable|string',
            'uploaded_dokumen' => 'nullable|string',
            'lokasi'         => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $perizinan = Perizinan::findOrFail($id);
            
            // Update data utama
            $perizinanData = collect($validated)->except(['geojson', 'lokasi', 'uploaded_dokumen'])->toArray();
            $perizinan->update($perizinanData);

            // Handle Pre-uploaded Documents Metadata
            if ($request->has('uploaded_dokumen')) {
                $uploadedDocs = json_decode($request->input('uploaded_dokumen'), true);
                if (is_array($uploadedDocs)) {
                    foreach ($uploadedDocs as $doc) {
                        \App\Models\Dokumen::create([
                            'perizinan_id' => $perizinan->id,
                            'nama_file'    => $doc['nama_file'],
                            'file_path'    => $doc['file_path'],
                            'file_id'      => $doc['file_id'],
                            'tipe_dokumen' => 'lainnya',
                            'ukuran_file'  => $doc['ukuran_file'],
                        ]);
                    }
                }
            }

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

            // Handle Penelusuran Dokumen yang Dihapus
            if ($request->has('deleted_dokumen')) {
                $deletedIds = $request->input('deleted_dokumen');
                foreach ($deletedIds as $docId) {
                    $doc = DB::table('dokumen')->where('id', $docId)->where('perizinan_id', $id)->first();
                    if ($doc) {
                        // Hapus dari Google Drive
                        try {
                            $pathToDelete = $doc->file_id ?? $doc->file_path;
                            if ($pathToDelete) {
                                \Illuminate\Support\Facades\Storage::disk('google')->delete($pathToDelete);
                            }
                        } catch (\Exception $e) {
                            \Log::warning("Gagal menghapus file dari Google Drive: " . ($doc->file_id ?? $doc->file_path));
                        }
                        // Hapus dari Database
                        DB::table('dokumen')->where('id', $docId)->delete();
                    }
                }
            }

            // Handle New Dokumen
            if ($request->hasFile('dokumen')) {
                // Buat nama subfolder: "NomorIzin - NamaPemohon"
                $folderName = preg_replace('#[\\/:*?"<>|]#', '_', $validated['nomor_izin'] . ' - ' . $validated['pemohon']);
                
                foreach ($request->file('dokumen') as $file) {
                    // Randomize filename for security
                    $filename = $validated['pemohon'] . '_' . \Illuminate\Support\Str::random(24) . '.' . $file->getClientOriginalExtension();
                    $filePath = $folderName . '/' . $filename;
                    
                    // Simpan ke Google Drive dalam subfolder per perizinan
                    $file->storeAs($folderName, $filename, 'google');
                    \Illuminate\Support\Facades\Log::info('New document file uploaded to GDrive on update.', [
                        'username' => auth()->user() ? auth()->user()->email : 'guest',
                        'filepath' => $filePath,
                        'original_name' => $file->getClientOriginalName()
                    ]);
                    
                    $url = $filePath;
                    try {
                        $url = \Illuminate\Support\Facades\Storage::disk('google')->url($filePath);
                    } catch (\Exception $e) {}
                    
                    \App\Models\Dokumen::create([
                        'perizinan_id' => $perizinan->id,
                        'nama_file'    => $filename,
                        'file_path'    => $url,
                        'file_id'      => $filePath,
                        'tipe_dokumen' => 'lainnya',
                        'ukuran_file'  => round($file->getSize() / 1024),
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

            \Illuminate\Support\Facades\Log::info('Perizinan deleted.', [
                'deleted_by' => auth()->user() ? auth()->user()->email : 'system',
                'perizinan_id' => $id,
                'nomor_izin' => $perizinan->nomor_izin,
                'pemohon' => $perizinan->pemohon,
                'ip' => request()->ip()
            ]);

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
