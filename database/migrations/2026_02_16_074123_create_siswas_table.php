<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            
            // Kolom kelas_id DIHAPUS, dipindah ke nilai_kehadirans
            
            // Identitas Siswa
            $table->string('nama_lengkap');
            $table->string('nis')->unique(); 
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            
            // Data Orang Tua
            $table->string('nama_orang_tua')->nullable();
            $table->string('email_orang_tua')->nullable();
            $table->string('nomor_hp_orang_tua')->nullable();
            $table->text('alamat')->nullable();

            // Status Siswa
            $table->enum('status', ['aktif', 'tidak aktif', 'lulus'])->default('aktif');
            // Kolom total_poin DIHAPUS, dipindah ke nilai_kehadirans

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};