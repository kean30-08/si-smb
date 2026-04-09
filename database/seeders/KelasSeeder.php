<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar kelas Sekolah Minggu yang akan di-generate
        $daftarKelas = [
            'Kelas PAUD & TK',
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