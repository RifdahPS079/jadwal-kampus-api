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

        return back()->with('success', 'Import jadwal berhasil.');
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

        $jadwals = Jadwal::with([
                'waktu',
                'ruangan',
                'pengampu.dosen',
                'pengampu.mataKuliah',
            ])
            ->whereIn('waktu_id', $waktus->pluck('id'))
            ->get();

        $matrix = [];

        foreach ($jadwals as $j) {
            if (!$j->waktu_id || !$j->ruangan_id) continue;

            $j->kelas = $j->kelas ?? '-';
            $j->nama_mk = optional(optional($j->pengampu)->mataKuliah)->nama_mk ?? '-';
            $j->kode_dosen = optional(optional($j->pengampu)->dosen)->kode_dosen ?? '-';
            $j->nama_dosen = optional(optional($j->pengampu)->dosen)->nama ?? '-';

            if ($j->status != 'batal') {
                $matrix[$j->waktu_id][$j->ruangan_id] = $j;
            }
        }

        // 🔥 DATA UNTUK DROPDOWN EDIT
        $dosens = Dosen::all();
        $matakuliahs = MataKuliah::all();
        $pengampus = PengampuMataKuliah::with(['dosen','mataKuliah'])->get();

        return view('admin.monitoring', compact(
            'daftarHari',
            'hari',
            'waktus',
            'ruangans',
            'matrix',
            'dosens',
            'matakuliahs',
            'pengampus'
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