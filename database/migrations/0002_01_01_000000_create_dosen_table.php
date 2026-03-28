<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama');
            $table->string('nidn')->unique();
            $table->enum('jabatan', ['Kepala Lab', 'Staf Lab'])->default('Staf Lab');
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->enum('status', ['Di Ruangan', 'Tidak Di Ruangan'])->default('Di Ruangan');
            $table->enum('status_mode', ['otomatis', 'manual'])->default('otomatis');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
