<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiPengajar extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function agenda() { return $this->belongsTo(Agenda::class); }
    public function pengajar() { return $this->belongsTo(Pengajar::class); }
}