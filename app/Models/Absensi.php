<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensis';
    protected $guarded = [];

    /**
     * Absensi belongs to an Agenda
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Agenda, Absensi>
     */
    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    /**
     * Absensi belongs to a Siswa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Siswa, Absensi>
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
