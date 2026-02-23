<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pengajar;
use App\Models\Agenda;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LaporanDummySeeder extends Seeder
{
    public function run()
    {
        // 1. Buat Data Kelas Dummy (Hanya nama_kelas, sesuai struktur DB Anda)
        $kelasPaud = Kelas::create(['nama_kelas' => 'Kelas PAUD & TK Dummy']);
        $kelasSD = Kelas::create(['nama_kelas' => 'Kelas SD Dummy']);
        $kelasSMP = Kelas::create(['nama_kelas' => 'Kelas SMP Dummy']);

        // 2. Buat Data Pengajar Dummy (Harus buat User-nya dulu karena ada relasi user_id)
        $pengajarsData = [
            ['nama' => 'Romo Pandita Suryadi', 'jk' => 'L', 'hp' => '081234567890'],
            ['nama' => 'Upacarika Metta', 'jk' => 'P', 'hp' => '081298765432'],
            ['nama' => 'Bapak Karuna', 'jk' => 'L', 'hp' => '081211223344'],
        ];

        foreach ($pengajarsData as $index => $p) {
            $user = User::create([
                'name' => $p['nama'],
                'email' => 'pengajar' . $index . '@vihara.com',
                'password' => Hash::make('password123'),
            ]);

            Pengajar::create([
                'user_id' => $user->id,
                'nama_lengkap' => $p['nama'],
                'nip' => 'NIP' . rand(100000, 999999),
                'nomor_hp' => $p['hp'],
                'jenis_kelamin' => $p['jk'], // Sesuai ENUM('L', 'P')
                'alamat' => 'Jl. Dharma No ' . ($index + 1) . ', Tabanan',
                'jabatan' => 'Guru Kelas'
            ]);
        }

        // 3. Buat Data Siswa Dummy 
        $namaSiswa = [
            ['Budi Santoso', 'L'], ['Ananda Vimala', 'P'], ['Candra Wijaya', 'L'], 
            ['Dharma Putra', 'L'], ['Eka Maitri', 'P'], ['Fajar Karuna', 'L'], 
            ['Gita Mudita', 'P'], ['Hadi Upekkha', 'L'], ['Indra Pratama', 'L'], 
            ['Jaya Kusuma', 'L'], ['Kiranavati', 'P'], ['Lina Suryani', 'P'],
            ['Siddhartha', 'L'], ['Nanda', 'L'], ['Rahula', 'L']
        ];

        foreach ($namaSiswa as $index => $data) {
            // Membagi rata siswa ke 3 kelas
            if ($index < 5) {
                $kelas_id = $kelasPaud->id;
            } elseif ($index < 10) {
                $kelas_id = $kelasSD->id;
            } else {
                $kelas_id = $kelasSMP->id;
            }

            Siswa::create([
                'kelas_id' => $kelas_id,
                'nama_lengkap' => $data[0], // Sesuai struktur DB
                'nis' => 'NIS2026' . str_pad($index + 1, 3, '0', STR_PAD_LEFT), // Buat NIS otomatis (Wajib ada)
                'jenis_kelamin' => $data[1], // 'L' atau 'P'
                'tempat_lahir' => 'Tabanan',
                'tanggal_lahir' => Carbon::now()->subYears(rand(5, 15))->format('Y-m-d'),
                'nama_orang_tua' => 'Ortu ' . $data[0],
                'email_orang_tua' => 'ortu' . $index . '@contoh.com',
                'nomor_hp_orang_tua' => '085' . rand(10000000, 99999999),
                'alamat' => 'Alamat Rumah No ' . rand(1, 100) . ' Tabanan',
                'status' => 'aktif',
                'total_poin' => 0
            ]);
        }

        // 4. Buat Data Agenda Dummy (Jadwal Lampau)
        for ($i = 1; $i <= 5; $i++) {
            $tanggal = Carbon::now()->subWeeks($i)->format('Y-m-d'); 
            
            Agenda::create([
                'tanggal' => $tanggal,
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '09:00:00',
                'nama_kegiatan' => 'Puja Bakti Minggu Ke-' . (5 - $i),
                'deskripsi_rundown' => 'Kegiatan rutin pembacaan paritta',
                'status' => 'selesai'
            ]);

            Agenda::create([
                'tanggal' => $tanggal,
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '10:30:00',
                'nama_kegiatan' => 'Belajar Dhamma Kelas',
                'deskripsi_rundown' => 'Pembelajaran sesuai tingkatan kelas masing-masing',
                'status' => 'selesai'
            ]);
        }
    }
}