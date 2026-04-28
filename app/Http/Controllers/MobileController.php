<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Jadwal;

class MobileController extends Controller
{
    public function me(Request $request)
    {
        $guard = $request->attributes->get('auth_guard');
        $user  = auth()->user();

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => [
                'role' => $guard,
                'user' => $user,
            ],
        ], 200);
    }

    public function jadwalToday(Request $request)
    {
        $guard = $request->attributes->get('auth_guard');
        $hari  = $request->query('hari');

        // Default hari = hari ini (Indonesia)
        if (!$hari) {
            $hari = Carbon::now()->locale('id')->isoFormat('dddd'); // Senin, Selasa, dst
        }

        // Dosen: ambil jadwal dosen untuk hari itu
        if ($guard === 'dosen') {
            $dosen = auth('dosen')->user();

            // pakai query ringkas (join) yang sama seperti jadwalDosen,
            // tapi tambahkan filter hari default
            $hariOrder = "FIELD(waktus.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')";

            $data = Jadwal::query()
                ->join('waktus', 'waktus.id', '=', 'jadwals.waktu_id')
                ->join('ruangans', 'ruangans.id', '=', 'jadwals.ruangan_id')
                ->join('pengampu_mata_kuliahs as pmk', 'pmk.id', '=', 'jadwals.pengampu_id')
                ->join('mata_kuliahs as mk', 'mk.id', '=', 'pmk.mata_kuliah_id')
                ->where('pmk.dosen_id', $dosen->id)
                ->where('waktus.hari', $hari)
                ->select([
                    'jadwals.id',
                    'waktus.hari',
                    'waktus.jam_mulai',
                    'waktus.jam_selesai',
                    'ruangans.kode_ruangan',
                    'ruangans.nama_ruangan',
                    'mk.kode_mk',
                    'mk.nama_mk',
                    'mk.sks',
                    'pmk.semester',
                    'pmk.tahun_ajaran',
                    'jadwals.program_studi',
                    'jadwals.kelas',
                ])
                ->orderByRaw($hariOrder)
                ->orderBy('waktus.jam_mulai')
                ->get();

            return response()->json([
                'success' => true,
                'message' => $data->isEmpty() ? 'Data kosong' : 'OK',
                'data'    => $data,
            ], 200);
        }

        // Mahasiswa: ambil jadwal prodi+kelas mahasiswa untuk hari itu
        if ($guard === 'mahasiswa') {
            $mhs = auth('mahasiswa')->user();

            // NOTE: kalau kelas mahasiswa belum ada di DB, Flutter wajib kirim ?kelas=A
            $kelas = $request->query('kelas', $mhs->kelas);
            $prodi = $request->query('program_studi', $mhs->program_studi);

            // normalizer sederhana
            $lower = mb_strtolower(trim((string)$prodi));
            if (str_contains($lower, 'sistem informasi') || str_contains($lower, 'istem informasi')) $prodi = 'SI';
            if (str_contains($lower, 'ilmu komputer')) $prodi = 'IK';

            $hariOrder = "FIELD(waktus.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')";

            $q = Jadwal::query()
                ->join('waktus', 'waktus.id', '=', 'jadwals.waktu_id')
                ->join('ruangans', 'ruangans.id', '=', 'jadwals.ruangan_id')
                ->join('pengampu_mata_kuliahs as pmk', 'pmk.id', '=', 'jadwals.pengampu_id')
                ->join('mata_kuliahs as mk', 'mk.id', '=', 'pmk.mata_kuliah_id')
                ->join('dosens', 'dosens.id', '=', 'pmk.dosen_id')
                ->where('waktus.hari', $hari)
                ->when($prodi, fn($qq) => $qq->where('jadwals.program_studi', $prodi))
                ->when($kelas, fn($qq) => $qq->where('jadwals.kelas', $kelas))
                ->select([
                    'jadwals.id',
                    'waktus.hari',
                    'waktus.jam_mulai',
                    'waktus.jam_selesai',
                    'ruangans.kode_ruangan',
                    'ruangans.nama_ruangan',
                    'mk.kode_mk',
                    'mk.nama_mk',
                    'mk.sks',
                    'dosens.nama as nama_dosen',
                    'pmk.semester',
                    'pmk.tahun_ajaran',
                    'jadwals.program_studi',
                    'jadwals.kelas',
                ])
                ->orderByRaw($hariOrder)
                ->orderBy('waktus.jam_mulai');

            $data = $q->get();

            return response()->json([
                'success' => true,
                'message' => $data->isEmpty() ? 'Data kosong' : 'OK',
                'data'    => $data,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Endpoint ini hanya untuk dosen/mahasiswa',
            'errors'  => null,
        ], 403);
    }
}
