<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    
    // Karena nama tabel Anda 'absensis' (berakhiran s), Laravel otomatis mengenalinya, 
    // tapi tidak ada salahnya dideklarasikan agar lebih aman.
    protected $table = 'absensis';
    protected $guarded = [];

    // Relasi ke tabel Agenda (1 Absen terikat pada 1 Agenda/Jadwal)
    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    // Relasi ke tabel Siswa (1 Absen adalah milik 1 Siswa)
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}