<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agenda;
use App\Models\Absensi;
use App\Models\HistoriSiswa;
use Carbon\Carbon;

class AgendaAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Tentukan Tanggal Mulai untuk Setiap Tahun Ajaran
        // Saya sudah memilihkan tanggal yang tepat jatuh pada HARI MINGGU di bulan Juli & Januari
        $tahunAjarans = [
            1 => ['nama' => '2025/2026 Ganjil', 'start_date' => '2025-07-20'], // 20 Juli 2025 (Minggu)
            4 => ['nama' => '2025/2026 Genap',  'start_date' => '2026-01-18'], // 18 Jan 2026 (Minggu)
            5 => ['nama' => '2026/2027 Ganjil', 'start_date' => '2026-07-19'], // 19 Juli 2026 (Minggu)
            6 => ['nama' => '2026/2027 Genap',  'start_date' => '2027-01-17'], // 17 Jan 2027 (Minggu)
        ];

        foreach ($tahunAjarans as $ta_id => $ta) {
            $tanggal = Carbon::parse($ta['start_date']);
            
            // Pengamanan Ekstra: Jika entah kenapa format tanggal bergeser, paksa cari hari MINGGU berikutnya
            if (!$tanggal->isSunday()) {
                $tanggal->next(Carbon::SUNDAY);
            }

            // Ambil daftar siswa yang terdaftar di Tahun Ajaran ini saja
            $historiSiswas = HistoriSiswa::where('tahun_ajaran_id', $ta_id)->get();

            // Lakukan perulangan untuk 16 Pekan (16 Kali Pertemuan Hari Minggu)
            for ($i = 1; $i <= 16; $i++) {
                
                // Tentukan Status Agenda (Berdasarkan waktu real-time komputer saat seeder dijalankan)
                if ($tanggal->isPast() && !$tanggal->isToday()) {
                    $statusAgenda = 'selesai';
                } elseif ($tanggal->isToday()) {
                    $statusAgenda = 'sedang berlangsung';
                } else {
                    $statusAgenda = 'akan datang';
                }

                // ==========================================
                // A. BUAT AGENDA KEGIATAN
                // ==========================================
                $agenda = Agenda::updateOrCreate(
                    [
                        'tahun_ajaran_id' => $ta_id,
                        'nama_kegiatan' => "Sekolah Minggu Pekan ke-{$i} ({$ta['nama']})",
                    ],
                    [
                        'tanggal' => $tanggal->format('Y-m-d'),
                        'waktu_mulai' => '08:00:00',
                        'waktu_selesai' => '11:00:00',
                        'status' => $statusAgenda,
                    ]
                );

                // ==========================================
                // B. BUAT ABSENSI SISWA (HANYA JIKA AGENDA SUDAH LEWAT/HARI INI)
                // ==========================================
                if (in_array($statusAgenda, ['selesai', 'sedang berlangsung'])) {
                    foreach ($historiSiswas as $histori) {
                        
                        // Buat Probabilitas Acak (75% Hadir, 10% Sakit, 10% Izin, 5% Alpa)
                        $chance = rand(1, 100);
                        if ($chance <= 75) {
                            $statusAbsen = 'hadir';
                        } elseif ($chance <= 85) {
                            $statusAbsen = 'sakit';
                        } elseif ($chance <= 95) {
                            $statusAbsen = 'izin';
                        } else {
                            $statusAbsen = 'alpa';
                        }

                        // Buat Jam Masuk Acak khusus yang hadir (sekitar jam 07:45 - 08:15)
                        $waktu_hadir = null;
                        if ($statusAbsen === 'hadir') {
                            $jam = rand(0, 1) ? '07' : '08';
                            $menit = ($jam === '07') ? rand(45, 59) : rand(0, 15);
                            $detik = rand(0, 59);
                            $waktu_hadir = sprintf("%02d:%02d:%02d", $jam, $menit, $detik);
                        }

                        // Simpan Data Absensi
                        Absensi::updateOrCreate(
                            [
                                'agenda_id' => $agenda->id,
                                'siswa_id' => $histori->siswa_id,
                            ],
                            [
                                'status_kehadiran' => $statusAbsen,
                                'waktu_hadir' => $waktu_hadir,
                                'metode_absen' => 'manual',
                            ]
                        );
                    }
                }

                // ==========================================
                // C. MAJUKAN TANGGAL 1 MINGGU KE DEPAN
                // ==========================================
                // addWeek() akan otomatis melompat 7 hari, jadi pasti selalu Hari Minggu
                $tanggal->addWeek();
            }
        }

        $this->command->info('Super! Seeder Gabungan (Agenda khusus hari Minggu + Absensi acak) berhasil dijalankan!');
    }
}