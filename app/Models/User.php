<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $fillable = ['nip_pegawai', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    // Relasi ke tabel Pegawai
    public function Pegawai() {
        return $this->belongsTo(Pegawai::class, 'nip_pegawai', 'nip_pegawai');
    }
}
