<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\JadwalImport;
use App\Models\Waktu;
use App\Models\Ruangan;
use App\Models\Jadwal;

// 🔥 TAMBAHAN WAJIB (INI YANG BIKIN ERROR TADI)
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\PengampuMataKuliah;

class AdminMonitoringController extends Controller
{
    public function importJadwal(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new JadwalImport, $request->file('file'));

        $last = Jadwal::latest()->first();

        return back()
            ->with('success', 'Import jadwal berhasil.')
            ->with('new_jadwal_id', optional($last)->id);
    }

    public function index(Request $request)
    {
        $daftarHari = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

        $hariIni = $this->hariIndonesia(Carbon::now()->dayOfWeekIso);
        $hari = $request->query('hari', $hariIni);

        if (!in_array($hari, $daftarHari, true)) {
            $hari = $hariIni;
        }

        $ruangans = Ruangan::orderBy('kode_ruangan')->get();
        $waktus = Waktu::where('hari', $hari)->orderBy('jam_mulai')->get();
        $allWaktus = Waktu::orderByRaw("
            FIELD(hari,
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat')
        ")
        ->orderBy('jam_mulai')
        ->get();

        $jadwals = Jadwal::with([
                'waktu',
                'ruangan',
                'pengampu.dosen',
                'pengampu.dosen2',
                'pengampu.mataKuliah',
            ])
            ->whereIn('waktu_id', $waktus->pluck('id'))
            ->get();

        $matrix = [];

        foreach ($jadwals as $j) {
            if (!$j->waktu_id || !$j->ruangan_id) continue;

            $j->kelas = $j->kelas ?? '-';
            $j->nama_mk = optional(optional($j->pengampu)->mataKuliah)->nama_mk ?? '-';
            $dosen1 = optional(optional($j->pengampu)->dosen)->kode_dosen;
            $dosen2 = optional(optional($j->pengampu)->dosen2)->kode_dosen;

            $j->kode_dosen =
                $dosen2
                    ? $dosen1 . '/' . $dosen2
                    : ($dosen1 ?? '-');
            $j->nama_dosen = optional(optional($j->pengampu)->dosen)->nama ?? '-';

            if ($j->status != 'batal') {
                $matrix[$j->waktu_id][$j->ruangan_id] = $j;
            }
        }

        // 🔥 DATA UNTUK DROPDOWN EDIT
        $dosens = Dosen::all();
        $matakuliahs = MataKuliah::all();
        $pengampus = PengampuMataKuliah::with([
    'dosen',
    'mataKuliah'
])->get()->map(function ($p) {

    return [
        'id' => $p->id,

        'semester' => $p->semester,

        'dosen' => [
            'nama' => optional($p->dosen)->nama
        ],

        'mata_kuliah' => [
            'nama_mk' => optional($p->mataKuliah)->nama_mk,
            'program_studi' => optional($p->mataKuliah)->program_studi,
        ]
    ];
});
        
        $programStudis = MataKuliah::select('program_studi')
            ->whereNotNull('program_studi')
            ->where('program_studi', '!=', '')
            ->distinct()
            ->orderBy('program_studi')
            ->pluck('program_studi');
        $semesters = PengampuMataKuliah::select('semester')->distinct()->pluck('semester');
        $kelasList = \App\Models\Mahasiswa::select('kelas')->distinct()->pluck('kelas');
        $ruangansKosong = $ruangans->filter(function ($r) use ($matrix) {
            return true; 
        });

        $jadwalTerpakai = Jadwal::select(
            'id',
            'ruangan_id',
            'waktu_id',
            'kelas',
            'pengampu_id'
        )->get();
        $used = [];
            foreach ($jadwalTerpakai as $j) {
                $used[$j->waktu_id][] = $j->ruangan_id;
            }
        $waktusKosong = $waktus->filter(function($w) use ($used, $ruangans) {
        $totalRuangan = $ruangans->count();
        $terpakai = isset($used[$w->id]) ? count($used[$w->id]) : 0;
            return $terpakai < $totalRuangan; // masih ada ruang kosong
        });
        
        return view('admin.monitoring', compact(
            'daftarHari',
            'hari',
            'waktus',
            'ruangans',
            'matrix',
            'dosens',
            'matakuliahs',
            'pengampus',
            'programStudis',
            'semesters',
            'kelasList',
            'waktusKosong',
            'jadwalTerpakai',
            'allWaktus'
        ));
    }

    private function hariIndonesia(int $dayOfWeekIso): string
    {
        return match ($dayOfWeekIso) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };
    }
}