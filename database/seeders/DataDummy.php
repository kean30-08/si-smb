<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pengajar;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Agenda;
use App\Models\Absensi;
use App\Models\AbsensiPengajar;
use App\Models\RefleksiSiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DataDummy extends Seeder
{
    /**
     * Seed the application's database.
     * * @return void
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

        // 1. Generate Pengajar
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
                    'status' => 'aktif', // Menambahkan status eksplisit
                ]
            );
        }

        // 2. Generate Kelas
        $daftarKelas = [
            'Kelas TK', 'Kelas 1 SD', 'Kelas 2 SD', 'Kelas 3 SD', 
            'Kelas 4 SD', 'Kelas 5 SD', 'Kelas 6 SD'
        ];

        foreach ($daftarKelas as $kelas) {
            Kelas::firstOrCreate(['nama_kelas' => $kelas]);
        }

        // 3. Generate Siswa
        $semuaKelas = Kelas::all();
        
        $lastSiswa = Siswa::orderBy('id', 'desc')->first();
        $nisCounter = $lastSiswa ? (int) substr($lastSiswa->nis, 2) + 1 : 1;

        foreach ($semuaKelas as $kelas) {
            for ($i = 1; $i <= 3; $i++) {
                $jkSiswa = $faker->randomElement(['L', 'P']);
                
                $namaDepanSiswa = $jkSiswa == 'L' ? $faker->firstNameMale : $faker->firstNameFemale;
                $namaSiswa = $namaDepanSiswa . ' ' . $faker->randomElement($safeLastNames);
                
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
                        'email_orang_tua' => null, // KOSONGKAN UNTUK TESTING MAILTRAP
                        'nomor_hp_orang_tua' => $faker->phoneNumber,
                        'alamat' => $faker->address,
                        'status' => 'aktif',
                        'total_poin' => 0,
                    ]
                );
                $nisCounter++;
            }
        }

        // 4. Generate Agenda (Yang sudah lewat / selesai)
        $agendas = [];
        $semuaPengajarIds = Pengajar::pluck('id')->toArray();

        for ($minggu = 4; $minggu >= 1; $minggu--) {
            $agenda = Agenda::firstOrCreate(
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
            
            // PERBAIKAN: Hubungkan PIC menggunakan tabel Pivot Many-to-Many
            if (!empty($semuaPengajarIds)) {
                // Ambil 1 atau 2 pengajar secara acak sebagai PIC
                $randomPics = $faker->randomElements($semuaPengajarIds, rand(1, 2));
                $agenda->penanggungJawab()->syncWithoutDetaching($randomPics);
            }
            
            $agendas[] = $agenda;
        }

        // Generate Agenda untuk besok (Akan datang)
        $agendaBesok = Agenda::firstOrCreate(
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
        
        if (!empty($semuaPengajarIds)) {
            $agendaBesok->penanggungJawab()->syncWithoutDetaching($faker->randomElements($semuaPengajarIds, rand(1, 2)));
        }

        // 5. Generate Absensi dan Refleksi untuk Agenda yang sudah selesai
        $semuaSiswa = Siswa::all();
        $semuaPengajar = Pengajar::all();
        
        $statusSiswa = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'];
        $statusPengajar = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit'];

        $dummyRangkuman = [
            'Hari ini belajar tentang riwayat Sang Buddha.',
            'Mendengarkan Dhammadesana dan bermeditasi bersama.',
            'Kegiatan hari ini sangat seru, kita menyanyi lagu Buddhis dan mewarnai.',
            'Belajar tentang hukum karma dan mempraktikkan perbuatan baik.'
        ];
        $dummyDisukai = [
            'Suka saat bagian menyanyi.',
            'Mewarnai gambar teratai.',
            'Cerita Jataka yang dibawakan sangat menarik.',
            'Saat kuis berhadiah.'
        ];
        $dummyKurangDisukai = [
            'Meditasinya terasa terlalu lama.',
            'Tidak ada, semuanya menyenangkan.',
            'Suara mic-nya agak kurang jelas di belakang.',
            'Sedikit mengantuk saat mendengarkan ceramah.'
        ];

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

                // Generate Refleksi Siswa (Peluang 60%)
                if ($status === 'hadir' && rand(1, 100) <= 60) {
                    
                    $waktuIsi = Carbon::parse($agenda->tanggal . ' 10:' . rand(10, 59) . ':00');

                    RefleksiSiswa::firstOrCreate(
                        [
                            'tanggal' => $agenda->tanggal,
                            'nis' => $siswa->nis,
                        ],
                        [
                            'nama_siswa' => $siswa->nama_lengkap,
                            'kelas_id' => $siswa->kelas_id, // PERBAIKAN: Menggunakan ID Kelas
                            'nama_orang_tua' => $siswa->nama_orang_tua,
                            'email_orang_tua' => null, // KOSONGKAN UNTUK TESTING MAILTRAP
                            'rangkuman' => $faker->randomElement($dummyRangkuman) . ' ' . $faker->sentence(3),
                            'bagian_disukai' => $faker->randomElement($dummyDisukai),
                            'bagian_kurang_disukai' => $faker->randomElement($dummyKurangDisukai),
                            'created_at' => $waktuIsi,
                            'updated_at' => $waktuIsi,
                        ]
                    );
                }
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