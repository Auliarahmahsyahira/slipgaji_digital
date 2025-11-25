<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Master extends Model
{
    protected $table  = 'masterkomponen';
    protected $primaryKey = 'id_komponen';
    protected $fillable = ['nama_komponen', 'tipe', 'kategori'];

    public function penghasilan()
        {
            return $this->hasMany(Penghasilan::class, 'id_komponen', 'id_komponen');
        }

        public function Potongan() 
        {
            return $this->hasMany(Potongan::class, 'id_komponen', 'id_komponen');
        }
}
