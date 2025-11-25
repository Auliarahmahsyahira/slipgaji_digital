<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    protected $table = 'gaji';
    protected $primaryKey = 'id_gaji';
    protected $fillable = ['nip_pegawai', 'periode', 'jumlah_kotor', 'jumlah_potongan', 'total_potongan', 'gaji_pokok', 'jumlah_bersih', 'gaji_diterima',];

    // Relasi ke pegawai
    public function pegawai() {
        return $this->belongsTo(Pegawai::class, 'nip_pegawai', 'nip_pegawai');
    }

    public function penghasilan() {
        return $this->hasMany(Penghasilan::class, 'id_gaji', 'id_gaji');
    }

    public function potongan() {
        return $this->hasMany(Potongan::class, 'id_gaji', 'id_gaji');
    }
}
