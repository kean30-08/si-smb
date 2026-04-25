<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $guarded = [];

    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    public function nilaiKehadiran()
    {
        return $this->hasMany(NilaiKehadiran::class);
    }
}