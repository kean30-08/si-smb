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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Kelas (Siswa milik kelas apa)
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null'); 

            // Identitas Siswa
            $table->string('nama_lengkap');
            $table->string('nis')->unique(); 
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            
            // Data Orang Tua (SESUAI REVISI PROPOSAL)
            $table->string('nama_orang_tua')->nullable();
            $table->string('email_orang_tua')->nullable();
            $table->string('nomor_hp_orang_tua')->nullable();
            $table->text('alamat')->nullable();

            // Fitur Monitoring & Gamifikasi (SESUAI NOVELTY)
            $table->enum('status', ['aktif', 'tidak aktif', 'lulus'])->default('aktif');
            $table->integer('total_poin')->default(0); // Kolom baru untuk menampung Poin Keaktifan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
