<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agenda;
use Carbon\Carbon;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tetapkan tanggal mulai dan selesai
        $startDate = Carbon::parse('2025-07-06');
        $endDate = Carbon::parse('2026-07-12');

        // Copy tanggal mulai untuk di-looping
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $year = $currentDate->year;
            $month = $currentDate->month;

            // Logika Penentuan ID Tahun Ajaran otomatis berdasarkan bulan & tahun
            $taId = 1; // Default
            
            if ($year == 2025) {
                // Juli s/d Desember 2025 masuk ke 2025/2026 Ganjil (ID: 1)
                $taId = 1;
            } elseif ($year == 2026 && $month <= 6) {
                // Januari s/d Juni 2026 masuk ke 2025/2026 Genap (ID: 4)
                $taId = 4;
            } elseif ($year == 2026 && $month >= 7) {
                // Mulai Juli 2026 masuk ke 2026/2027 Ganjil (ID: 5)
                $taId = 5;
            }

            // Gunakan updateOrCreate agar tidak duplikat jika dijalankan berulang kali
            Agenda::updateOrCreate(
                ['tanggal' => $currentDate->format('Y-m-d')],
                [
                    'tahun_ajaran_id' => $taId,
                    'nama_kegiatan' => 'Kegiatan Sekolah Minggu',
                    'waktu_mulai' => '08:00:00',
                    'waktu_selesai' => '12:00:00',
                    'status' => 'selesai', // Diset selesai karena jadwalnya adalah masa lalu
                    'is_public' => 1,
                    // is_libur tidak kita set di sini, agar tidak menimpa status libur (seperti tgl 6 Juli) yang mungkin sudah ada di database Anda
                ]
            );

            // Tambahkan 1 minggu (7 hari) untuk melompat ke hari Minggu berikutnya
            $currentDate->addWeek();
        }

        $this->command->info('Jadwal Sekolah Minggu dari 6 Juli 2025 s/d 12 Juli 2026 berhasil di-generate!');
    }
}