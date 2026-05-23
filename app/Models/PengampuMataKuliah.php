<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengampuMataKuliah extends Model
{
    protected $table = 'pengampu_mata_kuliahs';

    protected $fillable = [
        'dosen_id',
        'dosen2_id',
        'mata_kuliah_id',
        'semester',
        'tahun_ajaran',
    ];

    // Pengampu milik 1 dosen
   public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }


    // 1 Pengampu bisa punya banyak jadwal
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'pengampu_id');
    }

    public function dosen2()
    {
        return $this->belongsTo(Dosen::class, 'dosen2_id');
    }
}
