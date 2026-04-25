<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke seluruh riwayat nilai kehadiran (dari tahun ke tahun)
     */
    public function riwayatKehadiran()
    {
        return $this->hasMany(NilaiKehadiran::class);
    }

    /**
     * Relasi khusus untuk mengambil data kehadiran & kelas di Tahun Ajaran AKTIF saja.
     * Ini sangat mempermudah kita saat memanggil data di view/tabel.
     */
    public function nilaiKehadiranAktif()
    {
        return $this->hasOne(NilaiKehadiran::class)->whereHas('tahunAjaran', function($query) {
            $query->where('status', 'aktif');
        });
    }
}