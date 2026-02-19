<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $guarded = []; // Izinkan semua kolom diisi (nama_kegiatan, tanggal, dll)
}