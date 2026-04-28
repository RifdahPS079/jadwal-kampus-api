<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // ✅ INI WAJIB

class Notifikasi extends Model
{
    protected $fillable = [
        'role',
        'user_id',
        'tipe',
        'pesan',
        'is_read'
    ];
}