<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\JadwalImport;
use App\Models\Waktu;
use App\Models\Ruangan;
use App\Models\Jadwal;
use App\Models\PeriodeKuliah;
use App\Models\Notifikasi;
use App\Models\JadwalPertemuan;
use Illuminate\Support\Facades\DB;

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

    public function hapusJadwalMassal(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:selected,all'],
            'ids' => ['nullable', 'array'],
            'ids.*' => ['integer', 'exists:jadwals,id'],
        ]);

        DB::transaction(function () use ($request) {

            if ($request->type === 'all') {
                $jadwalIds = Jadwal::pluck('id')->toArray();
            } else {
                $jadwalIds = $request->ids ?? [];
            }

            if (count($jadwalIds) === 0) {
                return;
            }

            // hapus riwayat pertemuan yang terkait jadwal tersebut
            JadwalPertemuan::whereIn('jadwal_id', $jadwalIds)->delete();

            // hapus jadwal
            Jadwal::whereIn('id', $jadwalIds)->delete();
        });

        return response()->json([
            'success' => true,
            'message' => $request->type === 'all'
                ? 'Semua jadwal berhasil dihapus'
                : 'Jadwal terpilih berhasil dihapus',
        ]);
    }
    
    private function pertemuanSaatIni()
    {
        $periode = PeriodeKuliah::where('aktif', 1)->latest()->first();

        if (!$periode) return 1;

        $mulai = Carbon::parse($periode->tanggal_mulai);
        $hariIni = Carbon::now();

        $pertemuan = floor($mulai->diffInDays($hariIni) / 7) + 1;

        return max(1, min($pertemuan, $periode->jumlah_pertemuan ?? 16));
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

       $pertemuanKe = $this->pertemuanSaatIni();

        $jadwals = Jadwal::with([
                'waktu',
                'ruangan',
                'pengampu.dosen',
                'pengampu.dosen2',
                'pengampu.mataKuliah',
            ])

            ->get();

        $matrix = [];

        foreach ($jadwals as $j) {
        $jp = JadwalPertemuan::with(['waktu', 'ruangan'])
            ->where('jadwal_id', $j->id)
            ->where('pertemuan_ke', $pertemuanKe)
            ->first();

        if ($jp && $jp->status === 'batal') {
            continue;
        }

        $waktuTampil = $j->waktu;
        $ruanganTampil = $j->ruangan;
        $waktuId = $j->waktu_id;
        $ruanganId = $j->ruangan_id;

        if ($jp && $jp->status === 'pindah') {
            $waktuTampil = $jp->waktu;
            $ruanganTampil = $jp->ruangan;
            $waktuId = $jp->waktu_id;
            $ruanganId = $jp->ruangan_id;
        }

        if (!$waktuTampil || !$ruanganTampil) continue;
        if ($waktuTampil->hari !== $hari) continue;

        $j->kelas = $j->kelas ?? '-';
        $j->nama_mk = optional(optional($j->pengampu)->mataKuliah)->nama_mk ?? '-';

        $dosen1 = optional(optional($j->pengampu)->dosen)->kode_dosen;
        $dosen2 = optional(optional($j->pengampu)->dosen2)->kode_dosen;

        $j->kode_dosen = $dosen2
            ? $dosen1 . '/' . $dosen2
            : ($dosen1 ?? '-');

        $j->nama_dosen = optional(optional($j->pengampu)->dosen)->nama ?? '-';

        $matrix[$waktuId][$ruanganId] = $j;
    }

        // 🔥 DATA UNTUK DROPDOWN EDIT
        $dosens = Dosen::all();
        $matakuliahs = MataKuliah::all();
        $pengampus = PengampuMataKuliah::with([
            'dosen',
            'dosen2',
            'mataKuliah'
        ])->get()->map(function ($p) {

            $namaDosen1 = optional($p->dosen)->nama;
            $namaDosen2 = optional($p->dosen2)->nama;

            return [
                'id' => $p->id,

                'semester' => $p->semester,

                'nama_pengampu' => $namaDosen2
                    ? $namaDosen1 . ' / ' . $namaDosen2
                    : $namaDosen1,

                'mata_kuliah' => [
                    'nama_mk' => optional($p->mataKuliah)->nama_mk,
                    'program_studi' => trim(optional($p->mataKuliah)->program_studi ?? ''),
                ]
            ];
        });
                
        $programStudis = MataKuliah::whereNotNull('program_studi')
            ->whereRaw("TRIM(program_studi) != ''")
            ->selectRaw("TRIM(program_studi) as program_studi")
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
        
        $periodeAktif = PeriodeKuliah::where('aktif', true)->latest()->first();

        $permohonanMenunggu = JadwalPertemuan::with([
            'jadwal.pengampu.dosen',
            'jadwal.pengampu.dosen2',
            'jadwal.pengampu.mataKuliah',
            'jadwal.waktu',
            'jadwal.ruangan',
        ])
        ->where('status', 'menunggu')
        ->latest()
        ->get();

        $jumlahPermohonanMenunggu = $permohonanMenunggu->count();

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
            'allWaktus',
            'periodeAktif',
            'permohonanMenunggu',
            'jumlahPermohonanMenunggu'
        ));
    }

    public function riwayatPertemuan(Request $request)
    {
        $periodeAktif = PeriodeKuliah::where('aktif', true)->latest()->first();

        $query = JadwalPertemuan::with([
            'jadwal.pengampu.dosen',
            'jadwal.pengampu.dosen2',
            'jadwal.pengampu.mataKuliah',
            'jadwal.waktu',
            'jadwal.ruangan',
            'waktu',
            'ruangan',
        ])
        ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('pertemuan_ke')) {
            $query->where('pertemuan_ke', $request->pertemuan_ke);
        }

        $riwayats = $query->get();

        $totalPindah = JadwalPertemuan::where('status', 'pindah')->count();
        $totalBatal = JadwalPertemuan::where('status', 'batal')->count();
        $totalRiwayat = JadwalPertemuan::count();

        $rekapDosen = JadwalPertemuan::with([
            'jadwal.pengampu.dosen',
            'jadwal.pengampu.mataKuliah'
        ])
        ->get()
        ->groupBy(function ($item) {
            return optional(optional(optional($item->jadwal)->pengampu)->dosen)->nama ?? 'Dosen Tidak Diketahui';
        });

        return view('admin.riwayat-pertemuan', compact(
            'periodeAktif',
            'riwayats',
            'totalPindah',
            'totalBatal',
            'totalRiwayat',
            'rekapDosen'
        ));
    }

    public function simpanPeriode(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran' => ['required', 'string', 'max:20'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'tanggal_mulai' => ['required', 'date'],
            'jumlah_pertemuan' => ['required', 'integer', 'min:1', 'max:20'],
            'aktif' => ['required', 'boolean'],
        ]);
        PeriodeKuliah::query()->update(['aktif' => false]);
        PeriodeKuliah::create([
            'tahun_ajaran' => $data['tahun_ajaran'],
            'semester' => $data['semester'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'jumlah_pertemuan' => $data['jumlah_pertemuan'],
            'aktif' => $data['aktif'],
        ]);

        return redirect()
            ->route('admin.monitoring')
            ->with('success', 'Periode perkuliahan berhasil disimpan.');
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