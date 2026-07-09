<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\HistoriSiswa;
use App\Models\Agenda;
use App\Models\Absensi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataDummy extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Setup Tahun Ajaran
            $ta1 = TahunAjaran::updateOrCreate(['tahun_ajaran' => '2024/2025 Ganjil'], ['status' => 'tidak aktif']);
            $ta2 = TahunAjaran::updateOrCreate(['tahun_ajaran' => '2024/2025 Genap'], ['status' => 'tidak aktif']);
            $ta3 = TahunAjaran::updateOrCreate(['tahun_ajaran' => '2025/2026 Ganjil'], ['status' => 'aktif']);

            $daftarTa = [$ta1, $ta2, $ta3];
            $agendaMap = [];

            // 2. Buat Minimal 5 Agenda per Tahun Ajaran
            // Kita atur tanggal mundur agar tidak berbenturan dengan waktu saat ini
            $baseDate = Carbon::now()->subMonths(18); // Mundur 1.5 tahun untuk TA 2024/2025 Ganjil

            foreach ($daftarTa as $index => $ta) {
                // Majukan tanggal 6 bulan untuk tiap semester baru
                $taDate = $baseDate->copy()->addMonths($index * 6); 
                $agendaMap[$ta->id] = [];

                for ($i = 1; $i <= 5; $i++) {
                    // Buat kegiatan yang berjarak 1 minggu tiap pertemuannya
                    $tanggalAgenda = $taDate->copy()->addWeeks($i)->format('Y-m-d');
                    
                    $agenda = Agenda::firstOrCreate(
                        [
                            'tanggal' => $tanggalAgenda,
                            'tahun_ajaran_id' => $ta->id
                        ],
                        [
                            'nama_kegiatan' => 'Sekolah Minggu Pekan ke-' . $i . ' (' . $ta->tahun_ajaran . ')',
                            'waktu_mulai' => '08:00:00',
                            'waktu_selesai' => '11:00:00',
                            'status' => 'selesai'
                        ]
                    );

                    $agendaMap[$ta->id][] = $agenda;
                }
            }

            // 3. Setup Data Dummy Siswa
            $siswaList = [
                ['nama' => 'Metta', 'nis' => '24001', 'jk' => 'P'],
                ['nama' => 'Samitta', 'nis' => '24002', 'jk' => 'P']
            ];

            // Pilihan kehadiran (Hadir diperbanyak agar probabilitasnya lebih besar)
            $pilihanKehadiran = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'];

            foreach ($siswaList as $s) {
                $siswa = Siswa::firstOrCreate(['nis' => $s['nis']], [
                    'nama_lengkap' => $s['nama'],
                    'jenis_kelamin' => $s['jk'],
                    'tempat_lahir' => 'Tabanan',
                    'tanggal_lahir' => '2016-01-01',
                    'alamat' => 'Alamat Dummy',
                    'status' => 'aktif'
                ]);

                // Setup Histori (Naik Kelas)
                // Catatan ID Kelas: 1 = PG, 2 = TK A, 4 = 1 SD, 5 = 2 SD
                $historiData = ($s['nama'] == 'Metta') 
                    ? [['ta' => $ta1, 'kls' => 4], ['ta' => $ta2, 'kls' => 4], ['ta' => $ta3, 'kls' => 5]]
                    : [['ta' => $ta1, 'kls' => 1], ['ta' => $ta2, 'kls' => 1], ['ta' => $ta3, 'kls' => 2]];

                foreach ($historiData as $h) {
                    // Masukkan ke Histori Siswa
                    HistoriSiswa::firstOrCreate([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $h['ta']->id,
                        'kelas_id' => $h['kls']
                    ]);

                    // 4. Masukkan Absensi Bervariasi Untuk Tiap Agenda di Semester Tersebut
                    $agendas = $agendaMap[$h['ta']->id];
                    
                    foreach ($agendas as $agenda) {
                        // Mengambil status secara acak dari array $pilihanKehadiran
                        $randomStatus = $pilihanKehadiran[array_rand($pilihanKehadiran)];
                        
                        Absensi::firstOrCreate(
                            [
                                'agenda_id' => $agenda->id, 
                                'siswa_id' => $siswa->id
                            ],
                            [
                                'status_kehadiran' => $randomStatus, 
                                'metode_absen' => 'manual',
                                'waktu_hadir' => ($randomStatus == 'hadir') ? '08:15:00' : null,
                                'keterangan' => ($randomStatus != 'hadir') ? 'Testing Seeder' : null
                            ]
                        );
                    }
                }
            }
        });
    }
}