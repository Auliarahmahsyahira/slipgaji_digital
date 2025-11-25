<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghasilan extends Model
{
    protected $table = 'detailpenghasilan';
    protected $primaryKey = 'id_penghasilan';
    protected $fillable = ['id_gaji', 'id_komponen', 'nominal'];

    public function gaji()
    {
        return $this->belongsTo(Gaji::class, 'id_gaji', 'id_gaji');
    }
    //

    public function master()
    {
        return $this->belongsTo(Master::class, 'id_komponen', 'id_komponen');
    }
}
