<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DasarHukum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DasarHukumController extends Controller
{
    public function index()
    {
        try {
            $data = DasarHukum::orderBy('urutan', 'asc')->orderBy('id', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        }
        if (auth()->user()->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'kategori' => 'required|in:uu,pp,pm,se',
            'nomor' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'ringkasan' => 'required|string',
            'tahun' => 'required|integer',
            'link_file' => 'nullable|file|mimes:pdf|max:10240',
            'sop_file' => 'nullable|file|mimes:pdf|max:10240',
            'urutan' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $folderName = 'Dasar Hukum/' . preg_replace('#[\\/:*?"<>|]#', '_', $validated['nomor']);

            $linkFileId = null;
            $linkFileUrl = null;
            if ($request->hasFile('link_file')) {
                [$linkFileId, $linkFileUrl] = $this->uploadToGDrive($request->file('link_file'), $folderName, 'dokumen_');
            }

            $sopFileId = null;
            $sopFileUrl = null;
            if ($request->hasFile('sop_file')) {
                [$sopFileId, $sopFileUrl] = $this->uploadToGDrive($request->file('sop_file'), $folderName, 'sop_');
            }

            $dasarHukum = DasarHukum::create([
                'kategori' => $validated['kategori'],
                'nomor' => $validated['nomor'],
                'judul' => $validated['judul'],
                'ringkasan' => $validated['ringkasan'],
                'tahun' => $validated['tahun'],
                'link_file_id' => $linkFileId,
                'link_file_url' => $linkFileUrl,
                'sop_file_id' => $sopFileId,
                'sop_file_url' => $sopFileUrl,
                'urutan' => $validated['urutan'] ?? 0,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Dasar Hukum berhasil ditambahkan.',
                'data' => $dasarHukum
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        }
        if (auth()->user()->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $dasarHukum = DasarHukum::findOrFail($id);

        $validated = $request->validate([
            'kategori' => 'required|in:uu,pp,pm,se',
            'nomor' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'ringkasan' => 'required|string',
            'tahun' => 'required|integer',
            'link_file' => 'nullable|file|mimes:pdf|max:10240',
            'sop_file' => 'nullable|file|mimes:pdf|max:10240',
            'urutan' => 'nullable|integer',
            'delete_link_file' => 'nullable|string', // "true" or "false"
            'delete_sop_file' => 'nullable|string', // "true" or "false"
        ]);

        DB::beginTransaction();
        try {
            $folderName = 'Dasar Hukum/' . preg_replace('#[\\/:*?"<>|]#', '_', $validated['nomor']);

            // Hapus file dokumen resmi jika di-request
            if ($request->input('delete_link_file') === 'true' && $dasarHukum->link_file_id) {
                try {
                    Storage::disk('google')->delete($dasarHukum->link_file_id);
                } catch (\Exception $e) {
                    \Log::warning("Gagal menghapus link_file_id: " . $dasarHukum->link_file_id);
                }
                $dasarHukum->link_file_id = null;
                $dasarHukum->link_file_url = null;
            }

            // Hapus file SOP jika di-request
            if ($request->input('delete_sop_file') === 'true' && $dasarHukum->sop_file_id) {
                try {
                    Storage::disk('google')->delete($dasarHukum->sop_file_id);
                } catch (\Exception $e) {
                    \Log::warning("Gagal menghapus sop_file_id: " . $dasarHukum->sop_file_id);
                }
                $dasarHukum->sop_file_id = null;
                $dasarHukum->sop_file_url = null;
            }

            // Upload dokumen resmi baru
            if ($request->hasFile('link_file')) {
                // Hapus file lama jika ada
                if ($dasarHukum->link_file_id) {
                    try { Storage::disk('google')->delete($dasarHukum->link_file_id); } catch (\Exception $e) {}
                }
                [$dasarHukum->link_file_id, $dasarHukum->link_file_url] = $this->uploadToGDrive($request->file('link_file'), $folderName, 'dokumen_');
            }

            // Upload SOP baru
            if ($request->hasFile('sop_file')) {
                // Hapus file lama jika ada
                if ($dasarHukum->sop_file_id) {
                    try { Storage::disk('google')->delete($dasarHukum->sop_file_id); } catch (\Exception $e) {}
                }
                [$dasarHukum->sop_file_id, $dasarHukum->sop_file_url] = $this->uploadToGDrive($request->file('sop_file'), $folderName, 'sop_');
            }

            $dasarHukum->update([
                'kategori' => $validated['kategori'],
                'nomor' => $validated['nomor'],
                'judul' => $validated['judul'],
                'ringkasan' => $validated['ringkasan'],
                'tahun' => $validated['tahun'],
                'urutan' => $validated['urutan'] ?? $dasarHukum->urutan,
                'link_file_id' => $dasarHukum->link_file_id,
                'link_file_url' => $dasarHukum->link_file_url,
                'sop_file_id' => $dasarHukum->sop_file_id,
                'sop_file_url' => $dasarHukum->sop_file_url,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Dasar Hukum berhasil diperbarui.',
                'data' => $dasarHukum
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        }
        if (auth()->user()->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $dasarHukum = DasarHukum::findOrFail($id);

        DB::beginTransaction();
        try {
            // Hapus file-file di GDrive jika ada
            if ($dasarHukum->link_file_id) {
                try {
                    Storage::disk('google')->delete($dasarHukum->link_file_id);
                } catch (\Exception $e) {
                    \Log::warning("Gagal menghapus link_file_id dari GDrive saat hapus data: " . $dasarHukum->link_file_id);
                }
            }
            if ($dasarHukum->sop_file_id) {
                try {
                    Storage::disk('google')->delete($dasarHukum->sop_file_id);
                } catch (\Exception $e) {
                    \Log::warning("Gagal menghapus sop_file_id dari GDrive saat hapus data: " . $dasarHukum->sop_file_id);
                }
            }

            $dasarHukum->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Dasar Hukum berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fungsi tambahan untuk download file dasar hukum/SOP dari Google Drive
    public function downloadFile(Request $request, $id, $type)
    {
        try {
            $dasarHukum = DasarHukum::findOrFail($id);
            $filePath = ($type === 'sop') ? $dasarHukum->sop_file_id : $dasarHukum->link_file_id;
            
            if (!$filePath) {
                return abort(404, 'File tidak ditemukan');
            }

            $disk = 'google';
            if (!Storage::disk($disk)->exists($filePath)) {
                return abort(404, 'File fisik tidak ditemukan di Google Drive');
            }

            $originalName = basename($filePath);
            $stream = Storage::disk($disk)->readStream($filePath);
            $mimeType = Storage::disk($disk)->mimeType($filePath) ?? 'application/pdf';

            return response()->streamDownload(function () use ($stream) {
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }, $originalName, [
                'Content-Type' => $mimeType,
            ]);
        } catch (\Exception $e) {
            \Log::error('Download Dasar Hukum Error: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan saat mengunduh file');
        }
    }

    /**
     * Helper: pindahkan file upload ke folder lokal app, baca isinya,
     * lalu kirim ke Google Drive sebagai string (menghindari open_basedir XAMPP).
     * Mengembalikan [fileId, fileUrl] atau [null, null] jika gagal.
     */
    private function uploadToGDrive(\Illuminate\Http\UploadedFile $file, string $folderName, string $prefix): array
    {
        $filename = $prefix . preg_replace('#[\\/:*?"<>| ]+#', '_', $file->getClientOriginalName());
        $filePath = $folderName . '/' . $filename;

        try {
            // Pindahkan ke folder lokal app terlebih dahulu (keluar dari tmp XAMPP)
            $tempDir = storage_path('app/temp_uploads');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $file->move($tempDir, $filename);
            $tempFilePath = $tempDir . '/' . $filename;

            \Log::info("uploadToGDrive: File dipindahkan ke: " . $tempFilePath . " (exists=" . (file_exists($tempFilePath) ? 'yes' : 'no') . ", size=" . (file_exists($tempFilePath) ? filesize($tempFilePath) : 0) . ")");

            // Baca isi file sebagai string
            $contents = file_get_contents($tempFilePath);

            // Hapus file temp lokal
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }

            if ($contents === false) {
                \Log::error("uploadToGDrive: Gagal membaca file temp: " . $tempFilePath);
                return [null, null];
            }

            \Log::info("uploadToGDrive: Membaca " . strlen($contents) . " bytes, mulai upload ke GDrive: " . $filePath);

            // Upload ke Google Drive sebagai string content
            // throwsExceptions() aktif, sehingga error Google API akan ter-throw sebagai exception
            Storage::disk('google')->put($filePath, $contents);

            // Jika tidak ada exception, berarti sukses
            try {
                $url = Storage::disk('google')->url($filePath);
            } catch (\Exception $e) {
                $url = $filePath;
            }
            \Log::info("uploadToGDrive: Berhasil upload " . $filePath);
            return [$filePath, $url];

        } catch (\League\Flysystem\UnableToWriteFile $e) {
            \Log::error("uploadToGDrive UnableToWriteFile: " . $e->getMessage() . " | Caused by: " . ($e->getPrevious() ? $e->getPrevious()->getMessage() : 'N/A'));
            return [null, null];
        } catch (\Exception $e) {
            \Log::error("uploadToGDrive Exception [" . get_class($e) . "]: " . $e->getMessage());
            return [null, null];
        }
    }
}
