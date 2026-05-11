<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    public function index()
    {
        return response()->json(
            Mahasiswa::with('dosen')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'nim' => 'required|unique:mahasiswas',
            'program_studi' => 'required',
            'email' => 'required|email|unique:mahasiswas',
            'password' => 'required|min:6',
            'dosen_id' => 'nullable|exists:dosens,id'
        ]);

        $data['password'] = Hash::make($data['password']);

        return response()->json(
            Mahasiswa::create($data),
            201
        );
    }

    public function show($id)
    {
        return response()->json(
            Mahasiswa::with('dosen')->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required',
            'nim' => 'required|unique:mahasiswas,nim,' . $id,
            'program_studi' => 'required',
            'email' => 'required|email|unique:mahasiswas,email,' . $id,
            'dosen_id' => 'nullable|exists:dosens,id'
        ]);

        $mahasiswa->update($data);

        return response()->json([
            'message' => 'Mahasiswa berhasil diperbarui',
            'data' => $mahasiswa
        ]);
    }

    public function destroy($id)
    {
        Mahasiswa::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Mahasiswa berhasil dihapus'
        ]);
    }

    public function dashboard()
    {
        $user = auth('mahasiswa')->user();

        $jadwal = \App\Models\Jadwal::with('pengampu.mataKuliah')
            ->where('kelas', $user->kelas)
            ->where('status', 'aktif')
            ->get();

        $totalSks = $jadwal->sum(function ($j) {
            return $j->pengampu->mataKuliah->sks ?? 0;
        });

        $jumlahMk = $jadwal->count();

        return response()->json([
            'nama' => $user->nama,
            'nim' => $user->nim,
            'program_studi' => $user->program_studi,
            'total_sks' => $totalSks,
            'jumlah_mk' => $jumlahMk,
        ]);
    }

    public function jadwalSaya()
    {
        $user = auth('mahasiswa')->user();

        $jadwal = \App\Models\Jadwal::with([
            'pengampu.mataKuliah',
            'pengampu.dosen',
            'ruangan',
            'waktu'
        ])
        ->where('kelas', $user->kelas)
        ->where('status', 'aktif')
        ->get();

        $result = $jadwal->map(function ($j) {
            return [
                'hari' => $j->waktu->hari ?? '-',
                'jam_mulai' => $j->waktu->jam_mulai,
                'jam_selesai' => $j->waktu->jam_selesai,
                'ruangan' => $j->ruangan->nama_ruangan ?? '-',
                'nama_mk' => $j->pengampu->mataKuliah->nama_mk ?? '-',
                'nama_dosen' => $j->pengampu->dosen->nama ?? '-',
                'sks' => $j->pengampu->mataKuliah->sks ?? 0,
            ];
        });

        return response()->json([
            'data' => $result
        ]);
    }

    public function monitoring(Request $request)
    {
        $hari = $request->hari;

        $data = \App\Models\Jadwal::with([
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

        $ruangans = \App\Models\Ruangan::all();
        $waktus = \App\Models\Waktu::where('hari', $hari)->get();

        $matrix = [];

        foreach ($data as $j) {
            $waktuId = $j->waktu_id;
            $ruanganId = $j->ruangan_id;

            $matrix[$waktuId][$ruanganId] = [
                'kelas' => $j->kelas,
                'nama_mk' => $j->pengampu->mataKuliah->nama_mk ?? '',
                'kode_dosen' => $j->pengampu->dosen->nama ?? '',
            ];
        }

        return response()->json([
            'ruangans' => $ruangans,
            'waktus' => $waktus,
            'matrix' => $matrix
        ]);
    }

}
