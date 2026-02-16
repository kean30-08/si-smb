<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $guarded = []; // Izinkan semua kolom
    
    // Relasi: Satu Kelas punya banyak Siswa
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
}