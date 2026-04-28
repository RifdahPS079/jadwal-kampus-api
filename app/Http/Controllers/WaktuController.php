<?php

namespace App\Http\Controllers;

use App\Models\Waktu;
use Illuminate\Http\Request;

class WaktuController extends Controller
{
    public function index()
    {
        return response()->json(Waktu::orderBy('id','desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hari' => 'required|string|max:20',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tanggal' => 'nullable|date',
        ]);

        $waktu = Waktu::create($data);

        return response()->json([
            'message' => 'Waktu berhasil dibuat',
            'data' => $waktu
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Waktu::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $waktu = Waktu::findOrFail($id);

        $data = $request->validate([
            'hari' => 'required|string|max:20',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tanggal' => 'nullable|date',
        ]);

        $waktu->update($data);

        return response()->json([
            'message' => 'Waktu berhasil diperbarui',
            'data' => $waktu
        ]);
    }

    public function destroy($id)
    {
        Waktu::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Waktu berhasil dihapus'
        ]);
    }
}
