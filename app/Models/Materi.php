<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relasi: 1 Materi milik 1 Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}