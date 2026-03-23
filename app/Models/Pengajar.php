<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajar extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Summary of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Pengajar>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of jabatan
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Jabatan, Pengajar>
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}