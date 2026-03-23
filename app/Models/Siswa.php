<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Summary of kelas
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Kelas, Siswa>
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}