<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Waktu;
use App\Models\PengampuMataKuliah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Notifikasi;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Carbon\Carbon;


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

        $query = Jadwal::query()
            ->join('waktus', 'waktus.id', '=', 'jadwals.waktu_id')
            ->join('ruangans', 'ruangans.id', '=', 'jadwals.ruangan_id')
            ->join('pengampu_mata_kuliahs as pmk', 'pmk.id', '=', 'jadwals.pengampu_id')
            ->join('mata_kuliahs as mk', 'mk.id', '=', 'pmk.mata_kuliah_id')
            ->where('pmk.dosen_id', $dosen->id)
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
            ->when($request->query('hari'), fn($q) => $q->where('waktus.hari', $request->query('hari')))
            ->when($request->query('program_studi'), fn($q) => $q->where('jadwals.program_studi', $request->query('program_studi')))
            ->when($request->query('kelas'), fn($q) => $q->where('jadwals.kelas', $request->query('kelas')))
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

   public function batalkan($id)
    {
        $jadwal = Jadwal::with('pengampu.mataKuliah', 'pengampu.dosen', 'ruangan', 'waktu')->findOrFail($id);

        $jadwal->status = 'batal';
        $jadwal->save();

        $mk = $jadwal->pengampu->mataKuliah->nama_mk;
        $kelas = $jadwal->kelas;
        $dosen = $jadwal->pengampu->dosen->nama;
        $ruangan = $jadwal->ruangan->kode_ruangan;

        // ✅ FORMAT BARU
        $hari = $jadwal->waktu->hari . ', ' . Carbon::parse($jadwal->waktu->jam_mulai)->format('Y-m-d');

        $jam =
            Carbon::parse($jadwal->waktu->jam_mulai)->format('H:i') . '-' .
            Carbon::parse($jadwal->waktu->jam_selesai)->format('H:i');

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
                ]),
            ]);
        }

        $dosens = Dosen::where('id', '!=', $jadwal->pengampu->dosen->id)->get();

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
        ]),
    ]);
}

        return response()->json([
            'message' => 'Jadwal dibatalkan',
            'data' => $jadwal
        ]);
    }
    
    public function jadwalDosenByMataKuliah($mataKuliahId)
        {
        $dosen = auth('dosen')->user();

        $data = Jadwal::query()
            ->join('waktus', 'waktus.id', '=', 'jadwals.waktu_id')
            ->join('ruangans', 'ruangans.id', '=', 'jadwals.ruangan_id')
            ->join('pengampu_mata_kuliahs as pmk', 'pmk.id', '=', 'jadwals.pengampu_id')
            ->join('mata_kuliahs as mk', 'mk.id', '=', 'pmk.mata_kuliah_id')

            ->where('pmk.dosen_id', $dosen->id)
            ->where('mk.id', $mataKuliahId)

            ->select([
                'jadwals.id',
                'waktus.hari',
                'waktus.jam_mulai',
                'waktus.jam_selesai',
                'ruangans.kode_ruangan',
                'ruangans.nama_ruangan',
                'jadwals.program_studi',
                'jadwals.kelas',
                'jadwals.status',
            ])

            ->orderByRaw("FIELD(waktus.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('waktus.jam_mulai')
            ->get();

        return response()->json([
            'success' => true,
            'message' => $data->isEmpty() ? 'Data kosong' : 'OK',
            'data' => $data
        ]);
    }

    public function monitoringDosen(Request $request)
    {
        $hari = $request->query('hari', 'Senin');

        $ruangans = \App\Models\Ruangan::orderBy('kode_ruangan')->get();

        $waktus = \App\Models\Waktu::where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        $jadwals = \App\Models\Jadwal::with([
            'waktu',
            'ruangan',
            'pengampu.mataKuliah',
            'pengampu.dosen'
        ])
        ->whereIn('waktu_id', $waktus->pluck('id'))
        ->get();

        $matrix = [];

        foreach ($jadwals as $j) {
            if ($j->status == 'batal') continue;

            $matrix[$j->waktu_id][$j->ruangan_id] = [
                'id' => $j->id,
                'kelas' => $j->kelas,
                'nama_mk' => optional(optional($j->pengampu)->mataKuliah)->nama_mk,
                'kode_dosen' => optional(optional($j->pengampu)->dosen)->kode_dosen,
                'status' => $j->status ?? 'aktif',
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

public function gantiJadwal(Request $r, $id)
{
    try {

        $jadwal = Jadwal::find($id);
        

        if (!$jadwal) {
            return response()->json([
                'message' => 'Jadwal tidak ditemukan'
            ], 404);
        }

        // ✅ SIMPAN DATA LAMA SEBELUM DIUBAH
        $oldHari = $jadwal->waktu->hari;
        $oldJam = $jadwal->waktu->jam_mulai . '-' . $jadwal->waktu->jam_selesai;
        $oldRuangan = $jadwal->ruangan->kode_ruangan;

        if ($jadwal->status != 'batal') {
            return response()->json([
                'message' => 'Jadwal harus dibatalkan dulu'
            ], 400);
        }

        $jadwal = Jadwal::with('pengampu.mataKuliah', 'ruangan', 'waktu')->find($id);

        // simpan data lama dulu
        $ruanganLama = $jadwal->ruangan->kode_ruangan;
        $jamLama = $jadwal->waktu->jam_mulai . ' - ' . $jadwal->waktu->jam_selesai;

        // 🔥 UPDATE JADWAL
        $jadwal->waktu_id = $r->waktu_id;
        $jadwal->ruangan_id = $r->ruangan_id;
        $jadwal->status = 'pindah';
        $jadwal->save();

        // reload relasi baru
        $jadwal->load('ruangan', 'waktu');


        // 🔥 INSERT NOTIFIKASI
        $mahasiswas = \App\Models\Mahasiswa::all();
        $mk = $jadwal->pengampu->mataKuliah->nama_mk;
        $dosen = $jadwal->pengampu->dosen->nama;
        $kelas = $jadwal->kelas;
        $ruangan = $jadwal->ruangan->kode_ruangan;
        $hari = $jadwal->waktu->hari;
        $hariBaru = $jadwal->waktu->hari . ', ' . Carbon::parse($jadwal->waktu->jam_mulai)->format('Y-m-d');
        $jamBaru =
            Carbon::parse($jadwal->waktu->jam_mulai)->format('H:i') . '-' .
            Carbon::parse($jadwal->waktu->jam_selesai)->format('H:i');
      foreach ($mahasiswas as $m) {
        Notifikasi::create([
            'role' => 'mahasiswa',
            'user_id' => $m->id,
            'tipe' => 'pindah',
            'is_read' => 0,
            'pesan' => json_encode([
                'nama_mk' => $mk,
                'kelas' => $kelas,
                'nama_dosen' => $dosen,

                // 🔴 LAMA (FIX)
                'hari_lama' => $oldHari,
                'jam_lama' => $oldJam,
                'ruangan_lama' => $oldRuangan,

                // 🟢 BARU
                'hari_baru' => $hariBaru,
                'jam_baru' => $jamBaru,
                'ruangan_baru' => $ruangan,
            ]),
        ]);
}

    $dosens = Dosen::where('id', '!=', $jadwal->pengampu->dosen->id)->get();

foreach ($dosens as $d) {
    Notifikasi::create([
        'role' => 'dosen',
        'user_id' => $d->id,
        'tipe' => 'pindah',
        'is_read' => 0,
        'pesan' => json_encode([
            'nama_mk' => $mk,
            'kelas' => $kelas,
            'nama_dosen' => $dosen,
            'hari_lama' => $oldHari,
            'jam_lama' => $oldJam,
            'ruangan_lama' => $oldRuangan,
            'hari_baru' => $hariBaru,
            'jam_baru' => $jamBaru,
            'ruangan_baru' => $ruangan,
        ]),
    ]);
}
        return response()->json([
            'message' => 'Berhasil pindah'
        ]);

    } catch (\Exception $e) {

        return response()->json([
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
        $hari = $request->query('hari', 'Senin');

        $ruangans = \App\Models\Ruangan::orderBy('kode_ruangan')->get();

        $waktus = \App\Models\Waktu::where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        $jadwals = \App\Models\Jadwal::with([
            'waktu',
            'ruangan',
            'pengampu.mataKuliah',
            'pengampu.dosen'
        ])
        ->whereIn('waktu_id', $waktus->pluck('id'))
        ->where('status', '!=', 'batal') // opsional
        ->get();

        $matrix = [];

        foreach ($jadwals as $j) {
            $matrix[$j->waktu_id][$j->ruangan_id] = [
                'id' => $j->id,
                'kelas' => $j->kelas,
                'nama_mk' => optional($j->pengampu->mataKuliah)->nama_mk,
                'kode_dosen' => optional($j->pengampu->dosen)->kode_dosen,
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
