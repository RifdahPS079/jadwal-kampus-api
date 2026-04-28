<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Mahasiswa extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'nama',
        'nim',
        'program_studi',
        'email',
        'kelas',
        'angkatan',
        'password',
        'dosen_id'
    ];

    protected $hidden = ['password'];

    // RELASI
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => 'mahasiswa'];
    }

}
