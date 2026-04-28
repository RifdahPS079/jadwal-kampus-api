<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Dosen extends Authenticatable implements JWTSubject
{
    protected $table = 'dosens';

    protected $fillable = [
        'nama',
        'nidn',
        'kode_dosen',
        'program_studi',
        'email',
        'password',
    ];

    protected $hidden = ['password'];

    // JWT wajib
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => 'dosen'];
    }

    // RELASI
    public function mahasiswas()
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function pengampuMataKuliahs()
    {
        return $this->hasMany(PengampuMataKuliah::class, 'dosen_id');
    }

    public function pengampus()
    {
        return $this->hasMany(PengampuMataKuliah::class);
    }
    
    public function jadwals()
    {
        return $this->hasManyThrough(
            Jadwal::class,
            PengampuMataKuliah::class,
            'dosen_id',     // FK di pengampu_mata_kuliahs
            'pengampu_id',  // FK di jadwals
            'id',
            'id'
        );
    }
}
