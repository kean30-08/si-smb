<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pengajar;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Agenda;
use App\Models\Absensi;
use App\Models\TahunAjaran;
use App\Models\NilaiKehadiran;
use App\Models\AbsensiPengajar;
use App\Models\Jabatan;
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

        // 1. GENERATE TAHUN AJARAN SIMULASI (Untuk keperluan histori absensi dummy)
        // Gunakan updateOrCreate agar menimpa status 'aktif' dari DatabaseSeeder menjadi 'tidak aktif'
        $taLama = TahunAjaran::updateOrCreate(
            ['tahun_ajaran' => '2025/2026 Ganjil'],
            ['status' => 'tidak aktif'] 
        );
        
        $taBaru = TahunAjaran::updateOrCreate(
            ['tahun_ajaran' => '2025/2026 Genap'],
            ['status' => 'aktif']
        );

        // 2. AMBIL ID JABATAN GURU YANG SUDAH ADA DARI DATABASE SEEDER
        $jabatanGuru = Jabatan::where('nama_jabatan', 'Guru Sekolah Minggu')->first()->id ?? 1;

        // 3. GENERATE AKUN & DATA PENGAJAR DUMMY
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
                    'jabatan_id' => $jabatanGuru, 
                    'nama_lengkap' => $namaPengajar,
                    'nomor_hp' => $faker->phoneNumber,
                    'jenis_kelamin' => $jk,
                    'alamat' => $faker->address,
                    'status' => 'aktif', 
                ]
            );
        }

        // 4. GENERATE DATA SISWA DUMMY BERDASARKAN KELAS YANG SUDAH ADA
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

        // 5. GENERATE AGENDA DUMMY
        $agendas = [];
        $semuaPengajarIds = Pengajar::pluck('id')->toArray();

        // A. Agenda Semester Ganjil (Histori)
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

        // B. Agenda Semester Genap (Sekarang)
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

        // 6. GENERATE ABSENSI DUMMY SISWA
        $semuaSiswa = Siswa::all();
        $statusSiswa = ['hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'];

        foreach ($agendas as $agenda) {
            foreach ($semuaSiswa as $siswa) {
                $status = ($agenda->tahun_ajaran_id == $taLama->id) ? (rand(1,10) <= 8 ? 'hadir' : 'alpa') : $faker->randomElement($statusSiswa);
                $waktuHadir = $status === 'hadir' ? '07:' . str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT) . ':00' : null;
                
                Absensi::firstOrCreate(
                    ['agenda_id' => $agenda->id, 'siswa_id' => $siswa->id],
                    ['status_kehadiran' => $status, 'waktu_hadir' => $waktuHadir, 'metode_absen' => $status === 'hadir' ? 'barcode' : 'manual']
                );
            }
        }

        // 6.5 GENERATE ABSENSI DUMMY PENGAJAR
        $semuaPengajar = Pengajar::all();
        // Probabilitas hadir dibuat lebih tinggi karena pengajar biasanya lebih rajin
        $statusPengajar = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa']; 

        foreach ($agendas as $agenda) {
            foreach ($semuaPengajar as $pengajar) {
                // Di semester lama (histori) rajin masuk 90%, di semester baru diacak biasa
                $status = ($agenda->tahun_ajaran_id == $taLama->id) ? (rand(1,10) <= 9 ? 'hadir' : 'alpa') : $faker->randomElement($statusPengajar);
                
                // Waktu hadir pengajar dibuat lebih awal (07:15 - 07:45) dibanding siswa
                $waktuHadir = $status === 'hadir' ? '07:' . str_pad(rand(15, 45), 2, '0', STR_PAD_LEFT) . ':00' : null;
                
                AbsensiPengajar::firstOrCreate(
                    ['agenda_id' => $agenda->id, 'pengajar_id' => $pengajar->id],
                    [
                        'status_kehadiran' => $status, 
                        'waktu_hadir' => $waktuHadir
                    ]
                );
            }
        }

        // 7. FINALISASI REKAPITULASI POIN KEHADIRAN DUMMY
        // 7. FINALISASI REKAPITULASI POIN KEHADIRAN DUMMY
        foreach(NilaiKehadiran::all() as $nk) {
            // Hitung Hadir
            $totalHadir = Absensi::where('siswa_id', $nk->siswa_id)
                ->where('status_kehadiran', 'hadir')
                ->whereHas('agenda', function($q) use ($nk) {
                    $q->where('tahun_ajaran_id', $nk->tahun_ajaran_id);
                })->count();
            
            // Hitung Izin
            $totalIzin = Absensi::where('siswa_id', $nk->siswa_id)
                ->where('status_kehadiran', 'izin')
                ->whereHas('agenda', function($q) use ($nk) {
                    $q->where('tahun_ajaran_id', $nk->tahun_ajaran_id);
                })->count();

            // Hitung Sakit
            $totalSakit = Absensi::where('siswa_id', $nk->siswa_id)
                ->where('status_kehadiran', 'sakit')
                ->whereHas('agenda', function($q) use ($nk) {
                    $q->where('tahun_ajaran_id', $nk->tahun_ajaran_id);
                })->count();

            // Update Poin (Hadir*5, Izin*1, Sakit*1)
            $nk->update([
                'total_poin' => ($totalHadir * 5) + ($totalIzin * 1) + ($totalSakit * 1)
            ]);
        }
    }
}