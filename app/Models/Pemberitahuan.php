<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemberitahuan extends Model
{
    use HasFactory;
    
    // Menggunakan guarded agar semua kolom bisa diisi
    protected $guarded = [];
}