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
    Schema::create('materis', function (Blueprint $table) {
        $table->id();
        
        // Relasi (Materi ini untuk kelas apa, dan siapa guru yang upload)
        $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
        $table->foreignId('pengajar_id')->nullable()->constrained('pengajars')->onDelete('set null');

        // Detail Materi
        $table->string('judul_materi');
        $table->text('deskripsi')->nullable();
        
        // Menyimpan nama file/path PDF silabus di folder server
        $table->string('file_path')->nullable(); 

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};
