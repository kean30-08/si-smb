<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refleksi_siswas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_siswa');
            $table->string('nis');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('nama_orang_tua');
            $table->string('email_orang_tua')->nullable();
            $table->text('rangkuman');
            $table->text('bagian_disukai');
            $table->text('bagian_kurang_disukai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refleksi_siswas');
    }
};