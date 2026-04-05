<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pengajar;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Agenda;
use App\Models\Absensi;
use App\Models\AbsensiPengajar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DataDummy extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $safeLastNames = [
            'Santoso', 'Wijaya', 'Pratama', 'Kusuma', 'Saputra', 
            'Hidayat', 'Setiawan', 'Gunawan', 'Nugroho', 'Putra', 
            'Lestari', 'Sari', 'Rahmawati', 'Indah', 'Susanti',
            'Purnama', 'Wahyudi', 'Kurniawan', 'Wibowo', 'Permana'
        ];

        //Generate Pengajar
        for ($i = 1; $i <= 3; $i++) {
            $jk = $faker->randomElement(['L', 'P']);
            
            $namaDepan = $jk == 'L' ? $faker->firstNameMale : $faker->firstNameFemale;
            $namaPengajar = $namaDepan . ' ' . $faker->randomElement($safeLastNames);
            
            $emailPengajar = "pengajardummy{$i}@gmail.com";

            $userPengajar = User::firstOrCreate(
                ['email' => $emailPengajar],
                [
                    'name' => $namaPengajar,
                    'password' => Hash::make('pengajar123'),
                ]
            );

            Pengajar::firstOrCreate(
                ['user_id' => $userPengajar->id],
                [
                    'jabatan_id' => 1, 
                    'nama_lengkap' => $namaPengajar,
                    'nomor_hp' => $faker->phoneNumber,
                    'jenis_kelamin' => $jk,
                    'alamat' => $faker->address,
                ]
            );
        }

        //Generate Kelas
        $daftarKelas = [
            'Kelas TK', 'Kelas 1 SD', 'Kelas 2 SD', 'Kelas 3 SD', 
            'Kelas 4 SD', 'Kelas 5 SD', 'Kelas 6 SD'
        ];

        foreach ($daftarKelas as $kelas) {
            Kelas::firstOrCreate(['nama_kelas' => $kelas]);
        }

        //Generate Siswa
        $semuaKelas = Kelas::all();
        
        $lastSiswa = Siswa::orderBy('id', 'desc')->first();
        $nisCounter = $lastSiswa ? (int) substr($lastSiswa->nis, 2) + 1 : 1;

        foreach ($semuaKelas as $kelas) {
            for ($i = 1; $i <= 3; $i++) {
                $jkSiswa = $faker->randomElement(['L', 'P']);
                
                // Nama Siswa
                $namaDepanSiswa = $jkSiswa == 'L' ? $faker->firstNameMale : $faker->firstNameFemale;
                $namaSiswa = $namaDepanSiswa . ' ' . $faker->randomElement($safeLastNames);
                
                // Nama Orang Tua
                $panggilanOrtu = $faker->randomElement(['Bapak ', 'Ibu ']);
                $namaOrtu = $panggilanOrtu . $faker->firstName . ' ' . $faker->randomElement($safeLastNames);
                
                $nis = date('y') . str_pad($nisCounter, 3, '0', STR_PAD_LEFT); 

                Siswa::firstOrCreate(
                    ['nis' => $nis],
                    [
                        'kelas_id' => $kelas->id,
                        'nama_lengkap' => $namaSiswa,
                        'jenis_kelamin' => $jkSiswa,
                        'tempat_lahir' => $faker->city,
                        'tanggal_lahir' => $faker->dateTimeBetween('-12 years', '-6 years')->format('Y-m-d'),
                        'nama_orang_tua' => $namaOrtu,
                        'email_orang_tua' => null, 
                        'nomor_hp_orang_tua' => $faker->phoneNumber,
                        'alamat' => $faker->address,
                        'status' => 'aktif',
                        'total_poin' => 0,
                    ]
                );
                $nisCounter++;
            }
        }

        //Generate Agenda
        $agendas = [];

        for ($minggu = 4; $minggu >= 1; $minggu--) {
            $agendas[] = Agenda::firstOrCreate(
                [
                    'tanggal' => Carbon::now()->subWeeks($minggu)->toDateString(),
                    'nama_kegiatan' => 'Puja Bakti & Sekolah Minggu Ke-' . (5 - $minggu)
                ], 
                [
                    'waktu_mulai' => '08:00:00',
                    'waktu_selesai' => '10:00:00',
                    'deskripsi_rundown' => 'Kegiatan rutin mingguan.',
                    'status' => 'selesai',
                ]
            );
        }

        // Generate Agenda untuk besok
        Agenda::firstOrCreate(
            [
                'tanggal' => Carbon::tomorrow()->toDateString(),
                'nama_kegiatan' => 'Kegiatan Belajar Mengajar'
            ], 
            [
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'deskripsi_rundown' => 'Kegiatan rutin hari minggu untuk kelas TK dan SD.',
                'status' => 'akan datang',
            ]
        );

        // Generate Absensi untuk semua Agenda
        $semuaSiswa = Siswa::all();
        $semuaPengajar = Pengajar::all();
        
        $statusSiswa = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'];
        $statusPengajar = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit'];

        foreach ($agendas as $agenda) {
            
            // Generate Absen Siswa
            foreach ($semuaSiswa as $siswa) {
                $status = $faker->randomElement($statusSiswa);
                $waktuHadir = $status === 'hadir' ? '07:' . str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT) . ':00' : null;
                
                Absensi::firstOrCreate(
                    [
                        'agenda_id' => $agenda->id,
                        'siswa_id' => $siswa->id
                    ],
                    [
                        'status_kehadiran' => $status,
                        'waktu_hadir' => $waktuHadir,
                        'metode_absen' => $status === 'hadir' ? 'barcode' : 'manual'
                    ]
                );
            }

            // Generate Absen Pengajar
            foreach ($semuaPengajar as $pengajar) {
                $status = $faker->randomElement($statusPengajar);
                $waktuHadir = $status === 'hadir' ? '07:' . str_pad(rand(15, 45), 2, '0', STR_PAD_LEFT) . ':00' : null;
                
                AbsensiPengajar::firstOrCreate(
                    [
                        'agenda_id' => $agenda->id,
                        'pengajar_id' => $pengajar->id
                    ],
                    [
                        'status_kehadiran' => $status,
                        'waktu_hadir' => $waktuHadir,
                    ]
                );
            }
        }
    }
}