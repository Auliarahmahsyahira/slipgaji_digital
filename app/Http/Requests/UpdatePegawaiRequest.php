<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePegawaiRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'nama'          => 'required|string|max:100',
      'kdsatker'      => 'required|string|max:50',
      'jabatan'       => 'required|string|max:100',
      'golongan'      => 'required|string|max:20',
      'nama_golongan' => 'required|string|max:150',
      'no_rekening'   => 'required|string|max:50',
      'password'      => 'nullable|confirmed|min:6',
    ];
  }
}
