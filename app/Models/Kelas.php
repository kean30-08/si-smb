<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    /**
     * Define the relationship to the Siswa model (One Kelas has Many Siswas)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Siswa, Kelas>
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
}