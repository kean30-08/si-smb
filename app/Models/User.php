<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ========================================================
    // TAMBAHAN RELASI & LOGIKA AKSES ADMIN
    // ========================================================

    /**
     * Relasi ke tabel Pengajar (One-to-One)
     */
    public function pengajar()
    {
        return $this->hasOne(Pengajar::class);
    }

    /**
     * Single Source of Truth untuk mengecek apakah user adalah Admin
     * Aturan: Admin Utama (tidak ada di tabel pengajar) ATAU Kepala Sekolah
     */
    public function isAdmin()
    {
        $pengajar = $this->pengajar; // Tarik data dari relasi

        // 1. Jika TIDAK ADA di tabel pengajar, berarti dia Admin Utama (admin@gmail.com)
        if (!$pengajar) {
            return true;
        }

        // 2. Jika dia ada di tabel pengajar, cek jabatannya.
        // Jika nama jabatannya 'Kepala Sekolah Minggu', berikan hak akses Admin
        if ($pengajar->jabatan && $pengajar->jabatan->nama_jabatan === 'Kepala Sekolah Minggu') {
            return true;
        }

        // Jika bukan keduanya, berarti pengajar biasa.
        return false;
    }
}