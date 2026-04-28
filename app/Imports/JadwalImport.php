<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\MataKuliah;
use App\Models\PengampuMataKuliah;
use App\Models\Ruangan;
use App\Models\Waktu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class JadwalImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $hari        = trim((string)($row['hari'] ?? ''));
            $jamMulai    = $this->toTime($row['jam_mulai'] ?? null);
            $jamSelesai  = $this->toTime($row['jam_selesai'] ?? null);

            $kodeRuangan = trim((string)($row['kode_ruangan'] ?? ''));
            $kelas       = trim((string)($row['kelas'] ?? ''));
            $prodi       = trim((string)($row['program_studi'] ?? ''));

            $kodeMk      = trim((string)($row['kode_mk'] ?? ''));
            $kodeDosen   = trim((string)($row['kode_dosen'] ?? ''));

            $semester    = (int)($row['semester'] ?? 1);
            $tahunAjaran = trim((string)($row['tahun_ajaran'] ?? ''));

            // skip kalau data inti kosong
            if ($hari === '' || !$jamMulai || !$jamSelesai || $kodeRuangan === '' || $kelas === '' || $kodeMk === '' || $kodeDosen === '') {
                continue;
            }

            // cari waktu_id dari (hari + jam_mulai + jam_selesai)
            $waktu = Waktu::where('hari', $hari)
                ->where('jam_mulai', $jamMulai)
                ->where('jam_selesai', $jamSelesai)
                ->first();

            if (!$waktu) {
                // waktu belum ada di master -> skip
                continue;
            }

            // cari ruangan_id dari kode_ruangan
            $ruangan = Ruangan::where('kode_ruangan', $kodeRuangan)->first();
            if (!$ruangan) continue;

            // cari mata kuliah & dosen
            $mk = MataKuliah::where('kode_mk', $kodeMk)->first();
            if (!$mk) continue;

            $dosen = Dosen::where('kode_dosen', $kodeDosen)->first();
            if (!$dosen) continue;

            // default prodi kalau kosong -> ambil dari mata kuliah
            if ($prodi === '') $prodi = (string)($mk->program_studi ?? '');

            // default tahun ajaran kalau kosong
            if ($tahunAjaran === '') {
                $tahunAjaran = PengampuMataKuliah::max('tahun_ajaran') ?? (date('Y') . '/' . (date('Y') + 1));
            }

            // pastikan pengampu ada (dosen+mk+semester+tahun ajaran)
            $pengampu = PengampuMataKuliah::updateOrCreate(
                [
                    'mata_kuliah_id' => $mk->id,
                    'semester'       => $semester,
                    'tahun_ajaran'   => $tahunAjaran,
                ],
                [
                    'dosen_id' => $dosen->id,
                ]
            );

            // simpan jadwal (1 cell = 1 jadwal)
            Jadwal::updateOrCreate(
                [
                    'ruangan_id'    => $ruangan->id,
                    'waktu_id'      => $waktu->id,
                    'kelas'         => $kelas,
                    'program_studi' => $prodi,
                ],
                [
                    'pengampu_id' => $pengampu->id,
                ]
            );
        }
    }

    private function toTime($value): ?string
    {
        if ($value === null) return null;

        // kalau excel kasih angka (serial time)
        if (is_numeric($value)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject($value);
                return $dt->format('H:i:s');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $str = trim((string)$value);
        if ($str === '') return null;

        // dukung "07:30" atau "07:30:00"
        if (preg_match('/^\d{2}:\d{2}$/', $str)) return $str . ':00';
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $str)) return $str;

        return null;
    }
}
