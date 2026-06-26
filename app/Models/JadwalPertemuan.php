<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPertemuan extends Model
{
    protected $table = 'jadwal_pertemuans';

    protected $fillable = [
        'jadwal_id',
        'pertemuan_ke',
        'waktu_id',
        'ruangan_id',
        'status',
        'alasan_batal',
        'alasan_tolak',
        'disetujui_pada',
        'ditolak_pada',
        'dibaca_admin_pada',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function waktu()
    {
        return $this->belongsTo(Waktu::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}