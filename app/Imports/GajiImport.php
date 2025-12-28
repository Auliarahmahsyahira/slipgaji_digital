<?php

namespace App\Imports;

use App\Models\Pegawai;
use App\Models\Gaji;
use App\Models\GajiB;
use App\Models\PotonganB;
use App\Models\Master;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GajiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        /**
         * =================================
         * 1. AMBIL & VALIDASI PEGAWAI
         * =================================
         */
        $nama = $row['nama'] ?? null;
        if (!$nama) return null;

        $pegawai = Pegawai::where('nama', $nama)->first();
        if (!$pegawai) return null;

        /**
         * =================================
         * 2. AMBIL PERIODE
         * =================================
         */
        $periode = $row['periode'] ?? date('Y-m');

        /**
         * =================================
         * 3. CARI GAJI WAJIB
         * =================================
         */
        $gajiWajib = Gaji::where('nip_pegawai', $pegawai->nip_pegawai)
            ->where('periode', $periode)
            ->first();

        if (!$gajiWajib) return null;

        /**
         * =================================
         * 4. VALIDASI JUMLAH BERSIH
         * =================================
         */
        $jumlahBersihExcel = $row['gaji_bersih'] ?? 0;

        if ((int)$gajiWajib->jumlah_bersih !== (int)$jumlahBersihExcel) {
            throw new \Exception(
                "Jumlah bersih tidak sesuai: {$pegawai->nama} periode {$periode}"
            );
        }

        /**
         * =================================
         * 5. SIMPAN GAJI BULANAN
         * =================================
         */
        $gajiBulanan = GajiB::create([
            'nip_pegawai'    => $pegawai->nip_pegawai,
            'periode'        => $periode,
            'id_gaji'        => $gajiWajib->id_gaji,
            'total_potongan' => 0, // update di bawah
            'gaji_diterima'  => 0,
        ]);

        /**
         * =================================
         * 6. SIMPAN POTONGAN BULANAN
         * =================================
         */
        $totalPotongan = 0;

        $komponenBulanan = Master::where('kategori', 'bulanan')->get();

        foreach ($komponenBulanan as $komp) {

            // normalisasi nama kolom excel
            $col = strtolower(str_replace(' ', '_', $komp->nama_komponen));
            $nominal = $row[$col] ?? 0;

            if ($nominal > 0) {
                PotonganB::create([
                    'id_gaji_bulanan' => $gajiBulanan->id,
                    'id_komponen'     => $komp->id_komponen,
                    'nominal'         => $nominal,
                ]);

                $totalPotongan += $nominal;
            }
        }

        /**
         * =================================
         * 7. UPDATE TOTAL GAJI BULANAN
         * =================================
         */
        $gajiBulanan->update([
            'total_potongan' => $totalPotongan,
            'gaji_diterima'  => $gajiWajib->jumlah_bersih - $totalPotongan,
        ]);

        return $gajiBulanan;
    }
}
