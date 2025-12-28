<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGajiDuaTahunRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
{
  return [
    'nip_pegawai' => 'required',
    'periode'     => 'required|string|max:50',
    'gaji_pokok'  => 'required|numeric',
    'nominal'     => 'nullable|array',
    'nominal.*'   => 'nullable|numeric',
  ];
}
}
