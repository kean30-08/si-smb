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
    Schema::create('laporan_insentifs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pengajar_id')->constrained('pengajars')->onDelete('cascade');
        $table->string('bulan', 2); // Contoh: '01', '12'
        $table->year('tahun'); // Contoh: 2026
        $table->string('file_path')->nullable(); // Menyimpan lokasi PDF statis
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_insentifs');
    }
};
