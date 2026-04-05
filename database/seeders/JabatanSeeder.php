<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class JabatanSeeder extends Seeder
{
    public function run()
    {
        // SEEDER TABEL JABATAN
        $daftarJabatan = [
            'Guru Sekolah Minggu',
            'Kepala Sekolah Minggu',
            'Wakil Kepala Sekolah',
            'Sekretaris Sekolah Minggu',
            'Humas Sekolah Minggu',
            'Bendahara Sekolah Minggu',
        ];

        foreach ($daftarJabatan as $nama) {
            Jabatan::create([
                'nama_jabatan' => $nama
            ]);
        }
    }
}