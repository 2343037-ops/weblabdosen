<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_mingguan', function (Blueprint $table) {
            $table->id();
            $table->string('dosen_id');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('kegiatan', ['Mengajar', 'Bimbingan', 'Rapat', 'Istirahat', 'Luar Kampus']);
            $table->string('mata_kuliah')->nullable();
            $table->string('ruangan')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_mingguan');
    }
};
