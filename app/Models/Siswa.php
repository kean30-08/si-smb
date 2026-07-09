<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function riwayatHistori()
    {
        return $this->hasMany(HistoriSiswa::class);
    }

    /**
     * PERBAIKAN: Relasi ini sekarang akan SELALU mengambil kelas TERBARU siswa.
     * Tidak peduli Admin sedang mengaktifkan Tahun Ajaran lampau atau masa depan.
     */
    public function historiAktif()
    {
        return $this->hasOne(HistoriSiswa::class)->latestOfMany();
    }
}