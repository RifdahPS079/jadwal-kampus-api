<?php

namespace App\Imports;

use App\Models\Dosen;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DosenImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // wajib: kode_dosen, nama, password (minimal)
        $kode = trim($row['kode_dosen'] ?? '');
        $nama = trim($row['nama'] ?? '');
        $pass = (string)($row['password'] ?? '');

        if ($kode === '' || $nama === '') {
            return null; // skip baris kosong
        }

        // kalau password kosong, jangan lanjut (biar aman)
        if (trim($pass) === '') {
            return null;
        }

        // update kalau sudah ada kode_dosen, else create
        return Dosen::updateOrCreate(
            ['kode_dosen' => $kode],
            [
                'nama'          => $nama,
                'program_studi' => $row['program_studi'] ?? null,
                'nidn'          => $row['nidn'] ?? null,
                'email'         => $row['email'] ?? null,
                'password'      => Hash::make($pass),
            ]
        );
    }
}
