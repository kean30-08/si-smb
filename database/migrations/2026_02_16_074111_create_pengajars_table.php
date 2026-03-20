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
        Schema::create('pengajars', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User (Untuk Login)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Relasi ke tabel Jabatans
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->onDelete('set null');
            
            // Biodata Pengajar
            $table->string('nama_lengkap');
            $table->string('nomor_hp')->nullable(); 
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajars');
    }
};
