<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiPengajar extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Summary of agenda
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Agenda, AbsensiPengajar>
     */
    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    /**
     * Summary of pengajar
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Pengajar, AbsensiPengajar>
     */
    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class);
    }
}