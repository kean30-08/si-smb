<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\Kelas;
use App\Models\TahunAjaran; // <-- Jangan lupa import Model ini
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. SEEDER TAHUN AJARAN (SANGAT PENTING AGAR SISTEM BISA BERJALAN)
        TahunAjaran::firstOrCreate(
            ['tahun_ajaran' => '2025/2026 Ganjil'],
            ['status' => 'aktif'] // Langsung set aktif
        );

        // 2. SEEDER AKUN ADMIN
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'), 
        ]);

        // 3. SEEDER TABEL JABATAN
        $daftarJabatan = [
            'Guru Sekolah Minggu',
            'Kepala Sekolah Minggu',
            'Wakil Kepala Sekolah',
            'Sekretaris Sekolah Minggu',
            'Humas Sekolah Minggu',
            'Bendahara Sekolah Minggu',
        ];

        foreach ($daftarJabatan as $nama) {
            Jabatan::firstOrCreate([
                'nama_jabatan' => $nama
            ]);
        }

        // 4. SEEDER DAFTAR KELAS (Disesuaikan dengan format baru agar Auto-Increment berfungsi)
        $daftarKelas = [
            'Kelas PAUD',
            'Kelas TK',
            'Kelas 1 SD',
            'Kelas 2 SD',
            'Kelas 3 SD',
            'Kelas 4 SD',
            'Kelas 5 SD',
            'Kelas 6 SD',
        ];

        foreach ($daftarKelas as $kelas) {
            // Menggunakan firstOrCreate agar tidak terjadi error duplikasi 
            // jika seeder dijalankan lebih dari 1 kali
            Kelas::firstOrCreate([
                'nama_kelas' => $kelas
            ]);
        }
    }
}