<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LaporanInsentif extends Model
{
    protected $fillable = ['pengajar_id', 'bulan', 'tahun', 'file_path'];

    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class);
    }
}