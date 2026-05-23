<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;

use App\Models\PengampuMataKuliah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Notifikasi;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Carbon\Carbon;
use App\Models\Waktu;
use App\Models\PeriodeKuliah;
use App\Models\JadwalPertemuan;

class JadwalController extends Controller
{
    // =========================
    // Helper response
    // =========================
    private function ok($data, string $messageIfNotEmpty = 'OK')
    {
        $isEmpty = false;

        if (is_array($data)) $isEmpty = count($data) === 0;
        if ($data instanceof \Illuminate\Support\Collection) $isEmpty = $data->isEmpty();

        return response()->json([
            'success' => true,
            'message' => $isEmpty ? 'Data kosong' : $messageIfNotEmpty,
            'data'    => $data,
        ], 200);
    }

    private function okCreated($data, string $message = 'Berhasil dibuat')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], 201);
    }

    private function okDeleted(string $message = 'Berhasil dihapus')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => null,
        ], 200);
    }

    private function conflict(array $conflict, string $message = 'Bentrok jadwal')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => [
                'conflict' => $conflict
            ],
        ], 422);
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'hari'         => ['sometimes', 'string', Rule::in(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'])],
            'semester'     => ['sometimes', 'integer', 'between:1,8'],
            'tahun_ajaran' => ['sometimes', 'regex:/^\d{4}\/\d{4}$/'],
            'program_studi'=> ['sometimes', 'string', 'max:100'],
            'kelas'        => ['sometimes', 'string', 'max:50'],
            'per_page'     => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page'         => ['sometimes', 'integer', 'min:1'],
        ]);
    }

    private function periodeBelumAktif()
    {
        $periode = PeriodeKuliah::orderByDesc('id')->first();

        return !$periode || (int) $periode->aktif !== 1;
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

    private function tanggalPertemuan($pertemuanKe, $hari)
    {
        $periode = PeriodeKuliah::where('aktif', 1)->latest()->first();

        if (!$periode || !$hari) {
            return null;
        }

        $urutanHari = [
            'Senin' => 0,
            'Selasa' => 1,
            'Rabu' => 2,
            'Kamis' => 3,
            'Jumat' => 4,
            'Sabtu' => 5,
            'Minggu' => 6,
        ];

        if (!isset($urutanHari[$hari])) {
            return null;
        }

        Carbon::setLocale('id');

        $awalMinggu = Carbon::parse($periode->tanggal_mulai)
            ->startOfWeek(Carbon::MONDAY)
            ->addWeeks($pertemuanKe - 1);

        return $awalMinggu
            ->copy()
            ->addDays($urutanHari[$hari])
            ->translatedFormat('d F Y');
    }

    private function kodeDosenPengampu($jadwal)
    {
        $kode1 = optional(optional($jadwal->pengampu)->dosen)->kode_dosen;
        $kode2 = optional(optional($jadwal->pengampu)->dosen2)->kode_dosen;

        if ($kode1 && $kode2) {
            return $kode1 . '/' . $kode2;
        }

        return $kode1 ?? $kode2 ?? '-';
    }

    // =========================
    // ADMIN: list jadwal (detail lengkap) + filter + pagination optional
    // =========================
    public function index(Request $request)
    {
        $this->validateFilters($request);

        $query = Jadwal::with(['pengampu.dosen','pengampu.mataKuliah','ruangan','waktu'])
            ->orderBy('id', 'desc');

        // Filter hari (di tabel waktus)
        if ($request->filled('hari')) {
            $hari = $request->query('hari');
            $query->whereHas('waktu', fn($q) => $q->where('hari', $hari));
        }

        // Filter program_studi & kelas (di tabel jadwals)
        if ($request->filled('program_studi')) {
            $query->where('program_studi', $request->query('program_studi'));
        }
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->query('kelas'));
        }

        // Filter semester & tahun_ajaran (di tabel pengampu_mata_kuliahs)
        if ($request->filled('semester')) {
            $query->whereHas('pengampu', fn($q) => $q->where('semester', $request->query('semester')));
        }
        if ($request->filled('tahun_ajaran')) {
            $query->whereHas('pengampu', fn($q) => $q->where('tahun_ajaran', $request->query('tahun_ajaran')));
        }

        // Pagination optional
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        if ($request->has('page') || $request->has('per_page')) {
            $p = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => ($p->total() == 0) ? 'Data kosong' : 'OK',
                'data'    => $p->items(),
                'meta'    => [
                    'current_page' => $p->currentPage(),
                    'per_page'     => $p->perPage(),
                    'total'        => $p->total(),
                    'last_page'    => $p->lastPage(),
                ],
            ], 200);
        }

        return $this->ok($query->get(), 'OK');
    }

    public function show(Jadwal $jadwal)
    {
        $jadwal->load(['pengampu.dosen','pengampu.mataKuliah','ruangan','waktu']);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $jadwal,
        ], 200);
    }

    // =========================
    // ADMIN: create jadwal + cek bentrok
    // =========================
  public function store(Request $request)
{
    try {

        $data = $request->validate([
            'pengampu_id'   => 'required|exists:pengampu_mata_kuliahs,id',
            'ruangan_id'    => 'required|exists:ruangans,id',
            'waktu_id'      => 'required|exists:waktus,id',
            'program_studi' => 'required|string|max:100',
            'kelas'         => 'required|string|max:50',
        ]);

        // 🔥 WAJIB
        $data['status'] = 'aktif';

        $jadwal = Jadwal::create($data);

        if (!$jadwal) {
            return back()->with('error', 'Gagal simpan ke DB');
        }

        return redirect()
            ->route('admin.monitoring')
            ->with('success', 'Jadwal berhasil ditambahkan')
            ->with('new_jadwal_id', $jadwal->id);

    } catch (\Throwable $e) {

        dd($e->getMessage()); // 🔥 lihat error asli
    }
}

    // =========================
    // ADMIN: update jadwal + cek bentrok (exclude current)
    // =========================
   public function update(Request $request, Jadwal $jadwal)
{
    $data = $request->validate([
        'pengampu_id'   => 'required|exists:pengampu_mata_kuliahs,id',
        'ruangan_id'    => 'required|exists:ruangans,id',
        'waktu_id'      => 'required|exists:waktus,id',
        'program_studi' => 'required|string|max:100',
        'kelas'         => 'required|string|max:50',
    ]);

    $jadwal->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Jadwal berhasil diperbarui',
        'data'    => $jadwal
    ]);
}
    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return $this->okDeleted('Jadwal berhasil dihapus');
    }

    // =========================
    // DOSEN (mobile): ringkas + filter + sorting + pagination
    // =========================
    public function jadwalDosen(Request $request)
    {
        $this->validateFilters($request);

        $dosen = auth('dosen')->user();

        $hariOrder = "FIELD(waktus.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')";

        $query = Jadwal::with([
                'waktu',
                'ruangan',
                'pengampu.mataKuliah',
                'pengampu.dosen',
                'pengampu.dosen2',
            ])
            ->whereHas('pengampu', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id)
                ->orWhere('dosen2_id', $dosen->id);
            })
            ->when($request->query('hari'), function ($q) use ($request) {
                $q->whereHas('waktu', fn($w) => $w->where('hari', $request->query('hari')));
            })
            ->when($request->query('program_studi'), fn($q) =>
                $q->where('program_studi', $request->query('program_studi'))
            )
            ->when($request->query('kelas'), fn($q) =>
                $q->where('kelas', $request->query('kelas'))
            )
            ->when($request->query('semester'), function ($q) use ($request) {
                $q->whereHas('pengampu', fn($p) =>
                    $p->where('semester', $request->query('semester'))
                );
            })
            ->when($request->query('tahun_ajaran'), function ($q) use ($request) {
                $q->whereHas('pengampu', fn($p) =>
                    $p->where('tahun_ajaran', $request->query('tahun_ajaran'))
                );
            })
            ->join('waktus', 'waktus.id', '=', 'jadwals.waktu_id')
            ->orderByRaw($hariOrder)
            ->orderBy('waktus.jam_mulai')
            ->select('jadwals.*');

        $data = $query->get()->map(function ($j) {
            return [
                'id' => $j->id,
                'hari' => optional($j->waktu)->hari,
                'jam_mulai' => optional($j->waktu)->jam_mulai,
                'jam_selesai' => optional($j->waktu)->jam_selesai,
                'kode_ruangan' => optional($j->ruangan)->kode_ruangan,
                'nama_ruangan' => optional($j->ruangan)->nama_ruangan,
                'kode_mk' => optional(optional($j->pengampu)->mataKuliah)->kode_mk,
                'nama_mk' => optional(optional($j->pengampu)->mataKuliah)->nama_mk,
                'sks' => optional(optional($j->pengampu)->mataKuliah)->sks,
                'semester' => optional($j->pengampu)->semester,
                'tahun_ajaran' => optional($j->pengampu)->tahun_ajaran,
                'program_studi' => $j->program_studi,
                'kelas' => $j->kelas,
                'kode_dosen' => $this->kodeDosenPengampu($j),
            ];
        });

        return $this->ok($data, 'OK');
    }

  public function batalkan(Request $request, $id)
{
    try {

        \Log::info('REQUEST BATAL', [
            'all' => $request->all()
        ]);

        $request->validate([
            'alasan_batal' => 'required|string',
            'pertemuan_ke' => 'required|integer|min:1',
        ]);

        $jadwal = Jadwal::with(
            'pengampu.mataKuliah',
            'pengampu.dosen',
            'ruangan',
            'waktu'
        )->findOrFail($id);

        // ✅ UPDATE STATUS
        JadwalPertemuan::updateOrCreate(
        [
            'jadwal_id' => $jadwal->id,
            'pertemuan_ke' => $request->pertemuan_ke,
        ],
        [
            'waktu_id' => null,
            'ruangan_id' => null,
            'status' => 'batal',
            'alasan_batal' => $request->alasan_batal,
        ]
    );

        // DEBUG
        \Log::info('SETELAH SAVE', [
            'alasan' => $jadwal->alasan_batal
        ]);

        $mk = $jadwal->pengampu->mataKuliah->nama_mk;
        $kelas = $jadwal->kelas;
        $dosen = $jadwal->pengampu->dosen->nama;
        $waktuLama = $jadwal->waktu;
        $ruanganLamaObj = $jadwal->ruangan;

        $hari = $waktuLama->hari;

        $jam =
            Carbon::parse($waktuLama->jam_mulai)->format('H:i')
            . '-' .
            Carbon::parse($waktuLama->jam_selesai)->format('H:i');

        $ruangan = $ruanganLamaObj->kode_ruangan;

        $alasan = $request->alasan_batal;

        $mahasiswas = Mahasiswa::all();

        foreach ($mahasiswas as $m) {

            Notifikasi::create([
                'role' => 'mahasiswa',
                'user_id' => $m->id,
                'tipe' => 'batal',
                'is_read' => 0,
                'pesan' => json_encode([
                    'nama_mk' => $mk,
                    'kelas' => $kelas,
                    'nama_dosen' => $dosen,
                    'hari_lama' => $hari,
                    'jam_lama' => $jam,
                    'ruangan_lama' => $ruangan,
                    'tanggal_lama' => $this->tanggalPertemuan($request->pertemuan_ke, $hari),
                    // ✅ INI PENTING
                    'alasan_batal' => $alasan,
                ]),
            ]);
        }

        $dosens = Dosen::where(
            'id',
            '!=',
            $jadwal->pengampu->dosen->id
        )->get();

        foreach ($dosens as $d) {

            Notifikasi::create([
                'role' => 'dosen',
                'user_id' => $d->id,
                'tipe' => 'batal',
                'is_read' => 0,
                'pesan' => json_encode([
                    'nama_mk' => $mk,
                    'kelas' => $kelas,
                    'nama_dosen' => $dosen,
                    'hari_lama' => $hari,
                    'jam_lama' => $jam,
                    'ruangan_lama' => $ruangan,
                    'tanggal_lama' => $this->tanggalPertemuan($request->pertemuan_ke, $hari),
                    // ✅ INI PENTING
                    'alasan_batal' => $alasan,
                ]),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dibatalkan',
            'alasan' => $jadwal->alasan_batal,
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}
    
    public function jadwalDosenByMataKuliah(Request $request, $mataKuliahId)
{
    $dosen = auth('dosen')->user();

    $periode = PeriodeKuliah::where('aktif', 1)->latest()->first();

    if (!$periode) {
        return response()->json([
            'success' => true,
            'periode_aktif' => false,
            'message' => 'Semester belum dimulai. Silakan menunggu periode aktif dari admin.',
            'pertemuan_saat_ini' => 0,
            'data' => [],
        ]);
    }

    $pertemuanSaatIni = $this->pertemuanSaatIni();
    $pertemuanKe = (int) $request->query('pertemuan_ke', $pertemuanSaatIni);

    $jadwals = Jadwal::with([
        'waktu',
        'ruangan',
        'pengampu.mataKuliah',
        'pengampu.dosen',
        'pengampu.dosen2',
    ])
    ->whereHas('pengampu', function ($q) use ($dosen, $mataKuliahId) {
        $q->where('mata_kuliah_id', $mataKuliahId)
        ->where(function ($qq) use ($dosen) {
            $qq->where('dosen_id', $dosen->id)
                ->orWhere('dosen2_id', $dosen->id);
        });
    })
    ->get();

    $data = $jadwals->map(function ($j) use ($pertemuanKe) {

    $jp = JadwalPertemuan::with(['waktu', 'ruangan'])
        ->where('jadwal_id', $j->id)
        ->where('pertemuan_ke', $pertemuanKe)
        ->first();

    // 🔥 SIMPAN JADWAL ASLI
    $waktuAsli = Waktu::find($j->waktu_id);
    $ruanganAsli = \App\Models\Ruangan::find($j->ruangan_id);

    $waktu = $waktuAsli;
    $ruangan = $ruanganAsli;

    $status = 'aktif';

    // =====================
    // JIKA BATAL
    // =====================

    if ($jp && $jp->status === 'batal') {

        $status = 'batal';

        // 🔥 PAKAI DATA ASLI
        $waktu = $waktuAsli;
        $ruangan = $ruanganAsli;
    }

    // =====================
    // JIKA PINDAH
    // =====================

    if ($jp && $jp->status === 'pindah') {

        $status = 'pindah';

        $waktu = $jp->waktu;
        $ruangan = $jp->ruangan;
    }

    $hariTampil = $status === 'batal'
        ? $waktuAsli?->hari
        : $waktu?->hari;

    $tanggalTampil = $this->tanggalPertemuan($pertemuanKe, $hariTampil);

    return [
    'id' => $j->id,
    'tanggal' => $tanggalTampil,
    'tanggal_asli' => $this->tanggalPertemuan($pertemuanKe, $waktuAsli?->hari),
    // data yang tampil
    'hari' => $status === 'batal' ? $waktuAsli?->hari : $waktu?->hari,
    'jam_mulai' => $status === 'batal' ? $waktuAsli?->jam_mulai : $waktu?->jam_mulai,
    'jam_selesai' => $status === 'batal' ? $waktuAsli?->jam_selesai : $waktu?->jam_selesai,
    'kode_ruangan' => $status === 'batal' ? $ruanganAsli?->kode_ruangan : $ruangan?->kode_ruangan,
    'nama_ruangan' => $status === 'batal' ? $ruanganAsli?->nama_ruangan : $ruangan?->nama_ruangan,

    // data asli cadangan
    'hari_asli' => $waktuAsli?->hari,
    'jam_mulai_asli' => $waktuAsli?->jam_mulai,
    'jam_selesai_asli' => $waktuAsli?->jam_selesai,
    'kode_ruangan_asli' => $ruanganAsli?->kode_ruangan,

    'program_studi' => $j->program_studi,
    'kelas' => $j->kelas,
    'kode_dosen' => $this->kodeDosenPengampu($j),
    'status' => $status,
    'alasan_batal' => $jp?->alasan_batal,
];
});

    return response()->json([
        'success' => true,
        'periode_aktif' => true,
        'message' => $data->isEmpty() ? 'Data kosong' : 'OK',
        'pertemuan_saat_ini' => $pertemuanSaatIni,
        'pertemuan_dipilih' => $pertemuanKe,
        'data' => $data,
    ]);
}
    public function monitoringDosen(Request $request)
{
    $periode = PeriodeKuliah::latest()->first();

    if (!$periode || !$periode->aktif) {
        return response()->json([
            'success' => true,
            'periode_aktif' => false,
            'message' => 'Semester belum dimulai. Silakan menunggu periode aktif dari admin.',
            'data' => [
                'hari' => $request->query('hari', 'Senin'),
                'ruangans' => [],
                'waktus' => [],
                'matrix' => [],
            ],
        ]);
    }

    $hari = $request->query('hari', 'Senin');
    $pertemuanKe = (int) $request->query('pertemuan_ke', $this->pertemuanSaatIni());

    $ruangans = \App\Models\Ruangan::orderBy('kode_ruangan')->get();

    $waktus = \App\Models\Waktu::where('hari', $hari)
        ->orderBy('jam_mulai')
        ->get();

    $jadwals = \App\Models\Jadwal::with([
        'waktu',
        'ruangan',
        'pengampu.mataKuliah',
        'pengampu.dosen',
        'pengampu.dosen2',
    ])
    ->get()
    ->map(function ($j) use ($pertemuanKe) {

        $jp = \App\Models\JadwalPertemuan::where(
            'jadwal_id',
            $j->id
        )
        ->where('pertemuan_ke', $pertemuanKe)
        ->first();

        if ($jp) {

            $j->status = $jp->status;

            if ($jp->waktu_id) {
                $j->waktu = \App\Models\Waktu::find($jp->waktu_id);
            }

            if ($jp->ruangan_id) {
                $j->ruangan = \App\Models\Ruangan::find($jp->ruangan_id);
            }
        }

        return $j;
    });

    $matrix = [];

    foreach ($jadwals as $j) {

        $jp = JadwalPertemuan::with(['waktu', 'ruangan'])
            ->where('jadwal_id', $j->id)
            ->where('pertemuan_ke', $pertemuanKe)
            ->first();

        $waktuId = $j->waktu_id;
        $ruanganId = $j->ruangan_id;
        $hariTampil = optional($j->waktu)->hari;
        $status = $j->status ?? 'aktif';

        if ($jp) {
            $status = $jp->status;

            if ($jp->status == 'batal') {
                continue;
            }

            if ($jp->status == 'pindah') {
                $waktuId = $jp->waktu_id;
                $ruanganId = $jp->ruangan_id;
                $hariTampil = optional($jp->waktu)->hari;
            }
        }

        if ($hariTampil != $hari) {
            continue;
        }

        $matrix[$waktuId][$ruanganId] = [
            'id' => $j->id,
            'kelas' => $j->kelas,
            'nama_mk' => optional(optional($j->pengampu)->mataKuliah)->nama_mk,
            'kode_dosen' => $this->kodeDosenPengampu($j),
            'status' => $status,
        ];
    }

    return response()->json([
        'success' => true,
        'periode_aktif' => true,
        'message' => 'OK',
        'data' => [
            'hari' => $hari,
            'ruangans' => $ruangans,
            'waktus' => $waktus,
            'matrix' => $matrix,
        ]
    ]);
}

public function gantiJadwal(Request $r, $jadwalId)
{
    $r->validate([
    'waktu_id' => 'required|exists:waktus,id',
    'ruangan_id' => 'required|exists:ruangans,id',
    'pertemuan_ke' => 'required|integer|min:1',
]);

    try {

        $jadwal = Jadwal::with([
            'pengampu',
            'pengampu.dosen',
            'waktu',
            'ruangan'
        ])->find($jadwalId);

        $jadwalPertemuan = JadwalPertemuan::where('jadwal_id', $jadwal->id)
    ->where('pertemuan_ke', $r->pertemuan_ke)
    ->where('status', 'batal')
    ->first();

if (!$jadwalPertemuan) {
    return response()->json([
        'success' => false,
        'message' => 'Pertemuan ini harus dibatalkan dulu sebelum dipindahkan'
    ], 400);
}

        $pertemuan = JadwalPertemuan::where('jadwal_id', $jadwal->id)
            ->where('pertemuan_ke', $r->pertemuan_ke)
            ->first();

        if (!$pertemuan || $pertemuan->status != 'batal') {

            return response()->json([
                'success' => false,
                'message' => 'Pertemuan ini belum dibatalkan'
            ], 400);
        }

        // HARUS SUDAH DIBATALKAN
        $pertemuan = JadwalPertemuan::where('jadwal_id', $jadwal->id)
            ->where('pertemuan_ke', $r->pertemuan_ke)
            ->first();

        if (!$pertemuan || $pertemuan->status != 'batal') {
            return response()->json([
                'success' => false,
                'message' => 'Pertemuan ini belum dibatalkan'
            ], 400);
        }

        // AMBIL WAKTU BARU
        $waktuBaru = Waktu::find($r->waktu_id);

        if (!$waktuBaru) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu baru tidak ditemukan'
            ], 404);
        }

       // ===============================
// CEK BENTROK JADWAL PENGGANTI
// ===============================

$waktuBaru = Waktu::findOrFail($r->waktu_id);

$baruMulai = strtotime($waktuBaru->jam_mulai);
$baruSelesai = strtotime($waktuBaru->jam_selesai);

$dosenId = $jadwal->pengampu->dosen_id;
$kelasYangDipindah = $jadwal->kelas;
$pengampuId = $jadwal->pengampu_id;

$semuaJadwal = Jadwal::with([
    'waktu',
    'ruangan',
    'pengampu.dosen',
    'pengampu.mataKuliah'
])
->where('id', '!=', $jadwal->id)
->get();

foreach ($semuaJadwal as $j) {

    // ambil jadwal pertemuan khusus jika ada
    $jp = JadwalPertemuan::with(['waktu', 'ruangan'])
        ->where('jadwal_id', $j->id)
        ->where('pertemuan_ke', $r->pertemuan_ke)
        ->first();

    // kalau pertemuan itu batal, lewati
    if ($jp && $jp->status === 'batal') {
        continue;
    }

    // kalau jadwal lain sudah pindah, pakai waktu pindahnya
    $waktuCek = ($jp && $jp->status === 'pindah')
        ? $jp->waktu
        : $j->waktu;

    $ruanganCek = ($jp && $jp->status === 'pindah')
        ? $jp->ruangan
        : $j->ruangan;

    if (!$waktuCek) {
        continue;
    }

    // beda hari tidak bentrok
    if ($waktuCek->hari !== $waktuBaru->hari) {
        continue;
    }

    $lamaMulai = strtotime($waktuCek->jam_mulai);
    $lamaSelesai = strtotime($waktuCek->jam_selesai);

    $jamBentrok =
        ($baruMulai < $lamaSelesai) &&
        ($baruSelesai > $lamaMulai);

    if (!$jamBentrok) {
        continue;
    }

    // 1. Bentrok dosen
    if ($j->pengampu && $j->pengampu->dosen_id == $dosenId) {
        return response()->json([
            'success' => false,
            'message' => 'Bentrok! Dosen sudah memiliki jadwal mengajar di hari dan jam tersebut.'
        ], 422);
    }

    // 2. Bentrok kelas
    if ($j->kelas === $kelasYangDipindah) {
        return response()->json([
            'success' => false,
            'message' => 'Bentrok! Kelas '.$kelasYangDipindah.' sudah memiliki jadwal di hari dan jam tersebut.'
        ], 422);
    }

    // 3. Bentrok mata kuliah / pengampu yang sama
    if ($j->pengampu_id == $pengampuId) {
        return response()->json([
            'success' => false,
            'message' => 'Bentrok! Mata kuliah ini sudah memiliki jadwal di hari dan jam tersebut.'
        ], 422);
    }

    // 4. Bentrok ruangan
    if ($ruanganCek && $ruanganCek->id == $r->ruangan_id) {
        return response()->json([
            'success' => false,
            'message' => 'Bentrok! Ruangan sudah digunakan pada hari dan jam tersebut.'
        ], 422);
    }
}

// SIMPAN DATA LAMA
$hariLama = $jadwal->waktu->hari;

$jamLama =
    Carbon::parse($jadwal->waktu->jam_mulai)->format('H:i')
    . '-' .
    Carbon::parse($jadwal->waktu->jam_selesai)->format('H:i');

$ruanganLama = $jadwal->ruangan->kode_ruangan;

$r->validate([
    'waktu_id' => 'required|exists:waktus,id',
    'ruangan_id' => 'required|exists:ruangans,id',
    'pertemuan_ke' => 'required|integer|min:1|max:20',
]);

JadwalPertemuan::updateOrCreate(
    [
        'jadwal_id' => $jadwal->id,
        'pertemuan_ke' => $r->pertemuan_ke,
    ],
    [
        'waktu_id' => $r->waktu_id,
        'ruangan_id' => $r->ruangan_id,
        'status' => 'pindah',
        'alasan_batal' => $jadwalPertemuan->alasan_batal,
    ]
);

        // =====================================
// BUAT NOTIFIKASI PINDAH
// =====================================

// DATA LAMA
$hariLama = $jadwal->waktu->hari;

$jamLama =
    Carbon::parse($jadwal->waktu->jam_mulai)->format('H:i')
    . '-' .
    Carbon::parse($jadwal->waktu->jam_selesai)->format('H:i');

$ruanganLama = $jadwal->ruangan->kode_ruangan;

// DATA BARU
$waktuBaru = Waktu::find($r->waktu_id);

$hariBaru = $waktuBaru->hari;

$jamBaru =
    Carbon::parse($waktuBaru->jam_mulai)->format('H:i')
    . '-' .
    Carbon::parse($waktuBaru->jam_selesai)->format('H:i');

$ruanganBaru = \App\Models\Ruangan::find($r->ruangan_id);

// DATA MK
$mk = optional($jadwal->pengampu->mataKuliah)->nama_mk;
$kelas = $jadwal->kelas;
$namaDosen = optional($jadwal->pengampu->dosen)->nama;

// =====================================
// NOTIF MAHASISWA
// =====================================

$mahasiswas = Mahasiswa::all();

foreach ($mahasiswas as $m) {

    Notifikasi::create([
        'role' => 'mahasiswa',
        'user_id' => $m->id,
        'tipe' => 'pindah',
        'is_read' => 0,
        'pesan' => json_encode([
            'nama_mk' => $mk,
            'kelas' => $kelas,
            'nama_dosen' => $namaDosen,

            'hari_lama' => $hariLama,
            'jam_lama' => $jamLama,
            'ruangan_lama' => $ruanganLama,

            'hari_baru' => $hariBaru,
            'jam_baru' => $jamBaru,
            'ruangan_baru' => $ruanganBaru->kode_ruangan,
            'tanggal_lama' => $this->tanggalPertemuan($r->pertemuan_ke, $hariLama),
            'tanggal_baru' => $this->tanggalPertemuan($r->pertemuan_ke, $hariBaru),
        ]),
    ]);
}

// =====================================
// NOTIF DOSEN
// =====================================

$dosens = Dosen::where(
    'id',
    '!=',
    $jadwal->pengampu->dosen->id
)->get();

foreach ($dosens as $d) {

    Notifikasi::create([
        'role' => 'dosen',
        'user_id' => $d->id,
        'tipe' => 'pindah',
        'is_read' => 0,
        'pesan' => json_encode([
            'nama_mk' => $mk,
            'kelas' => $kelas,
            'nama_dosen' => $namaDosen,

            'hari_lama' => $hariLama,
            'jam_lama' => $jamLama,
            'ruangan_lama' => $ruanganLama,

            'hari_baru' => $hariBaru,
            'jam_baru' => $jamBaru,
            'ruangan_baru' => $ruanganBaru->kode_ruangan,
            'tanggal_lama' => $this->tanggalPertemuan($r->pertemuan_ke, $hariLama),
            'tanggal_baru' => $this->tanggalPertemuan($r->pertemuan_ke, $hariBaru),
        ]),
    ]);
}

        return response()->json([
            'success' => true,
            'message' => 'Berhasil pindah jadwal'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    // =========================
    // MAHASISWA (mobile): ringkas + filter + sorting + pagination
    // =========================
    public function jadwalMahasiswa(Request $request)
    {
        $this->validateFilters($request);

        $mhs = auth('mahasiswa')->user();

        // Normalizer prodi (biar "Sistem Informasi" / "istem Informasi" / "SI" match)
        $normalizeProdi = function (?string $value) {
            if ($value === null) return null;

            $v = trim($value);
            if ($v === '') return null;

            $lower = mb_strtolower($v);

            if ($lower === 'si') return 'SI';
            if ($lower === 'ik') return 'IK';

            if (str_contains($lower, 'sistem informasi') || str_contains($lower, 'istem informasi')) return 'SI';
            if (str_contains($lower, 'ilmu komputer')) return 'IK';

            return $v;
        };

        // ambil dari query dulu, fallback dari akun
        $prodi = $normalizeProdi($request->query('program_studi', $mhs->program_studi));
        $kelas = $request->query('kelas', $mhs->kelas); // bisa null kalau kolom belum ada

        $hariOrder = "FIELD(waktus.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')";

        $query = Jadwal::query()
            ->join('waktus', 'waktus.id', '=', 'jadwals.waktu_id')
            ->join('ruangans', 'ruangans.id', '=', 'jadwals.ruangan_id')
            ->join('pengampu_mata_kuliahs as pmk', 'pmk.id', '=', 'jadwals.pengampu_id')

            ->join('mata_kuliahs as mk', 'mk.id', '=', 'pmk.mata_kuliah_id')
            ->join('dosens', 'dosens.id', '=', 'pmk.dosen_id')

            // filter utama: prodi/kelas (jangan paksa kalau null)
            ->when($prodi, fn($q) => $q->where('jadwals.program_studi', $prodi))
            ->when($kelas, fn($q) => $q->where('jadwals.kelas', $kelas))

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

            // filter optional
            ->when($request->query('hari'), fn($q) => $q->where('waktus.hari', $request->query('hari')))
            ->when($request->query('semester'), fn($q) => $q->where('pmk.semester', $request->query('semester')))
            ->when($request->query('tahun_ajaran'), fn($q) => $q->where('pmk.tahun_ajaran', $request->query('tahun_ajaran')))

            ->orderByRaw($hariOrder)
            ->orderBy('waktus.jam_mulai');

        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        if ($request->has('page') || $request->has('per_page')) {
            $p = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => ($p->total() == 0) ? 'Data kosong' : 'OK',
                'data'    => $p->items(),
                'meta'    => [
                    'current_page' => $p->currentPage(),
                    'per_page'     => $p->perPage(),
                    'total'        => $p->total(),
                    'last_page'    => $p->lastPage(),
                ],
            ], 200);
        }

        return $this->ok($query->get(), 'OK');
    }
    
    public function monitoringMahasiswa(Request $request)
    {
        $periode = PeriodeKuliah::latest()->first();

        if (!$periode || !$periode->aktif) {
            return response()->json([
                'success' => true,
                'periode_aktif' => false,
                'message' => 'Belum ada perkuliahan aktif. Jadwal akan tampil setelah periode diaktifkan admin.',
                'data' => [
                    'hari' => $request->query('hari', 'Senin'),
                    'ruangans' => [],
                    'waktus' => [],
                    'matrix' => [],
                ],
            ]);
        }
        $hari = $request->query('hari', 'Senin');
        
        $ruangans = \App\Models\Ruangan::orderBy('kode_ruangan')->get();
        $pertemuanKe = (int) $request->query('pertemuan_ke', $this->pertemuanSaatIni());
        $waktus = \App\Models\Waktu::where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        $jadwals = \App\Models\Jadwal::with([
            'waktu',
            'ruangan',
            'pengampu.mataKuliah',
            'pengampu.dosen',
            'pengampu.dosen2',  
        ])

        ->get();

        $matrix = [];

        foreach ($jadwals as $j) {

        $jp = JadwalPertemuan::with(['waktu', 'ruangan'])
            ->where('jadwal_id', $j->id)
            ->where('pertemuan_ke', $pertemuanKe)
            ->first();

        // =====================
        // JIKA PERTEMUAN DIBATALKAN
        // =====================

        if ($jp && $jp->status == 'batal') {
            continue;
        }

        // =====================
        // JIKA PERTEMUAN PINDAH
        // =====================

        $waktuId = $jp && $jp->status == 'pindah'
            ? $jp->waktu_id
            : $j->waktu_id;

        $ruanganId = $jp && $jp->status == 'pindah'
            ? $jp->ruangan_id
            : $j->ruangan_id;

        $waktuTampil = $jp && $jp->status == 'pindah'
            ? $jp->waktu
            : $j->waktu;

        // =====================
        // FILTER HARI
        // =====================

        if (!$waktuTampil) {
            continue;
        }

        if ($waktuTampil->hari != $hari) {
            continue;
        }

        // =====================
        // SIMPAN MATRIX
        // =====================

        $matrix[$waktuId][$ruanganId] = [
            'id' => $j->id,
            'kelas' => $j->kelas,
            'nama_mk' => optional(optional($j->pengampu)->mataKuliah)->nama_mk,
            'kode_dosen' => $this->kodeDosenPengampu($j),
            'status' => $jp->status ?? 'aktif',
        ];
    }

        return response()->json([
            'success' => true,
            'data' => [
                'hari' => $hari,
                'ruangans' => $ruangans,
                'waktus' => $waktus,
                'matrix' => $matrix,
            ]
        ]);
    }

}
