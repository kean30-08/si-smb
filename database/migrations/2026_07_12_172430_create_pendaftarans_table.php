<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan kolom nama panggilan di tabel siswas
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nama_panggilan')->after('nama_lengkap')->nullable();
        });

        // 2. Buat tabel penampungan pendaftaran baru
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->string('nis')->nullable(); // Opsional untuk pendaftar
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('asal_sekolah')->nullable();
            $table->string('nomor_hp_siswa')->nullable();
            $table->string('nama_orang_tua');
            $table->string('email_orang_tua')->nullable();
            $table->string('nomor_hp_orang_tua');
            $table->text('alamat');
            $table->enum('status', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('nama_panggilan');
        });
    }
};