<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function getSatker()
    {
        try {
            $data = DB::table('satker')->select('id', 'nama_satker', 'kode_satker')->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data Satker: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPpk(Request $request)
    {
        try {
            $query = DB::table('ppk')->select('id', 'nama_ppk', 'satker_id');

            if ($request->has('satker_id') && $request->satker_id) {
                $query->where('satker_id', $request->satker_id);
            }

            $data = $query->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data PPK: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRuasJalan(Request $request)
    {
        try {
            $query = DB::table('ruas_jalan')->select('id', 'nama_ruas', 'kode_ruas', 'ppk_id', 'satker_id');

            if ($request->has('ppk_id') && $request->ppk_id) {
                $query->where('ppk_id', $request->ppk_id);
            } elseif ($request->has('satker_id') && $request->satker_id) {
                $query->where('satker_id', $request->satker_id);
            }

            $data = $query->orderBy('nama_ruas')->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data Ruas Jalan: ' . $e->getMessage()
            ], 500);
        }
    }
}
