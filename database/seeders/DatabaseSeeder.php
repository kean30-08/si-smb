<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jabatan; // Jangan lupa import model Jabatan
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import Hash untuk password

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 
        // SEEDER AKUN ADMIN
        // 
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            // Saya tambahkan Hash::make() agar passwordnya terenkripsi dengan benar dan bisa dipakai login
            'password' => Hash::make('admin123'), 
        ]);
    }
}