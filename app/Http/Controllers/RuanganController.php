<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RuanganController extends Controller
{
    public function index()
    {
        return response()->json(Ruangan::orderBy('id','desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_ruangan' => 'required|string|max:50|unique:ruangans,kode_ruangan',
            'nama_ruangan' => 'required|string|max:255',
            'gedung' => 'nullable|string|max:255',
        ]);

        $ruangan = Ruangan::create($data);

        return response()->json([
            'message' => 'Ruangan berhasil dibuat',
            'data' => $ruangan
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Ruangan::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $data = $request->validate([
            'kode_ruangan' => [
                'required','string','max:50',
                Rule::unique('ruangans','kode_ruangan')->ignore($ruangan->id)
            ],
            'nama_ruangan' => 'required|string|max:255',
            'gedung' => 'nullable|string|max:255',
        ]);

        $ruangan->update($data);

        return response()->json([
            'message' => 'Ruangan berhasil diperbarui',
            'data' => $ruangan
        ]);
    }

    public function destroy($id)
    {
        Ruangan::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Ruangan berhasil dihapus'
        ]);
    }
}
