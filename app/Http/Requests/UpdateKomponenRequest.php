<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKomponenRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'nama_komponen' => 'required|string|max:100',
      'tipe'          => 'required|in:penghasilan,potongan',
      'kategori'      => 'required|in:wajib,lainnya',
      'periode'       => 'required|in:24,1',
    ];
  }
}
