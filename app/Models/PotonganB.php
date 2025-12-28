<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotonganB extends Model
{
    protected $table = 'potonganbulanan';
    protected $primaryKey = 'id_bulanan';
    protected $fillable = ['id_gaji_bulanan', 'id_komponen', 'nominal',];

    //Relasi ke Master 
    public  function master()
    {
        return $this->belongsTo(Master::class, 'id_komponen', 'id_komponen');
    }

    public function gajib()
    {
        return $this->belongsTo(GajiB::class, 'id_gaji_bulanan', 'id');
    }
}
