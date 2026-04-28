<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $nim = trim((string)($row['nim'] ?? ''));
        if ($nim === '') return null;

        $email = trim((string)($row['email'] ?? ''));

        // password wajib ada saat import
        $plainPass = (string)($row['password'] ?? '');
        if (trim($plainPass) === '') $plainPass = '1234'; // fallback (boleh kamu hapus)

        Mahasiswa::updateOrCreate(
            ['nim' => $nim],
            [
                'nama'          => (string)($row['nama'] ?? '-'),
                'program_studi' => (string)($row['program_studi'] ?? null),
                'kelas'         => (string)($row['kelas'] ?? null),
                'angkatan'      => (string)($row['angkatan'] ?? null),
                'email'         => $email !== '' ? $email : null,
                'password'      => Hash::make($plainPass),
            ]
        );

        return null;
    }
}
