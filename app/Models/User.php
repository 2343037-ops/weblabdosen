<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password'];

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    /**
     * Relasi ke data profil dosen.
     * Satu akun user memiliki satu data dosen.
     */
    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id');
    }
}
