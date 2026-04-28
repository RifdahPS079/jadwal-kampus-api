<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangans';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'gedung',
    ];

    // 1 Ruangan dipakai oleh banyak jadwal
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'ruangan_id');
    }
}
