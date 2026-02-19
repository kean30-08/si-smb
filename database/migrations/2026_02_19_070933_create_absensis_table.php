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
    Schema::create('absensis', function (Blueprint $table) {
        $table->id();
        
        // Relasi (Absen ini untuk acara apa, dan punya siapa)
        $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
        $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
        
        // Data Kehadiran
        $table->time('waktu_hadir')->nullable(); // Jam berapa dia scan barcode
        $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpa'])->default('alpa');
        
        // Untuk menandai apakah dia absen lewat scan barcode atau dicentang manual oleh guru
        $table->enum('metode_absen', ['barcode', 'manual'])->default('manual');
        
        $table->text('keterangan')->nullable(); // Jika izin/sakit, tulis alasannya di sini

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
