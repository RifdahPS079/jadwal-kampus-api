<?php

namespace App\Imports;

use App\Models\Waktu;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class WaktuImport implements ToCollection, WithHeadingRow
{
    public array $insertedIds = [];
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $mulai = $this->toTime($row['jam_mulai'] ?? null);
            $selesai = $this->toTime($row['jam_selesai'] ?? null);
            $hari = $this->normalizeHari($row['hari'] ?? null);
            $tanggal = $this->toDate($row['tanggal'] ?? null); // boleh kosong

            if (!$hari || !$mulai || !$selesai) {
                continue;
            }

            // unik berdasarkan HARI + JAM. tanggal hanya ikut di-update (opsional)
            $waktu = Waktu::updateOrCreate(
                ['hari' => $hari, 'jam_mulai' => $mulai, 'jam_selesai' => $selesai],
                ['tanggal' => $tanggal]
            );

            $this->insertedIds[] = $waktu->id;
        }
    }

    private function normalizeHari($value): ?string
    {
        if ($value === null) return null;
        $v = trim(mb_strtolower((string) $value));

        $map = [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            "jum'at" => 'Jumat',
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu',
        ];

        return $map[$v] ?? null;
    }

    private function toTime($value): ?string
    {
        if ($value === null) return null;

        // kalau Excel kasih DateTime
        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i:s');
        }

        $v = trim((string) $value);
        if ($v === '') return null;

        // dukung "07:00" atau "07:00:00"
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $v)) {
            return strlen($v) === 5 ? $v.':00' : $v;
        }

        // kalau numeric (serial excel)
        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('H:i:s');
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    private function toDate($value): ?string
    {
        if ($value === null) return null;

        // kalau Excel kasih DateTime
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $v = trim((string) $value);
        if ($v === '') return null;

        // numeric serial excel
        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        // dukung "2026-01-07" atau "07/01/2026"
        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
