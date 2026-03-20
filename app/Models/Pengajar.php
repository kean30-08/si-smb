<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajar extends Model
{
    use HasFactory;

    protected $guarded = []; // Izinkan semua kolom diisi

    // Relasi ke tabel Users (Untuk data Login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tambahkan Relasi ke tabel Jabatans
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}