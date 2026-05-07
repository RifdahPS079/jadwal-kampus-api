<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MahasiswaImport;

class AdminMahasiswaWebController extends Controller
{
    public function index(Request $request)
    {
        // =========================
        // FILTER INPUT
        // =========================
        $q      = trim((string) $request->get('q', ''));          // cari nama/nim/email
        $prodi  = trim((string) $request->get('prodi', ''));      // program_studi
        $kelas  = trim((string) $request->get('kelas', ''));      // kelas
        $angk   = trim((string) $request->get('angkatan', ''));   // angkatan

        $query = Mahasiswa::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('nim', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($prodi !== '') {
            $query->where('program_studi', 'like', "%{$prodi}%");
        }

        if ($kelas !== '') {
            $query->where('kelas', 'like', "%{$kelas}%");
        }

        if ($angk !== '') {
            $query->where('angkatan', 'like', "%{$angk}%");
        }

        $mahasiswas = $query->orderBy('nama')->get();

        return view('admin.mahasiswa', compact(
            'mahasiswas',
            'q',
            'prodi',
            'kelas',
            'angk'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'          => ['required','string','max:255'],
            'program_studi' => ['nullable','string','max:255'],
            'nim'           => ['required', 'digits:9'],
            'kelas'         => ['nullable','string','max:50'],
            'angkatan'      => ['nullable','string','max:10'],
            'email'         => ['required','email','max:255'],
            'password'      => ['required','string','min:4','max:100'],
        ]);

        $errors = [];

        // =========================
        // CEK EMAIL
        // =========================

        if (Mahasiswa::where('email', $data['email'])->exists()) {

            $errors[] =
                'Email '.$data['email'].' sudah digunakan';
        }

        // =========================
        // CEK NIM
        // =========================

        if (Mahasiswa::where('nim', $data['nim'])->exists()) {

            $errors[] =
                'NIM '.$data['nim'].' sudah terdaftar';
        }

        // =========================
        // JIKA ADA ERROR
        // =========================

        if (count($errors) > 0) {

            return back()
                ->withInput()
                ->with('error', $errors);
        }

        $data['password'] = Hash::make($data['password']);

        $mahasiswa = Mahasiswa::create($data);

        return redirect()
            ->route('admin.mahasiswa.index')
            ->with(
                'ok',
                'Data mahasiswa berhasil disimpan.'
            )
            ->with(
                'highlight_id',
                $mahasiswa->id
            );
            }

    public function edit(Mahasiswa $mahasiswa)
    {
        return view('admin.mahasiswa_edit', compact('mahasiswa'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $request->validate([
            'nama'          => ['required','string','max:255'],
            'program_studi' => ['nullable','string','max:255'],
            'nim'           => ['required','digits:9'],
            'kelas'         => ['nullable','string','max:50'],
            'angkatan'      => ['nullable','string','max:10'],
            'email'         => ['required','email','max:255'],
            'password'      => ['nullable','string','min:4','max:100'],

        ], [

            'nim.required' => 'NIM wajib diisi',

            'nim.digits' => 'NIM harus tepat 9 angka',

        ]);
        $errors = [];

        // =========================
        // CEK NIM
        // =========================

        $cekNim = Mahasiswa::where(
            'nim',
            $data['nim']
        )
        ->where('id', '!=', $mahasiswa->id)
        ->exists();

        if ($cekNim) {

            $errors[] =
                'NIM '.$data['nim'].' sudah digunakan';
        }

        // =========================
        // CEK EMAIL
        // =========================

        $cekEmail = Mahasiswa::where(
            'email',
            $data['email']
        )
        ->where('id', '!=', $mahasiswa->id)
        ->exists();

        if ($cekEmail) {

            $errors[] =
                'Email '.$data['email'].' sudah digunakan';
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

        $mahasiswa->update($data);

       return redirect()
                ->route('admin.mahasiswa.index')
                ->with(
                    'ok',
                    'Data mahasiswa berhasil diupdate.'
                )
                ->with(
                    'highlight_id',
                    $mahasiswa->id
                );
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();
        return redirect()->route('admin.mahasiswa.index')->with('ok', 'Data mahasiswa berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xlsx,xls,csv'],

            // optional: supaya setelah import balik ke filter yang sama
            'q' => ['nullable','string','max:255'],
            'prodi' => ['nullable','string','max:255'],
            'kelas' => ['nullable','string','max:50'],
            'angkatan' => ['nullable','string','max:10'],
        ]);

        Excel::import(new MahasiswaImport, $request->file('file'));
        $lastMahasiswa = \App\Models\Mahasiswa::latest()->first();
        return redirect()->route('admin.mahasiswa.index', [
            'q' => $request->input('q', ''),
            'prodi' => $request->input('prodi', ''),
            'kelas' => $request->input('kelas', ''),
            'angkatan' => $request->input('angkatan', ''),
        ])->with(
            'ok',
            'Import mahasiswa berhasil.'
        )->with(
            'highlight_id',
            $lastMahasiswa?->id
        );
    }
}
