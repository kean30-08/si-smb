<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    /**
     * Relasi ke tabel pendaftaran/buku induk tahunan
     */
    public function nilaiKehadirans()
    {
        return $this->hasMany(NilaiKehadiran::class);
    }
}