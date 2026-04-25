<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function penanggungJawab()
    {
        // Ubah dari belongsTo menjadi belongsToMany
        return $this->belongsToMany(Pengajar::class, 'agenda_pengajar', 'agenda_id', 'pengajar_id');
    }

    // RELASI BARU
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}