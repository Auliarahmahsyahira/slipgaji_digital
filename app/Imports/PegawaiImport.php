<?php

namespace App\Imports;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PegawaiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek jika tidak ada NIP
        if (!isset($row['nip']) || $row['nip'] == null) {
            return null;
        }

        // 1. Simpan atau update data pegawai
        $pegawai = Pegawai::updateOrCreate(
            ['nip_pegawai' => $row['nip']],
            [
                'nama'          => $row['nama'] ?? null,
                'kdsatker'      => $row['kdsatker'] ?? null,
                'jabatan'       => $row['jabatan'] ?? null,
                'no_rekening'   => $row['no_rekening'] ?? null,
                'golongan'      => $row['golongan'] ?? null,
                'nama_golongan' => $row['nama_golongan'] ?? null,
            ]
        );

        // 2. Buat user otomatis jika belum ada
        User::firstOrCreate(
            ['nip_pegawai' => $row['nip']], // foreign key → pegawai
            [
                'password' => Hash::make('KANWILKEMENAGKEPRI'), // default password
                'role'     => 'pegawai',
            ]
        );

        return $pegawai;
    }
}
