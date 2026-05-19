<?php

namespace App\Imports;

use App\Models\Ruangan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuanganImport implements ToCollection, WithHeadingRow
{
    public array $insertedIds = [];
    public array $duplicateCodes = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $kode = trim((string) ($row['kode_ruangan'] ?? ''));

            if ($kode === '') {
                continue;
            }

            // 🔥 CEK DUPLIKAT
            $exists = Ruangan::where('kode_ruangan', $kode)->exists();

            if ($exists) {

                $this->duplicateCodes[] = $kode;

                continue;
            }

            // 🔥 SIMPAN BARU
            $ruangan = Ruangan::create([
                'kode_ruangan' => $kode,
                'nama_ruangan' => trim((string) ($row['nama_ruangan'] ?? '')) ?: null,
                'gedung'       => trim((string) ($row['gedung'] ?? '')) ?: null,
            ]);

            $this->insertedIds[] = $ruangan->id;
        }
    }
}
