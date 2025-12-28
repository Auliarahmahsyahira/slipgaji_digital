<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'nip_pegawai'   => 'required|unique:pegawai,nip_pegawai',
      'nama'          => 'required|string|max:100',
      'kdsatker'      => 'required|string|max:50',
      'jabatan'       => 'required|string|max:100',
      'golongan'      => 'required|string|max:50',
      'nama_golongan' => 'required|string|max:100',
      'no_rekening'   => 'required|string|max:50',
      'password'      => 'required|confirmed|min:6',
    ];
  }
}
