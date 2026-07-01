<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToModel, WithHeadingRow
{
    public static $errors = [];

    private function passwordValid(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[@$!%*#?&_]/', $password);
    }

    public function model(array $row)
    {
        $nim = trim((string)($row['nim'] ?? ''));
        if ($nim === '') return null;

        $email = trim((string)($row['email'] ?? ''));

        $plainPass = trim((string)($row['password'] ?? ''));

        if ($plainPass === '') {
            self::$errors[] = 'Password mahasiswa dengan NIM '.$nim.' kosong.';
            return null;
        }

        if (!$this->passwordValid($plainPass)) {
            self::$errors[] =
                'Password mahasiswa dengan NIM '.$nim.' tidak valid. Password minimal 8 karakter, wajib huruf besar, huruf kecil, angka, dan simbol.';
            return null;
        }

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
