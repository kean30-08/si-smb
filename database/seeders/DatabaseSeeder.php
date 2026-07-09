<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. SEEDER TAHUN AJARAN UTAMA
        TahunAjaran::firstOrCreate(
            ['tahun_ajaran' => '2025/2026 Ganjil'],
            ['status' => 'aktif']
        );

        // 2. SEEDER AKUN ADMIN MASTER
        User::firstOrCreate(
            ['email' => 'admin.smb.vdc@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin81238'), 
                'email_verified_at' => now(),
            ]
        );

        // 3. SEEDER TABEL JABATAN RESMI
        $daftarJabatan = [
            'Guru Sekolah Minggu',
            'Kepala Sekolah Minggu',
            'Wakil Kepala Sekolah',
            'Sekretaris Sekolah Minggu',
            'Humas Sekolah Minggu',
            'Bendahara Sekolah Minggu',
        ];

        foreach ($daftarJabatan as $nama) {
            Jabatan::firstOrCreate(['nama_jabatan' => $nama]);
        }

        // 4. SEEDER DAFTAR KELAS RESMI
        $daftarKelas = [
            'Kelas PG',
            'Kelas TK A',
            'Kelas TK B',
            'Kelas 1 SD',
            'Kelas 2 SD',
            'Kelas 3 SD',
            'Kelas 4 SD',
            'Kelas 5 SD',
            'Kelas 6 SD',
        ];

        foreach ($daftarKelas as $kelas) {
            Kelas::firstOrCreate(['nama_kelas' => $kelas]);
        }
    }
}