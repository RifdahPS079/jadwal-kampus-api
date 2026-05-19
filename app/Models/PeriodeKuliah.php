<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeKuliah extends Model
{
    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'tanggal_mulai',
        'aktif',
    ];
}