<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agenda;
use App\Models\Absensi;
use App\Models\HistoriSiswa;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Ambil semua jadwal kegiatan yang statusnya sudah lewat atau hari ini
        // (Kita tidak mengisi absensi untuk jadwal di masa depan)
        $agendas = Agenda::whereIn('status', ['selesai', 'sedang berlangsung'])->get();

        foreach ($agendas as $agenda) {
            
            // 2. Cari siapa saja siswa yang terdaftar (punya histori) di Tahun Ajaran jadwal ini
            $historiSiswas = HistoriSiswa::where('tahun_ajaran_id', $agenda->tahun_ajaran_id)->get();

            foreach ($historiSiswas as $histori) {
                
                // 3. Buat Probabilitas Acak agar data terlihat realistis
                // Misal: 75% Hadir, 10% Sakit, 10% Izin, 5% Alpa
                $chance = rand(1, 100);
                
                if ($chance <= 75) {
                    $status = 'hadir';
                } elseif ($chance <= 85) {
                    $status = 'sakit';
                } elseif ($chance <= 95) {
                    $status = 'izin';
                } else {
                    $status = 'alpa';
                }

                // 4. Buat Waktu Masuk Acak khusus yang statusnya "Hadir"
                // Jam normal sekolah minggu adalah 08:00, kita buat acak antara 07:45 sampai 08:15
                $waktu_hadir = null;
                if ($status === 'hadir') {
                    $jam = rand(0, 1) ? '07' : '08';
                    $menit = ($jam === '07') ? rand(45, 59) : rand(0, 15);
                    $detik = rand(0, 59);
                    $waktu_hadir = sprintf("%02d:%02d:%02d", $jam, $menit, $detik);
                }

                // 5. Simpan ke Database
                Absensi::updateOrCreate(
                    [
                        'agenda_id' => $agenda->id,
                        'siswa_id' => $histori->siswa_id,
                    ],
                    [
                        'status_kehadiran' => $status,
                        'waktu_hadir' => $waktu_hadir,
                        'metode_absen' => 'manual', // Sesuai enum di database Anda
                    ]
                );
            }
        }

        $this->command->info('Berhasil! Data absensi acak untuk seluruh jadwal yang sudah berlalu telah berhasil di-generate.');
    }
}