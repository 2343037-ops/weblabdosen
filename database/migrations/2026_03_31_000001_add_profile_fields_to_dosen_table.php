<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('nik')->nullable()->after('nidn');
            $table->string('ruangan')->nullable()->after('telepon');
            $table->boolean('tampilkan_nidn')->default(true)->after('ruangan');
            $table->boolean('tampilkan_nik')->default(false)->after('tampilkan_nidn');
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn(['nik', 'ruangan', 'tampilkan_nidn', 'tampilkan_nik']);
        });
    }
};
