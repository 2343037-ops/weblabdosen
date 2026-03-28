<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalAkanDatang extends Model
{
    protected $table = 'jadwal_akan_datang';
    protected $fillable = ['dosen_id', 'judul', 'tanggal_mulai', 'tanggal_selesai', 'is_fullday', 'jam_mulai', 'jam_selesai', 'keterangan'];
    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date', 'is_fullday' => 'boolean'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}
