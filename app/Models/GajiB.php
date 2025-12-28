<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajiB extends Model
{
    protected $table = 'gaji_bulanan';
    protected $primaryKey = 'id';
    protected $fillable = ['nip_pegawai', 'periode', 'id_gaji', 'total_potongan', 'gaji_diterima',];

    // Relasi ke pegawai
    public function pegawai() {
        return $this->belongsTo(Pegawai::class, 'nip_pegawai', 'nip_pegawai');
    }

    public function gaji() {
        return $this->belongsTo(Gaji::class, 'id_gaji', 'id_gaji');
    }

    public function potonganB() {
        return $this->hasMany(PotonganB::class, 'id_gaji_bulanan', 'id');
    }


}
