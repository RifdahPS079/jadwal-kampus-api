<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\PengampuMataKuliah;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataKuliahImport implements ToCollection, WithHeadingRow
{
    public array $insertedIds = [];
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $kode = trim((string)($row['kode_mk'] ?? ''));
            $nama = trim((string)($row['nama_mk'] ?? ''));

            if ($kode === '' || $nama === '') {
                continue;
            }

            $semester = isset($row['semester']) ? (int) $row['semester'] : 1;
            $tahunAjaran = trim((string)($row['tahun_ajaran'] ?? ''));
            if ($tahunAjaran === '') {
                $tahunAjaran = date('Y') . '/' . (date('Y') + 1);
            }

            $mk = MataKuliah::updateOrCreate(
                ['kode_mk' => $kode],
                [
                    'nama_mk'       => $nama,
                    'program_studi' => $row['program_studi'] ?? null,
                    'semester'      => $semester,
                    'sks'           => isset($row['sks']) ? (int)$row['sks'] : 0,
                ]
            );

            $this->insertedIds[] = $mk->id;

            // set pengampu jika ada kode_dosen
            $kodeDosen = trim((string)($row['kode_dosen'] ?? ''));
            if ($kodeDosen !== '') {
                $dosen = Dosen::where('kode_dosen', $kodeDosen)->first();

                if ($dosen) {
                    PengampuMataKuliah::updateOrCreate(
                        [
                            'mata_kuliah_id' => $mk->id,
                            'semester'       => $semester,
                            'tahun_ajaran'   => $tahunAjaran,
                        ],
                        [
                            'dosen_id' => $dosen->id,
                        ]
                    );
                }
            }
        }
    }
}
