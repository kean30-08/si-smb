<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi
    protected $guarded = [];

    // Relasi: Setiap Siswa milik 1 Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}