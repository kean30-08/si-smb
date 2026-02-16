<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('pengajars', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke User (Untuk Login)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        // Biodata Pengajar
        $table->string('nama_lengkap');
        $table->string('nip')->nullable(); 
        $table->string('nomor_hp')->nullable(); // Tetap butuh untuk admin menghubungi guru
        $table->enum('jenis_kelamin', ['L', 'P']);
        $table->text('alamat')->nullable();
        
        // Jabatan Sesuai Struktur Organisasi (Gambar 2.3)
        // Default: Guru Kelas. Bisa diubah jadi 'Kepala Sekolah Minggu' dll.
        $table->string('jabatan')->default('Guru Kelas'); 

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajars');
    }
};
