<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalMingguan extends Model
{
    protected $table = 'jadwal_mingguan';
    protected $fillable = ['dosen_id', 'hari', 'jam_mulai', 'jam_selesai', 'kegiatan', 'mata_kuliah', 'ruangan', 'keterangan'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}
