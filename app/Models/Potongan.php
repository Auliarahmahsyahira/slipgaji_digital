<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Potongan extends Model
{
    protected $table = 'detailpotongan';
    protected $primaryKey = 'id_potongan';
    protected $fillable = ['id_gaji', 'id_komponen', 'nominal'];

    //Relasi ke gaji
    public function gaji()
    {
        return $this->belongsTo(Gaji::class, 'id_gaji', 'id_gaji');
    }

    //Relasi ke Master 
    public  function master()
    {
        return $this->belongsTo(Master::class, 'id_komponen', 'id_komponen');
    }
}
