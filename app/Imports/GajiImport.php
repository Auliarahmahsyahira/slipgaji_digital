<?php

namespace App\Imports;

use App\Models\Pegawai;
use App\Models\Gaji;
use App\Models\Potongan;
use App\Models\Master;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GajiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
{   
    // langsung ambil dari header excel
    $nama = $row['nama'] ?? null;

    if (!$nama) return null;

    $pegawai = Pegawai::where('nama', $nama)->first();
    if (!$pegawai) return null;

    $periode = $row['periode'] ?? date('Y-m');

    $gaji = Gaji::where('nama', $nama)
                ->where('periode', $periode)
                ->first();
    if (!$gaji) return null;

    // cek jumlah bersih (nama kolomnya apa di excel?)
    $jumlahBersihExcel = $row['gaji_diterima'] ?? 0;

    if ($gaji->jumlah_bersih != $jumlahBersihExcel) {
        throw new \Exception("Jumlah bersih tidak sesuai untuk Nama pegawai: $nama");
    }

    // hitung potongan lainnya
    $totalPotonganLain = 0;

    $komponenLain = Master::where('kategori', 'lainnya')->get();

    foreach ($komponenLain as $komp) {
        // header excel kamu sudah pakai underscore, jadi normalisasinya cocok
        $col = strtolower($komp->nama_komponen);

        $nominal = $row[$col] ?? 0;
        $totalPotonganLain += $nominal;

        Potongan::updateOrCreate(
            [
                'id_gaji' => $gaji->id,
                'id_komponen' => $komp->id_komponen
            ],
            [
                'nominal' => $nominal
            ]
        );
    }

    // update total
    $gaji->update([
        'total_potongan' => $totalPotonganLain,
        'gaji_diterima' => $gaji->jumlah_bersih - $totalPotonganLain,
    ]);

    return $gaji;
}

}
