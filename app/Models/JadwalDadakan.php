<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalDadakan extends Model
{
    protected $table = 'jadwal_dadakan';
    protected $fillable = ['dosen_id', 'judul', 'tanggal_mulai', 'tanggal_selesai', 'is_fullday', 'jam_mulai', 'jam_selesai', 'keterangan'];
    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date', 'is_fullday' => 'boolean'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}
