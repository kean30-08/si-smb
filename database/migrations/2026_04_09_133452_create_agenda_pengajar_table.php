<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat Tabel Perantara (Pivot)
        Schema::create('agenda_pengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas')->cascadeOnDelete();
            $table->foreignId('pengajar_id')->constrained('pengajars')->cascadeOnDelete();
            $table->timestamps();
        });

        // 2. Hapus kolom PIC lama dari tabel Agendas
        Schema::table('agendas', function (Blueprint $table) {
            // Drop foreign key (pastikan nama ini sesuai dengan struktur Anda)
            $table->dropForeign(['penanggung_jawab_id']);
            $table->dropColumn('penanggung_jawab_id');
        });
    }

    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->foreignId('penanggung_jawab_id')->nullable()->constrained('pengajars')->nullOnDelete();
        });
        Schema::dropIfExists('agenda_pengajar');
    }
};