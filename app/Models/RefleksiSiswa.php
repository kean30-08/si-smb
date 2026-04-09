<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefleksiSiswa extends Model
{
    use HasFactory;
    protected $guarded = []; // Izinkan semua kolom diisi

    // Tambahkan relasi ini agar nanti mudah dipanggil di halaman index/show Admin
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}