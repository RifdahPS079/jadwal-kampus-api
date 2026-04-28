<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waktu extends Model
{
    protected $table = 'waktus';

    protected $fillable = [
        'hari',
        'jam_mulai',
        'jam_selesai',
        'tanggal',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i:s',   // atau 'string' kalau kamu simpan TIME
        'jam_selesai' => 'datetime:H:i:s',
        'tanggal' => 'date',
    ];

    // 1 Waktu dipakai oleh banyak jadwal
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'waktu_id');
    }
}
