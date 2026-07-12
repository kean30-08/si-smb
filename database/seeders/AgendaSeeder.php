<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agenda;
use Carbon\Carbon;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Berdasarkan ID Tahun Ajaran dari Database Anda
        $tahunAjarans = [
            1 => ['nama' => '2025/2026 Ganjil', 'start_date' => '2025-07-20'], // Dimulai pertengahan Juli 2025
            4 => ['nama' => '2025/2026 Genap',  'start_date' => '2026-01-18'], // Dimulai pertengahan Januari 2026
            5 => ['nama' => '2026/2027 Ganjil', 'start_date' => '2026-07-19'], // Dimulai pertengahan Juli 2026
            6 => ['nama' => '2026/2027 Genap',  'start_date' => '2027-01-17'], // Dimulai pertengahan Januari 2027
        ];

        foreach ($tahunAjarans as $ta_id => $ta) {
            // Gunakan Carbon untuk memanipulasi tanggal
            $tanggal = Carbon::parse($ta['start_date']);

            // Buat 16 kali pertemuan (16 Pekan/Minggu) untuk setiap Semester
            for ($i = 1; $i <= 16; $i++) {
                
                // Logika Status: Menyesuaikan dengan waktu saat ini (Real-time)
                if ($tanggal->isPast()) {
                    $status = 'selesai';
                } elseif ($tanggal->isToday()) {
                    $status = 'sedang berlangsung';
                } else {
                    $status = 'akan datang';
                }

                // Gunakan updateOrCreate agar tidak terjadi duplikat jika seeder dijalankan berulang kali
                Agenda::updateOrCreate(
                    [
                        'tahun_ajaran_id' => $ta_id,
                        'nama_kegiatan' => "Sekolah Minggu Pekan ke-{$i} ({$ta['nama']})",
                    ],
                    [
                        'tanggal' => $tanggal->format('Y-m-d'),
                        'waktu_mulai' => '08:00:00',
                        'waktu_selesai' => '11:00:00',
                        'status' => $status,
                    ]
                );

                // Tambahkan 7 hari untuk memajukan jadwal ke pekan berikutnya
                $tanggal->addWeek();
            }
        }

        $this->command->info('Berhasil! Jadwal Sekolah Minggu dari 2025/2026 Ganjil hingga 2026/2027 Genap telah dibuat.');
    }
}