<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Dosen;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DosenImport;

class AdminDosenWebController extends Controller
{
    public function index(Request $request)
    {
        $matakuliahs = \App\Models\MataKuliah::orderBy('nama_mk')
             ->get();
        $q     = trim((string) $request->get('q', ''));
        $prodi = trim((string) $request->get('prodi', ''));
        $nidn  = trim((string) $request->get('nidn', ''));
        $urut  = $request->get('urut', 'kode'); // kode | nama

        $query = Dosen::query();

        // filter pencarian umum (kode/nama/email/nidn)
        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('kode_dosen', 'like', "%{$q}%")
                  ->orWhere('nama', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('nidn', 'like', "%{$q}%");
            });
        }

        // filter program studi
        if ($prodi !== '') {
            $query->where('program_studi', 'like', "%{$prodi}%");
        }

        // filter NIDN spesifik (optional)
        if ($nidn !== '') {
            $query->where('nidn', 'like', "%{$nidn}%");
        }

        // sorting
        if ($urut === 'nama') {
            $query->orderBy('nama');
        } else {
            $query->orderBy('kode_dosen');
        }

        $dosens = $query->get();

        return view('admin.dosen', compact('dosens', 'q', 'prodi', 'nidn', 'urut', 'matakuliahs'));
    }

   public function store(Request $request)
{   
    $request->validate([
        'nama' => 'required',
        'nidn' => 'required|digits:10',
        'kode_dosen' => 'required',
        'program_studi' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6',
    ], [
        'nidn.required' => 'NIDN wajib diisi',
        'nidn.digits' => 'NIDN harus tepat 10 angka',
    ]);

    $errors = [];

    // =========================
    // CEK NIDN
    // =========================

    $cekNidn = \App\Models\Dosen::where(
        'nidn',
        $request->nidn
    )->exists();

    if ($cekNidn) {

        $errors[] =
            'NIDN '.$request->nidn.' sudah tersedia';
    }

    // =========================
    // CEK EMAIL
    // =========================

    $cekEmail = \App\Models\Dosen::where(
        'email',
        $request->email
    )->exists();

    if ($cekEmail) {

        $errors[] =
            'Email '.$request->email.' sudah tersedia';
    }

    // =========================
    // CEK KODE DOSEN
    // =========================

    $cekKode = \App\Models\Dosen::where(
        'kode_dosen',
        $request->kode_dosen
    )->exists();

    if ($cekKode) {

        $errors[] =
            'Kode Dosen '.$request->kode_dosen.' sudah digunakan';
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
    // SIMPAN DOSEN
    // =========================

    $dosen = \App\Models\Dosen::create([
        'nama' => $request->nama,
        'nidn' => $request->nidn,
        'kode_dosen' => $request->kode_dosen,
        'program_studi' => $request->program_studi,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    // =========================
// SIMPAN PENGAMPU
// =========================

if ($request->mata_kuliah_id) {

    \App\Models\PengampuMataKuliah::create([

        'dosen_id' => $dosen->id,

        'mata_kuliah_id' => $request->mata_kuliah_id,

        'semester' => 1,

        'tahun_ajaran' => $request->tahun_ajaran,
    ]);
}

return back()
    ->with(
        'ok',
        'Dosen berhasil ditambahkan'
    )
    ->with(
        'highlight_id',
        $dosen->id
    );
}

    public function edit(Dosen $dosen)
    {
        return view('admin.dosen.edit', compact('dosen'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $data = $request->validate([
        'kode_dosen'    => ['required','string','max:50'],
        'nama'          => ['required','string','max:255'],
        'program_studi' => ['nullable','string','max:255'],
        'nidn'          => ['required','digits:10'],
        'email'         => ['nullable','email','max:255'],
        'password'      => ['nullable','string','min:6'],
    ], [

        'nidn.required' => 'NIDN wajib diisi',
        'nidn.digits' => 'NIDN harus tepat 10 angka',
    ]);

        $errors = [];

        // =========================
        // CEK KODE DOSEN
        // =========================

        $cekKode = Dosen::where(
            'kode_dosen',
            $data['kode_dosen']
        )
        ->where('id', '!=', $dosen->id)
        ->exists();

        if ($cekKode) {

            $errors[] =
                'Kode Dosen '.$data['kode_dosen'].' sudah digunakan';
        }

        // =========================
        // CEK EMAIL
        // =========================

        $cekEmail = Dosen::where(
            'email',
            $data['email']
        )
        ->where('id', '!=', $dosen->id)
        ->exists();

        if ($cekEmail) {

            $errors[] =
                'Email '.$data['email'].' sudah tersedia';
        }

        // =========================
        // CEK NIDN
        // =========================

        $cekNidn = Dosen::where(
            'nidn',
            $data['nidn']
        )
        ->where('id', '!=', $dosen->id)
        ->exists();

        if ($cekNidn) {

            $errors[] =
                'NIDN '.$data['nidn'].' sudah tersedia';
        }

        // =========================
        // JIKA ADA ERROR
        // =========================

        if (count($errors) > 0) {

            return back()
                ->withInput()
                ->with('error', $errors);
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $dosen->update($data);

       return redirect()
            ->route('admin.dosen.index')
            ->with(
                'ok',
                'Data dosen berhasil diupdate.'
            )
            ->with(
                'highlight_id',
                $dosen->id
            );
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->delete();
        return redirect()->route('admin.dosen.index')->with('ok', 'Data dosen berhasil dihapus.');
    }

   public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        // reset duplicate
        \App\Imports\DosenImport::$duplicates = [];

        \Maatwebsite\Excel\Facades\Excel::import(
            new \App\Imports\DosenImport,
            $request->file('file')
        );

        $duplicates = \App\Imports\DosenImport::$duplicates;

        if (count($duplicates) > 0) {

            return back()
                ->with(
                    'error',
                    $duplicates
                )
                ->with(
                    'ok',
                    'Import selesai. Sebagian data berhasil ditambahkan.'
                );
        }

        $lastDosen = \App\Models\Dosen::latest()->first();

    return back()
        ->with(
            'ok',
            'Import dosen berhasil'
        )
        ->with(
            'highlight_id',
            $lastDosen?->id
        );
    }
}
