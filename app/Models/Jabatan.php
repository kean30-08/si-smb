<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Define the relationship to the Pengajar model (One Jabatan has Many Pengajars)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Pengajar, Jabatan>
     */
    public function pengajars()
    {
        return $this->hasMany(Pengajar::class);
    }
}