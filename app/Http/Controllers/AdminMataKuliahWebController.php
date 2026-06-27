<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\PengampuMataKuliah;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MataKuliahImport;
use App\Models\JadwalPertemuan;
class AdminMataKuliahWebController extends Controller
{
    public function index(Request $request)
    {
        // =========================
        // 1) Parameter filter (periode + tambahan)
        // =========================
        $tahunAjaran = $request->get('tahun_ajaran')
            ?? PengampuMataKuliah::max('tahun_ajaran')
            ?? (date('Y') . '/' . (date('Y') + 1));

        $semester = (int) ($request->get('semester', 1));

        // filter tambahan
        $qDosen = trim((string) $request->get('dosen', '')); // bisa nama/kode dosen
        $qProdi = trim((string) $request->get('prodi', '')); // program studi

        // list dosen (buat dropdown/select di form tambah)
        $dosens = Dosen::orderBy('kode_dosen')->get();

        // =========================
        // 2) Query mata kuliah
        // =========================
        $mataKuliahsQuery = MataKuliah::query();

        // filter prodi di tabel mata_kuliahs
        if ($qProdi !== '') {
            $mataKuliahsQuery->where('program_studi', $qProdi);
        }

        // filter dosen berdasarkan tabel pivot pengampu_mata_kuliah untuk periode yang dipilih
        if ($qDosen !== '') {
            $mataKuliahsQuery->whereHas('pengampus', function ($q) use ($tahunAjaran, $semester, $qDosen) {
                $q->where('tahun_ajaran', $tahunAjaran)
                  ->where('semester', $semester)
                  ->whereHas('dosen', function ($qd) use ($qDosen) {
                      $qd->where('kode_dosen', 'like', '%' . $qDosen . '%')
                         ->orWhere('nama', 'like', '%' . $qDosen . '%');
                  });
            });
        }

        // eager load pengampu sesuai periode (buat tampil di kolom "Dosen Pengampu")
        $mataKuliahs = $mataKuliahsQuery
            ->with(['pengampus' => function ($q) use ($tahunAjaran, $semester) {
                $q->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->with(['dosen', 'dosen2']);
            }])
            ->orderBy('nama_mk')
            ->get();

        $prodis = MataKuliah::select('program_studi')
            ->whereNotNull('program_studi')
            ->distinct()
            ->pluck('program_studi');

        $jumlahPermohonanMenunggu = JadwalPertemuan::where('status', 'menunggu')
        ->whereNull('dibaca_admin_pada')
        ->count();
      return view('admin.mata_kuliah', compact(
            'mataKuliahs',
            'dosens',
            'tahunAjaran',
            'semester',
            'qDosen',
            'qProdi',
            'prodis' // ✅ WAJIB ADA
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([

            'kode_mk'       => ['required','string','max:50'],
            'nama_mk'       => ['required','string','max:255'],
            'program_studi' => ['nullable','string','max:255'],
            'sks'           => ['required','integer','min:1','max:5'],
            'semester'      => ['required','integer','min:1','max:14'],
            'tahun_ajaran'  => ['required','string','max:20'],
            'dosen_id'      => ['required','exists:dosens,id'],
            'dosen2_id'     => ['nullable','exists:dosens,id'],

        ]);

        $errors = [];

        // =========================
        // CEK KODE MK
        // =========================

        $cekKode = MataKuliah::where(
            'kode_mk',
            $data['kode_mk']
        )->exists();

        if ($cekKode) {

            $errors[] =
                'Kode Mata Kuliah '.$data['kode_mk'].' sudah tersedia';
        }

        // =========================
        // JIKA ADA ERROR
        // =========================

        if (count($errors) > 0) {

            return back()
                ->withInput()
                ->with('error', $errors);
        }

        // =========================
        // SIMPAN MK
        // =========================

        $mk = MataKuliah::create([

            'kode_mk'       => $data['kode_mk'],
            'nama_mk'       => $data['nama_mk'],
            'program_studi' => $data['program_studi'] ?? null,
            'sks'           => $data['sks'],
            'semester'      => $data['semester'],

        ]);

        // =========================
        // SIMPAN PENGAMPU
        // =========================

        PengampuMataKuliah::create([

            'mata_kuliah_id' => $mk->id,
            'semester'       => $data['semester'],
            'tahun_ajaran'   => $data['tahun_ajaran'],
            'dosen_id'       => $data['dosen_id'],
            'dosen2_id' => $data['dosen2_id'] ?? null,

        ]);

        return redirect()
            ->route('admin.matakuliah.index', [

                'semester' => $data['semester'],
                'tahun_ajaran' => $data['tahun_ajaran'],

            ])
            ->with(
                'ok',
                'Mata kuliah berhasil disimpan.'
            )
            ->with(
                'highlight_ids',
                [$mk->id]
            );
    }

    public function edit(Request $request, MataKuliah $mataKuliah)
    {
     $jumlahPermohonanMenunggu = \App\Models\JadwalPertemuan::where('status', 'menunggu')->count();    
    $tahunAjaran = $request->get('tahun_ajaran')
            ?? PengampuMataKuliah::max('tahun_ajaran')
            ?? (date('Y') . '/' . (date('Y') + 1));

        $semester = (int) ($request->get('semester', 1));

        $dosens = Dosen::orderBy('kode_dosen')->get();
        $prodis = MataKuliah::select('program_studi')
            ->distinct()
            ->pluck('program_studi');

        $pengampu = PengampuMataKuliah::where('mata_kuliah_id', $mataKuliah->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->first();

        return view('admin.mata_kuliah_edit', compact(
            'mataKuliah',
            'dosens',
            'pengampu',
            'tahunAjaran',
            'semester'
        ));
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $data = $request->validate([
            'kode_mk'       => ['required','string','max:50'],
            'nama_mk'       => ['required','string','max:255'],
            'program_studi' => ['nullable','string','max:255'],
            'sks'           => ['required','integer','min:0','max:30'],
            'semester'      => ['required','integer','min:1','max:14'],
            'tahun_ajaran'  => ['required','string','max:20'],
            'dosen_id'      => ['required','exists:dosens,id'],
            'dosen2_id'     => ['nullable','exists:dosens,id'],
        ]);

        // cek kode mk duplikat
        $exists = MataKuliah::where('kode_mk', $data['kode_mk'])
            ->where('id', '!=', $mataKuliah->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', [
                    'Kode Mata Kuliah '.$data['kode_mk'].' sudah digunakan'
                ]);
        }

        $mataKuliah->update([
            'kode_mk'       => $data['kode_mk'],
            'nama_mk'       => $data['nama_mk'],
            'program_studi' => $data['program_studi'] ?? null,
            'sks'           => $data['sks'],
            'semester'      => $data['semester'],
        ]);

        // update pengampu sesuai periode
        PengampuMataKuliah::updateOrCreate(
            [
                'mata_kuliah_id' => $mataKuliah->id,
                'semester'       => $data['semester'],
                'tahun_ajaran'   => $data['tahun_ajaran'],
            ],
            [
                'dosen_id' => $data['dosen_id'],
                'dosen2_id' => $data['dosen2_id'] ?? null,
            ]
        );

        return redirect()
            ->route('admin.matakuliah.index', [
                'semester' => $data['semester'],
                'tahun_ajaran' => $data['tahun_ajaran'],
            ])
            ->with('ok', 'Mata kuliah berhasil diupdate.')
            ->with('highlight_ids', [$mataKuliah->id]);
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        PengampuMataKuliah::where('mata_kuliah_id', $mataKuliah->id)->delete();
        $mataKuliah->delete();

        return redirect()->route('admin.matakuliah.index')->with('ok', 'Mata kuliah berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xlsx,xls,csv'],
            'semester' => ['nullable','integer','min:1','max:14'],
            'tahun_ajaran' => ['nullable','string','max:20'],
            'dosen' => ['nullable','string','max:255'],
            'prodi' => ['nullable','string','max:255'],
        ]);

        $import = new MataKuliahImport;
        Excel::import($import, $request->file('file'));

        // ✅ balik ke filter yang sedang aktif
        $semester = (int) ($request->input('semester', 1));
        $tahunAjaran = $request->input('tahun_ajaran') ?: (date('Y') . '/' . (date('Y') + 1));
        $qDosen = $request->input('dosen', '');
        $qProdi = $request->input('prodi', '');

       return redirect()
            ->route('admin.matakuliah.index', [
                'semester' => $semester,
                'tahun_ajaran' => $tahunAjaran,
                'dosen' => $qDosen,
                'prodi' => $qProdi,
            ])
            ->with('ok', 'Import mata kuliah berhasil.')
            ->with('highlight_ids', $import->insertedIds ?? []);
    }
}
