<?php

namespace App\Imports;

use App\Models\Dosen;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DosenImport implements ToModel, WithHeadingRow
{
    // 🔥 simpan daftar duplicate
    public static $duplicates = [];

    public function model(array $row)
    {
        // wajib: kode_dosen, nama, password
        $kode = trim($row['kode_dosen'] ?? '');
        $nama = trim($row['nama'] ?? '');
        $pass = (string)($row['password'] ?? '');

        if ($kode === '' || $nama === '') {
            return null;
        }

        // password kosong → skip
        if (trim($pass) === '') {
            return null;
        }

        $nidn = trim($row['nidn'] ?? '');

        // ====================================
        // CEK APAKAH KODE DOSEN SUDAH ADA
        // ====================================

        $existingByKode = Dosen::where(
            'kode_dosen',
            $kode
        )->first();

                // ====================================
        // CEK DUPLICATE NIDN
        // ====================================

        if ($nidn !== '') {

            $existingNidn = Dosen::where(
                'nidn',
                $nidn
            )->first();

            if (
                $existingNidn &&
                (
                    !$existingByKode ||
                    $existingNidn->id !== $existingByKode->id
                )
            ) {

                self::$duplicates[] =
                    'NIDN '.$nidn.' sudah tersedia';

                return null;
            }
        }

        // ====================================
        // CEK DUPLICATE EMAIL
        // ====================================

        $email = trim($row['email'] ?? '');

        if ($email !== '') {

            $existingEmail = Dosen::where(
                'email',
                $email
            )->first();

            if (
                $existingEmail &&
                (
                    !$existingByKode ||
                    $existingEmail->id !== $existingByKode->id
                )
            ) {

                self::$duplicates[] =
                    'Email '.$email.' sudah tersedia';

                return null;
            }
        }

        // ====================================
        // CEK DUPLICATE KODE DOSEN
        // ====================================

        $existingKodeLain = Dosen::where(
            'kode_dosen',
            $kode
        )->first();

        if (
            $existingKodeLain &&
            $nidn !== '' &&
            $existingKodeLain->nidn !== $nidn
        ) {

            self::$duplicates[] =
                'Kode Dosen '.$kode.' sudah digunakan';

            return null;
        }
        // ====================================
        // UPDATE ATAU CREATE
        // ====================================

        return Dosen::updateOrCreate(
            [
                'kode_dosen' => $kode
            ],
            [
                'nama'          => $nama,
                'program_studi' => $row['program_studi'] ?? null,
                'nidn'          => $nidn ?: null,
                'email'         => $row['email'] ?? null,
                'password'      => Hash::make($pass),
            ]
        );
    }
}
