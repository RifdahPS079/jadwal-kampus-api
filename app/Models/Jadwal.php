<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwals';

    protected $fillable = [
        'pengampu_id',
        'ruangan_id',
        'waktu_id',
        'program_studi',
        'kelas',
        'status',
    ];

    // Jadwal milik 1 pengampu
    public function pengampu()
    {
        return $this->belongsTo(PengampuMataKuliah::class, 'pengampu_id');
    }

    // Jadwal menggunakan 1 ruangan
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    // Jadwal menggunakan 1 waktu
    public function waktu()
    {
        return $this->belongsTo(Waktu::class, 'waktu_id');
    }
}
