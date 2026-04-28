<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliahs';

    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'sks',
        'program_studi',
        'semester',
    ];

    protected $casts = [
        'sks' => 'integer',
        'semester' => 'integer',
    ];

    // RELASI ASLI
    public function pengampuMataKuliahs()
    {
        return $this->hasMany(PengampuMataKuliah::class, 'mata_kuliah_id');
    }

    // ✅ ALIAS (BIAR CONTROLLER/VIEW YANG PAKAI "pengampus" TIDAK ERROR)
   public function pengampus()
    {
        return $this->hasMany(PengampuMataKuliah::class);
    }

    public function pengampu()
    {
        return $this->hasMany(
            \App\Models\PengampuMataKuliah::class,
            'mata_kuliah_id'
        );
    }

}
