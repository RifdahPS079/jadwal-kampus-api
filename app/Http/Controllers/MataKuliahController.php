<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MataKuliahImport;

class MataKuliahController extends Controller
{
    public function index()
    {
        return response()->json(MataKuliah::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_mk' => 'required|string|max:50|unique:mata_kuliahs,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:10',
            'program_studi' => 'required|string|max:100',
            'semester' => 'required|string|max:20',
        ]);

        $mk = MataKuliah::create($data);

        return response()->json([
            'message' => 'Mata kuliah berhasil dibuat',
            'data' => $mk
        ], 201);
    }

    public function show($id)
    {
        $mk = MataKuliah::findOrFail($id);
        return response()->json($mk);
    }

    public function update(Request $request, $id)
    {
        $mk = MataKuliah::findOrFail($id);

        $data = $request->validate([
            'kode_mk' => [
                'required','string','max:50',
                Rule::unique('mata_kuliahs','kode_mk')->ignore($mk->id)
            ],
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:10',
            'program_studi' => 'required|string|max:100',
            'semester' => 'required|string|max:20',
        ]);

        $mk->update($data);

        return response()->json([
            'message' => 'Mata kuliah berhasil diperbarui',
            'data' => $mk
        ]);
    }

    public function destroy($id)
    {
        MataKuliah::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Mata kuliah berhasil dihapus'
        ]);
    }

    // ===== Import Excel (Tahap E)
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        Excel::import(new MataKuliahImport, $request->file('file'));

        return response()->json([
            'message' => 'Import mata kuliah berhasil'
        ]);
    }
}
