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
    Schema::create('agendas', function (Blueprint $table) {
        $table->id();
        // TAMBAHAN: Foreign Key Tahun Ajaran
        $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->onDelete('set null');
        $table->foreignId('penanggung_jawab_id')->nullable()->constrained('pengajars')->onDelete('set null');
        $table->string('nama_kegiatan'); // Misal: Puja Bakti Anak, Sekolah Minggu
        $table->date('tanggal');
        $table->time('waktu_mulai');
        $table->time('waktu_selesai')->nullable();
        $table->text('deskripsi_rundown')->nullable(); // Untuk menyimpan rundown kegiatan
        
        // Status untuk membedakan jadwal masa depan dan yang sudah selesai
        $table->enum('status', ['akan datang', 'sedang berlangsung', 'selesai', 'batal'])->default('akan datang');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
