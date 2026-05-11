<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Waktu;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RuanganImport;
use App\Imports\WaktuImport;


class AdminRuanganWaktuWebController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();

        $waktus = Waktu::orderByRaw("FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jam_mulai')
            ->get();

        return view('admin.ruangan_waktu', compact('ruangans', 'waktus'));
    }

    // =========================
    // RUANGAN
    // =========================
    public function storeRuangan(Request $request)
    {
        $data = $request->validate([
            'kode_ruangan' => ['required', 'string', 'max:50'],
            'nama_ruangan' => ['nullable', 'string', 'max:255'],
            'gedung'       => ['nullable', 'string', 'max:255'],
        ]);

        // kalau kamu mau unik berdasarkan kode_ruangan:
        Ruangan::updateOrCreate(
            ['kode_ruangan' => $data['kode_ruangan']],
            $data
        );

        return redirect()->route('admin.ruangan_waktu.index')->with('ok', 'Ruangan berhasil disimpan.');
    }

    public function editRuangan(Ruangan $ruangan)
    {
        // tampilkan form edit khusus
        return view('admin.ruangan_edit', compact('ruangan'));
    }

    public function updateRuangan(Request $request, Ruangan $ruangan)
    {
        $data = $request->validate([
            'kode_ruangan' => ['required', 'string', 'max:50'],
            'nama_ruangan' => ['nullable', 'string', 'max:255'],
            'gedung'       => ['nullable', 'string', 'max:255'],
        ]);

        // cegah kode_ruangan bentrok
        $exists = Ruangan::where('kode_ruangan', $data['kode_ruangan'])
            ->where('id', '!=', $ruangan->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['kode_ruangan' => 'Kode ruangan sudah dipakai.'])->withInput();
        }

        $ruangan->update($data);

       return redirect()
        ->route('admin.ruangan_waktu.index')
        ->with('ok', 'Ruangan berhasil diupdate.')
        ->with('highlight_ruangan_id', $ruangan->id);
    }

    public function destroyRuangan(Ruangan $ruangan)
    {
        $ruangan->delete();
        return redirect()->route('admin.ruangan_waktu.index')->with('ok', 'Ruangan berhasil dihapus.');
    }

    // =========================
    // WAKTU
    // =========================
    public function storeWaktu(Request $request)
    {
        $data = $request->validate([
            'jam_mulai'   => ['required'],
            'jam_selesai' => ['required'],
            'hari'        => ['required','string','max:15'],
            'tanggal'     => ['nullable','date'],
        ]);

        \App\Models\Waktu::updateOrCreate(
            ['hari' => $data['hari'], 'jam_mulai' => $data['jam_mulai'], 'jam_selesai' => $data['jam_selesai']],
            ['tanggal' => $data['tanggal'] ?? null]
        );

        return redirect()->route('admin.ruangan_waktu.index')->with('ok', 'Waktu berhasil disimpan.');
    }

    public function editWaktu(Waktu $waktu)
    {
        return view('admin.waktu_edit', compact('waktu'));
    }

    public function updateWaktu(Request $request, Waktu $waktu)
    {
        $data = $request->validate([
            'jam_mulai'   => ['required'],
            'jam_selesai' => ['required'],
        ]);

        $waktu->update($data);

        return redirect()
            ->route('admin.ruangan_waktu.index')
            ->with('ok', 'Waktu berhasil diupdate.')
            ->with('highlight_waktu_id', $waktu->id);
    }

    public function destroyWaktu(Waktu $waktu)
    {
        $waktu->delete();
        return redirect()->route('admin.ruangan_waktu.index')->with('ok', 'Waktu berhasil dihapus.');
    }

   public function importRuangan(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xlsx,xls,csv'],
        ]);

        $import = new RuanganImport;

        Excel::import($import, $request->file('file'));

        return redirect()
            ->route('admin.ruangan_waktu.index')
            ->with('ok', 'Import ruangan berhasil.')
            ->with('highlight_ruangan_ids', $import->insertedIds ?? []);
    }

    public function importWaktu(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xlsx,xls,csv'],
        ]);

        $import = new WaktuImport;

        Excel::import($import, $request->file('file'));

        return redirect()
            ->route('admin.ruangan_waktu.index')
            ->with('ok', 'Import waktu berhasil.')
            ->with('highlight_waktu_ids', $import->insertedIds ?? []);
    }
}
