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
use App\Models\TahunAjaran;
use App\Models\NilaiKehadiran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DataDummy extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $safeLastNames = [
            'Santoso', 'Wijaya', 'Pratama', 'Kusuma', 'Saputra', 
            'Hidayat', 'Setiawan', 'Gunawan', 'Nugroho', 'Putra', 
            'Lestari', 'Sari', 'Rahmawati', 'Indah', 'Susanti',
            'Purnama', 'Wahyudi', 'Kurniawan', 'Wibowo', 'Permana'
        ];

        // 1. GENERATE TAHUN AJARAN (Lama dan Baru)
        $taLama = TahunAjaran::firstOrCreate(
            ['tahun_ajaran' => '2025/2026 Ganjil'],
            ['status' => 'tidak aktif']
        );
        $taBaru = TahunAjaran::firstOrCreate(
            ['tahun_ajaran' => '2025/2026 Genap'], // Buat nyambung
            ['status' => 'aktif']
        );

        // 2. GENERATE AKUN ADMIN
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // 3. GENERATE JABATAN & PENGAJAR
        $daftarJabatan = [
            'Guru Sekolah Minggu', 'Kepala Sekolah Minggu', 'Wakil Kepala Sekolah',
            'Sekretaris Sekolah Minggu', 'Humas Sekolah Minggu', 'Bendahara Sekolah Minggu',
        ];
        foreach ($daftarJabatan as $nama) {
            \App\Models\Jabatan::firstOrCreate(['nama_jabatan' => $nama]);
        }

        for ($i = 1; $i <= 3; $i++) {
            $jk = $faker->randomElement(['L', 'P']);
            $namaPengajar = ($jk == 'L' ? $faker->firstNameMale : $faker->firstNameFemale) . ' ' . $faker->randomElement($safeLastNames);
            $userPengajar = User::firstOrCreate(
                ['email' => "pengajardummy{$i}@gmail.com"],
                ['name' => $namaPengajar, 'password' => Hash::make('pengajar123')]
            );
            Pengajar::firstOrCreate(
                ['user_id' => $userPengajar->id],
                [
                    'jabatan_id' => 1, 
                    'nama_lengkap' => $namaPengajar,
                    'nomor_hp' => $faker->phoneNumber,
                    'jenis_kelamin' => $jk,
                    'alamat' => $faker->address,
                    'status' => 'aktif', 
                ]
            );
        }

        // 4. GENERATE KELAS
        $daftarKelas = ['Kelas TK', 'Kelas 1 SD', 'Kelas 2 SD', 'Kelas 3 SD', 'Kelas 4 SD', 'Kelas 5 SD', 'Kelas 6 SD'];
        foreach ($daftarKelas as $kelas) {
            Kelas::firstOrCreate(['nama_kelas' => $kelas]);
        }

        // 5. GENERATE SISWA & ENROLLMENT (NILAI KEHADIRAN)
        $semuaKelas = Kelas::all();
        $lastSiswa = Siswa::orderBy('id', 'desc')->first();
        $nisCounter = $lastSiswa ? (int) substr($lastSiswa->nis, 2) + 1 : 1;

        foreach ($semuaKelas as $kelas) {
            for ($i = 1; $i <= 3; $i++) {
                $jkSiswa = $faker->randomElement(['L', 'P']);
                $namaSiswa = ($jkSiswa == 'L' ? $faker->firstNameMale : $faker->firstNameFemale) . ' ' . $faker->randomElement($safeLastNames);
                $nis = date('y') . str_pad($nisCounter, 3, '0', STR_PAD_LEFT); 

                $siswa = Siswa::firstOrCreate(
                    ['nis' => $nis],
                    [
                        'nama_lengkap' => $namaSiswa,
                        'jenis_kelamin' => $jkSiswa,
                        'tempat_lahir' => $faker->city,
                        'tanggal_lahir' => $faker->dateTimeBetween('-12 years', '-6 years')->format('Y-m-d'),
                        'nama_orang_tua' => $faker->randomElement(['Bapak ', 'Ibu ']) . $faker->firstName . ' ' . $faker->randomElement($safeLastNames),
                        'email_orang_tua' => null,
                        'nomor_hp_orang_tua' => $faker->phoneNumber,
                        'alamat' => $faker->address,
                        'status' => 'aktif',
                    ]
                );

                NilaiKehadiran::firstOrCreate(['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $taLama->id], ['kelas_id' => $kelas->id, 'total_poin' => 0]);
                NilaiKehadiran::firstOrCreate(['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $taBaru->id], ['kelas_id' => $kelas->id, 'total_poin' => 0]);
                $nisCounter++;
            }
        }

        // 6. GENERATE AGENDA (DIBAGI 2 TAHUN AJARAN)
        $agendas = [];
        $semuaPengajarIds = Pengajar::pluck('id')->toArray();

        // A. Agenda Semester Lalu
        for ($minggu = 50; $minggu >= 30; $minggu--) {
            $agenda = Agenda::firstOrCreate(
                [
                    'tanggal' => Carbon::now()->subWeeks($minggu)->toDateString(),
                    'nama_kegiatan' => 'Kegiatan Semester Ganjil Ke-' . (51 - $minggu)
                ], 
                [
                    'tahun_ajaran_id' => $taLama->id,
                    'waktu_mulai' => '08:00:00',
                    'waktu_selesai' => '10:00:00',
                    'status' => 'selesai',
                ]
            );
            if (!empty($semuaPengajarIds)) $agenda->penanggungJawab()->syncWithoutDetaching($faker->randomElements($semuaPengajarIds, rand(1, 2)));
            $agendas[] = $agenda;
        }

        // B. Agenda Semester Ini
        for ($minggu = 20; $minggu >= 1; $minggu--) {
            $agenda = Agenda::firstOrCreate(
                [
                    'tanggal' => Carbon::now()->subWeeks($minggu)->toDateString(),
                    'nama_kegiatan' => 'Puja Bakti Semester Genap Ke-' . (21 - $minggu)
                ], 
                [
                    'tahun_ajaran_id' => $taBaru->id,
                    'waktu_mulai' => '08:00:00',
                    'waktu_selesai' => '10:00:00',
                    'status' => 'selesai',
                ]
            );
            if (!empty($semuaPengajarIds)) $agenda->penanggungJawab()->syncWithoutDetaching($faker->randomElements($semuaPengajarIds, rand(1, 2)));
            $agendas[] = $agenda;
        }

        // 7. GENERATE ABSENSI
        $semuaSiswa = Siswa::all();
        $semuaPengajar = Pengajar::all();
        $statusSiswa = ['hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'];

        foreach ($agendas as $agenda) {
            foreach ($semuaSiswa as $siswa) {
                // Di semester lama rajin masuk (80%), di semester baru diacak biasa
                $status = ($agenda->tahun_ajaran_id == $taLama->id) ? (rand(1,10) <= 8 ? 'hadir' : 'alpa') : $faker->randomElement($statusSiswa);
                $waktuHadir = $status === 'hadir' ? '07:' . str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT) . ':00' : null;
                
                Absensi::firstOrCreate(
                    ['agenda_id' => $agenda->id, 'siswa_id' => $siswa->id],
                    ['status_kehadiran' => $status, 'waktu_hadir' => $waktuHadir, 'metode_absen' => $status === 'hadir' ? 'barcode' : 'manual']
                );
            }
        }

        // 8. FINALISASI PERHITUNGAN POIN MUTLAK
        // Menghitung ulang poin berdasarkan jumlah "Hadir" yang terikat pada Tahun Ajaran masing-masing
        foreach(NilaiKehadiran::all() as $nk) {
            $totalHadirSemesterIni = Absensi::where('siswa_id', $nk->siswa_id)
                ->where('status_kehadiran', 'hadir')
                ->whereHas('agenda', function($q) use ($nk) {
                    $q->where('tahun_ajaran_id', $nk->tahun_ajaran_id);
                })->count();
            
            // Asumsi 1 Hadir = 10 Poin
            $nk->update(['total_poin' => $totalHadirSemesterIni * 10]);
        }
    }
}