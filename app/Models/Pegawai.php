<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;    

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'nip_pegawai';
    /**  @var bool */
    protected $fillable = ['nip_pegawai', 'nama', 'kdsatker', 'jabatan', 'no_rekening', 'golongan', 'nama_golongan'];

    // Relasi ke User
    public function user() {
        return $this->hasOne(User::class, 'nip_pegawai', 'nip_pegawai');
    }

    // Relasi ke Gaji
    public function gaji() {
        return $this->hasMany(Gaji::class, 'nip_pegawai', 'nip_pegawai');
    }

    public function gajib() {
        return $this->hasMany(GajiB::class, 'nip_pegawai', 'nip_pegawai');
    }
}
