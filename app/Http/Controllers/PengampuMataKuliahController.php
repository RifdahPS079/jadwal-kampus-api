<?php

namespace App\Http\Controllers;

use App\Models\PengampuMataKuliah;
use Illuminate\Http\Request;

class PengampuMataKuliahController extends Controller
{
    public function index()
    {
        $data = PengampuMataKuliah::with(['dosen','mataKuliah'])
            ->orderBy('id','desc')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'semester' => 'required|string|max:20',
            'tahun_ajaran' => 'required|string|max:20',
        ]);

        $pengampu = PengampuMataKuliah::create($data);

        return response()->json([
            'message' => 'Pengampu berhasil dibuat',
            'data' => $pengampu
        ], 201);
    }

    public function show($id)
    {
        $pengampu = PengampuMataKuliah::with(['dosen','mataKuliah'])->findOrFail($id);
        return response()->json($pengampu);
    }

    public function update(Request $request, $id)
    {
        $pengampu = PengampuMataKuliah::findOrFail($id);

        $data = $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'semester' => 'required|string|max:20',
            'tahun_ajaran' => 'required|string|max:20',
        ]);

        $pengampu->update($data);

        return response()->json([
            'message' => 'Pengampu berhasil diperbarui',
            'data' => $pengampu
        ]);
    }

    public function destroy($id)
    {
        PengampuMataKuliah::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Pengampu berhasil dihapus'
        ]);
    }
}
