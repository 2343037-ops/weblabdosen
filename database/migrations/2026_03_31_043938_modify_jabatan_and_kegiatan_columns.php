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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE dosen MODIFY jabatan VARCHAR(100) NULL DEFAULT NULL");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE jadwal_mingguan MODIFY kegiatan VARCHAR(100) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE dosen MODIFY jabatan ENUM('Kepala Lab', 'Staf Lab') NOT NULL DEFAULT 'Staf Lab'");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE jadwal_mingguan MODIFY kegiatan ENUM('Mengajar', 'Bimbingan', 'Rapat', 'Istirahat', 'Luar Kampus') NOT NULL");
    }
};
