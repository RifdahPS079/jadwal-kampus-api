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

        return view('admin.dosen', compact('dosens', 'q', 'prodi', 'nidn', 'urut'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_dosen'    => ['required','string','max:50'],
            'nama'          => ['required','string','max:255'],
            'program_studi' => ['nullable','string','max:255'],
            'nidn'          => ['nullable','string','max:50'],
            'email'         => ['nullable','email','max:255'],
            'password'      => ['required','string','min:6'],
        ]);

        $data['password'] = Hash::make($data['password']);

        Dosen::updateOrCreate(
            ['kode_dosen' => $data['kode_dosen']],
            $data
        );

        return redirect()->route('admin.dosen.index')->with('ok', 'Data dosen berhasil disimpan.');
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
            'nidn'          => ['nullable','string','max:50'],
            'email'         => ['nullable','email','max:255'],
            'password'      => ['nullable','string','min:6'],
        ]);

        $exists = Dosen::where('kode_dosen', $data['kode_dosen'])
            ->where('id', '!=', $dosen->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['kode_dosen' => 'Kode dosen sudah dipakai dosen lain.'])
                ->withInput();
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $dosen->update($data);

        return redirect()->route('admin.dosen.index')->with('ok', 'Data dosen berhasil diupdate.');
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->delete();
        return redirect()->route('admin.dosen.index')->with('ok', 'Data dosen berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xlsx,xls,csv']
        ]);

        Excel::import(new DosenImport, $request->file('file'));

        // balik ke index (filter tidak wajib dipertahankan di redirect ini)
        return redirect()->route('admin.dosen.index')->with('ok', 'Import dosen berhasil.');
    }
}
