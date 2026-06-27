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
use App\Models\Mahasiswa;
use App\Models\JadwalPertemuan;
use App\Services\FcmService;
use Illuminate\Support\Facades\DB;
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
    
    private function tanggalPertemuan($hari, $pertemuanKe)
{
    $periode = PeriodeKuliah::where('aktif', 1)->latest()->first();

    if (!$periode || !$hari) return '-';

    $urutanHari = [
        'Senin' => 1,
        'Selasa' => 2,
        'Rabu' => 3,
        'Kamis' => 4,
        'Jumat' => 5,
        'Sabtu' => 6,
        'Minggu' => 7,
    ];

    $tanggalAwal = Carbon::parse($periode->tanggal_mulai)
        ->timezone('Asia/Makassar')
        ->startOfDay();

    $targetHari = $urutanHari[$hari] ?? 1;
    $hariAwal = $tanggalAwal->dayOfWeekIso;

    $selisihHari = $targetHari - $hariAwal;

    if ($selisihHari < 0) {
        $selisihHari += 7;
    }

    return $tanggalAwal
        ->copy()
        ->addDays($selisihHari)
        ->addWeeks($pertemuanKe - 1)
        ->translatedFormat('d F Y');
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
        $permohonanDipilih = null;
        if ($request->filled('permohonan_id')) {
            $permohonanDipilih = JadwalPertemuan::with([
                'jadwal.pengampu.dosen',
                'jadwal.pengampu.dosen2',
                'jadwal.pengampu.mataKuliah',
                'jadwal.waktu',
                'jadwal.ruangan',
            ])
            ->where('status', 'menunggu')
            ->find($request->permohonan_id);
        }
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
            'jumlahPermohonanMenunggu',
            'permohonanDipilih'
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

public function notifikasi()
{
    $permohonanMenunggu = JadwalPertemuan::with([
        'jadwal.pengampu.dosen',
        'jadwal.pengampu.dosen2',
        'jadwal.pengampu.mataKuliah',
        'jadwal.waktu',
        'jadwal.ruangan',
        'waktu',
        'ruangan',
    ])
    ->where('status', 'menunggu')
    ->latest()
    ->get();

    JadwalPertemuan::where('status', 'menunggu')
        ->whereNull('dibaca_admin_pada')
        ->update([
            'dibaca_admin_pada' => now(),
        ]);

    $riwayatPermohonan = JadwalPertemuan::with([
        'jadwal.pengampu.dosen',
        'jadwal.pengampu.dosen2',
        'jadwal.pengampu.mataKuliah',
        'jadwal.waktu',
        'jadwal.ruangan',
        'waktu',
        'ruangan',
    ])
    ->whereIn('status', ['ditolak', 'pindah'])
    ->orderByDesc('updated_at')
    ->get();

    $jumlahPermohonanMenunggu = 0;

    return view('admin.notifikasi', compact(
        'permohonanMenunggu',
        'riwayatPermohonan',
        'jumlahPermohonanMenunggu'
    ));
}

public function mulaiSetujuiPermohonan($id)
{
    $permohonan = JadwalPertemuan::where('status', 'menunggu')->findOrFail($id);

    return redirect()
        ->route('admin.monitoring', [
            'permohonan_id' => $permohonan->id,
        ])
        ->with('info', 'Silakan pilih slot kosong pada tabel monitoring sebagai jadwal pengganti.');
}

public function pilihSlotPengganti(Request $request, $id)
{
    $request->validate([
        'waktu_id' => ['required', 'exists:waktus,id'],
        'ruangan_id' => ['required', 'exists:ruangans,id'],
    ]);

    $permohonan = JadwalPertemuan::with([
        'jadwal.pengampu.dosen',
        'jadwal.pengampu.dosen2',
        'jadwal.pengampu.mataKuliah',
        'jadwal.waktu',
        'jadwal.ruangan',
    ])->where('status', 'menunggu')->findOrFail($id);

    $jadwal = $permohonan->jadwal;
    $waktuBaru = Waktu::findOrFail($request->waktu_id);
    $ruanganBaru = Ruangan::findOrFail($request->ruangan_id);

    $baruMulai = strtotime($waktuBaru->jam_mulai);
    $baruSelesai = strtotime($waktuBaru->jam_selesai);

    $dosenId1 = optional($jadwal->pengampu)->dosen_id;
    $dosenId2 = optional($jadwal->pengampu)->dosen2_id;
    $kelasYangDipindah = $jadwal->kelas;
    $pengampuId = $jadwal->pengampu_id;

    $semuaJadwal = Jadwal::with([
        'waktu',
        'ruangan',
        'pengampu.dosen',
        'pengampu.dosen2',
        'pengampu.mataKuliah',
    ])
    ->where('id', '!=', $jadwal->id)
    ->get();

    foreach ($semuaJadwal as $j) {
        $jp = JadwalPertemuan::with(['waktu', 'ruangan'])
            ->where('jadwal_id', $j->id)
            ->where('pertemuan_ke', $permohonan->pertemuan_ke)
            ->first();

        if ($jp && in_array($jp->status, ['batal', 'ditolak'])) {
            continue;
        }

        $waktuCek = ($jp && $jp->status === 'pindah') ? $jp->waktu : $j->waktu;
        $ruanganCek = ($jp && $jp->status === 'pindah') ? $jp->ruangan : $j->ruangan;

        if (!$waktuCek) continue;
        if ($waktuCek->hari !== $waktuBaru->hari) continue;

        $lamaMulai = strtotime($waktuCek->jam_mulai);
        $lamaSelesai = strtotime($waktuCek->jam_selesai);

        $jamBentrok = ($baruMulai < $lamaSelesai) && ($baruSelesai > $lamaMulai);

        if (!$jamBentrok) continue;

        if ($j->pengampu && (
            $j->pengampu->dosen_id == $dosenId1 ||
            $j->pengampu->dosen2_id == $dosenId1 ||
            ($dosenId2 && $j->pengampu->dosen_id == $dosenId2) ||
            ($dosenId2 && $j->pengampu->dosen2_id == $dosenId2)
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Bentrok! Dosen sudah memiliki jadwal mengajar pada hari dan jam tersebut.'
            ], 422);
        }

        if ($j->kelas === $kelasYangDipindah) {
            return response()->json([
                'success' => false,
                'message' => 'Bentrok! Kelas sudah memiliki jadwal pada hari dan jam tersebut.'
            ], 422);
        }

        if ($j->pengampu_id == $pengampuId) {
            return response()->json([
                'success' => false,
                'message' => 'Bentrok! Mata kuliah ini sudah memiliki jadwal pada hari dan jam tersebut.'
            ], 422);
        }

        if ($ruanganCek && $ruanganCek->id == $request->ruangan_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bentrok! Ruangan sudah digunakan pada hari dan jam tersebut.'
            ], 422);
        }
    }

    $permohonan->update([
        'waktu_id' => $request->waktu_id,
        'ruangan_id' => $request->ruangan_id,
        'status' => 'pindah',
        'disetujui_pada' => now(),
    ]);

    $mk = optional(optional($jadwal->pengampu)->mataKuliah)->nama_mk ?? '-';
    $kelas = $jadwal->kelas ?? '-';
    $namaDosen = optional(optional($jadwal->pengampu)->dosen)->nama ?? '-';

    $waktuLama = $jadwal->waktu;
    $ruanganLama = $jadwal->ruangan;

    $jamLama = $waktuLama
        ? Carbon::parse($waktuLama->jam_mulai)->format('H:i') . '-' . Carbon::parse($waktuLama->jam_selesai)->format('H:i')
        : '-';

    $jamBaru = Carbon::parse($waktuBaru->jam_mulai)->format('H:i') . '-' . Carbon::parse($waktuBaru->jam_selesai)->format('H:i');

    $tanggalLama = $this->tanggalPertemuan(
    optional($waktuLama)->hari,
    $permohonan->pertemuan_ke
);

$tanggalBaru = $this->tanggalPertemuan(
    $waktuBaru->hari,
    $permohonan->pertemuan_ke
);

$pesan = [
    'nama_mk' => $mk,
    'kelas' => $kelas,
    'nama_dosen' => $namaDosen,
    'pertemuan_ke' => $permohonan->pertemuan_ke,

    'hari_lama' => optional($waktuLama)->hari ?? '-',
    'tanggal_lama' => $tanggalLama,
    'jam_lama' => $jamLama,
    'ruangan_lama' => optional($ruanganLama)->kode_ruangan ?? '-',

    'hari_baru' => $waktuBaru->hari,
    'tanggal_baru' => $tanggalBaru,
    'jam_baru' => $jamBaru,
    'ruangan_baru' => $ruanganBaru->kode_ruangan,
];

    foreach (Dosen::all() as $dosen) {
        Notifikasi::create([
            'role' => 'dosen',
            'user_id' => $dosen->id,
            'tipe' => 'pindah',
            'is_read' => 0,
            'pesan' => json_encode($pesan),
        ]);

        if ($dosen->fcm_token) {
            app(FcmService::class)->sendToToken(
                $dosen->fcm_token,
                'Jadwal Perkuliahan Berubah',
                $mk . ' kelas ' . $kelas . ' dipindahkan ke ' . $waktuBaru->hari . ', ruang ' . $ruanganBaru->kode_ruangan . '.',
                [
                    'tipe' => 'pindah',
                    'jadwal_id' => (string) $jadwal->id,
                    'pertemuan_ke' => (string) $permohonan->pertemuan_ke,
                ]
            );
        }
    }

    foreach (Mahasiswa::all() as $mhs) {
        Notifikasi::create([
            'role' => 'mahasiswa',
            'user_id' => $mhs->id,
            'tipe' => 'pindah',
            'is_read' => 0,
            'pesan' => json_encode($pesan),
        ]);

        if ($mhs->fcm_token) {
            app(FcmService::class)->sendToToken(
                $mhs->fcm_token,
                'Jadwal Perkuliahan Berubah',
                $mk . ' kelas ' . $kelas . ' dipindahkan ke ' . $waktuBaru->hari . ', ruang ' . $ruanganBaru->kode_ruangan . '.',
                [
                    'tipe' => 'pindah',
                    'jadwal_id' => (string) $jadwal->id,
                    'pertemuan_ke' => (string) $permohonan->pertemuan_ke,
                ]
            );
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Permohonan disetujui dan jadwal berhasil dipindahkan.'
    ]);
}

public function tolakPermohonan(Request $request, $id)
{
    $request->validate([
        'alasan_tolak' => ['required', 'string', 'max:1000'],
    ]);

    $permohonan = JadwalPertemuan::with([
        'jadwal.pengampu.dosen',
        'jadwal.pengampu.dosen2',
        'jadwal.pengampu.mataKuliah',
        'jadwal.waktu',
        'jadwal.ruangan',
    ])->findOrFail($id);

    $permohonan->update([
        'status' => 'ditolak',
        'alasan_tolak' => $request->alasan_tolak,
        'ditolak_pada' => now(),
    ]);

    $jadwal = $permohonan->jadwal;
    $pengampu = optional($jadwal)->pengampu;

    $mk = optional(optional($pengampu)->mataKuliah)->nama_mk ?? '-';
    $kelas = optional($jadwal)->kelas ?? '-';

    $waktu = optional($jadwal)->waktu;
    $ruangan = optional($jadwal)->ruangan;

    $jamLama = $waktu
        ? \Carbon\Carbon::parse($waktu->jam_mulai)->format('H:i') . '-' .
          \Carbon\Carbon::parse($waktu->jam_selesai)->format('H:i')
        : '-';

    $dosenIds = [];

    if ($pengampu && $pengampu->dosen_id) {
        $dosenIds[] = $pengampu->dosen_id;
    }

    if ($pengampu && $pengampu->dosen2_id) {
        $dosenIds[] = $pengampu->dosen2_id;
    }
foreach ($dosenIds as $dosenId) {

    Notifikasi::create([
        'role' => 'dosen',
        'user_id' => $dosenId,
        'tipe' => 'ditolak',
        'is_read' => 0,
        'pesan' => json_encode([
            'judul' => 'Permohonan Perubahan Jadwal Ditolak',
            'jadwal_id' => optional($jadwal)->id,
            'pertemuan_ke' => $permohonan->pertemuan_ke,
            'nama_mk' => $mk,
            'kelas' => $kelas,
            'hari_lama' => optional($waktu)->hari ?? '-',
            'jam_lama' => $jamLama,
            'ruangan_lama' => optional($ruangan)->kode_ruangan ?? '-',
            'alasan_batal' => $permohonan->alasan_batal,
            'alasan_tolak' => $request->alasan_tolak,
        ]),
    ]);

    $dosen = Dosen::find($dosenId);

if ($dosen && $dosen->fcm_token) {
    $sent = app(FcmService::class)->sendToToken(
        $dosen->fcm_token,
        'Permohonan Jadwal Ditolak',
        'Permohonan perubahan jadwal ' . $mk . ' kelas ' . $kelas . ' ditolak oleh admin.',
        [
            'tipe' => 'ditolak',
            'jadwal_id' => (string) optional($jadwal)->id,
            'pertemuan_ke' => (string) $permohonan->pertemuan_ke,
        ]
    );

    \Log::info('FCM TOLAK PERMOHONAN', [
        'dosen_id' => $dosenId,
        'has_token' => true,
        'sent' => $sent,
    ]);
} else {
    \Log::warning('FCM TOLAK PERMOHONAN TOKEN KOSONG', [
        'dosen_id' => $dosenId,
    ]);
}
}

    return redirect()
        ->route('admin.notifikasi')
        ->with('success', 'Permohonan berhasil ditolak dan notifikasi dikirim ke dosen.');
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