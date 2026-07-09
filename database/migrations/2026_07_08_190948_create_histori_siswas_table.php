<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histori_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
            $table->timestamps();
            
            // Kita tidak perlu menyimpan "total_poin" di sini, 
            // karena poin, H, I, S, A akan dihitung otomatis secara real-time!
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histori_siswas');
    }
};