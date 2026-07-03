<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemberitahuan extends Model
{
    use HasFactory;

    // Tambahkan 'gambar' ke sini
    protected $fillable = ['judul', 'deskripsi', 'gambar', 'status'];
}