<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pengajar;
use App\Models\Agenda;
use App\Models\User;
use App\Models\Absensi;         
use App\Models\AbsensiPengajar;  
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class JabatanSeeder extends Seeder
{
    public function run()
    {
        // 
        // SEEDER TABEL JABATAN
        // 
        $daftarJabatan = [
            'Guru Sekolah Minggu',
            'Kepala Sekolah Minggu',
            'Wakil Kepala Sekolah',
            'Sekretaris Sekolah Minggu',
            'Humas Sekolah Minggu',
            'Bendahara Sekolah Minggu',
            'Pengurus Vihara',
        ];

        // Looping untuk memasukkan data ke tabel jabatans
        foreach ($daftarJabatan as $nama) {
            Jabatan::create([
                'nama_jabatan' => $nama
            ]);
        }
    }
}