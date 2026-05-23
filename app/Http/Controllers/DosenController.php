<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\Ruangan;
use App\Models\Waktu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DosenController extends Controller
{
    /**
     * GET /api/dosen
     */
    public function index()
    {
        return response()->json(Dosen::all());
    }

    /**
     * POST /api/dosen
     */
    public function store(Request $request)
    {
        // 1) Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nidn' => 'required|string|max:50|unique:dosens,nidn',
            'kode_dosen' => 'required|string|max:50|unique:dosens,kode_dosen',
            'program_studi' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:dosens,email',
            'password' => 'required|string|min:6',
        ]);

        // 2) Hash password
        $validated['password'] = Hash::make($validated['password']);

        // 3) Simpan ke DB
        $dosen = Dosen::create($validated);

        return response()->json([
            'message' => 'Dosen berhasil ditambahkan',
            'data' => $dosen
        ], 201);
    }

    /**
     * GET /api/dosen/{id}
     */
    public function show($id)
    {
        return response()->json(Dosen::findOrFail($id));
    }

    /**
     * PUT /api/dosen/{id}
     */
    public function update(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);

        // password di update bersifat opsional
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nidn' => 'required|string|max:50|unique:dosens,nidn,' . $id,
            'kode_dosen' => 'required|string|max:50|unique:dosens,kode_dosen,' . $id,
            'program_studi' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:dosens,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        // Kalau user kirim password baru, hash dulu
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // biar password lama tidak ketimpa null
        }

        $dosen->update($validated);

        return response()->json([
            'message' => 'Dosen berhasil diperbarui',
            'data' => $dosen
        ]);
    }

    /**
     * DELETE /api/dosen/{id}
     */
    public function destroy($id)
    {
        $dosen = Dosen::findOrFail($id);
        $dosen->delete();

        return response()->json([
            'message' => 'Dosen berhasil dihapus'
        ]);
    }

    public function mataKuliahSaya()
    {
        $dosen = auth('dosen')->user();

        $data = \App\Models\PengampuMataKuliah::with([
            'mataKuliah',
            'dosen',
            'dosen2'
        ])
        ->where(function ($q) use ($dosen) {
            $q->where('dosen_id', $dosen->id)
            ->orWhere('dosen2_id', $dosen->id);
        })
        ->get()
        ->map(function ($p) {
            return [
                'id' => optional($p->mataKuliah)->id,
                'nama_mk' => optional($p->mataKuliah)->nama_mk,
                'kode_mk' => optional($p->mataKuliah)->kode_mk,
                'sks' => optional($p->mataKuliah)->sks,
                'semester' => $p->semester,
                'tahun_ajaran' => $p->tahun_ajaran,
                'dosen_1' => optional($p->dosen)->nama,
                'dosen_2' => optional($p->dosen2)->nama,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
public function monitoring(Request $request)
{
    $hari = $request->hari;

    $data = Jadwal::with([
        'pengampu.mataKuliah',
        'pengampu.dosen',
        'ruangan',
        'waktu'
    ])
    ->where('status', 'aktif')
    ->whereHas('waktu', function ($q) use ($hari) {
        $q->where('hari', $hari);
    })
    ->get();

    $ruangans = Ruangan::orderBy('kode_ruangan')->get();

    $waktus = Waktu::where('hari', $hari)
        ->orderBy('jam_mulai')
        ->get();

    $matrix = new \stdClass();

    foreach ($data as $j) {

        if (!$j->waktu_id || !$j->ruangan_id) {
            continue;
        }

        $waktuId = $j->waktu_id;
        $ruanganId = $j->ruangan_id;

        $matrix[$waktuId][$ruanganId] = [
            'kelas' => $j->kelas ?? '-',
            'nama_mk' => optional($j->pengampu->mataKuliah)->nama_mk ?? '-',
            'kode_dosen' => optional($j->pengampu->dosen)->kode_dosen ?? '-',
        ];
    }

    return response()->json([
        'ruangans' => $ruangans,
        'waktus' => $waktus,
        'matrix' => $matrix
    ]);
}
}
