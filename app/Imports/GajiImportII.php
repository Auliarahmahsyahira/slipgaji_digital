<?php

namespace App\Imports;

use App\Models\Pegawai;
use App\Models\Gaji;
use App\Models\Master;
use App\Models\Penghasilan;
use App\Models\Potongan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class GajiImportII implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {

            $master = Master::all(); // semua komponen

            foreach ($rows as $row) {

                if (!$row['nip_pegawai']) continue;

                $pegawai = Pegawai::where('nip_pegawai', $row['nip_pegawai'])->first();
                if (!$pegawai) continue;

                $periode = date('Y-m');

                // Buat data gaji (2 tahunan)
                $gaji = Gaji::updateOrCreate(
                    ['nip_pegawai' => $pegawai->nip_pegawai, 'periode' => $periode],
                    [
                        'nama_pegawai' => $pegawai->nama_pegawai,
                        'gaji_pokok'   => $row['gaji_pokok'] ?? 0,
                    ]
                );

                // hapus detail sebelumnya
                Penghasilan::where('id_gaji', $gaji->id)->delete();
                Potongan::where('id_gaji', $gaji->id)->delete();

                $totalPenghasilan = 0;
                $totalPotongan = 0;

                foreach ($master as $komp) {

                    $col = strtolower(str_replace([' ', '-', '.', '/'], '_', $komp->nama_komponen));
                    $nominal = $row[$col] ?? 0;

                    if ($komp->tipe == 'penghasilan') {
                        Penghasilan::create([
                            'id_gaji'     => $gaji->id,
                            'id_komponen' => $komp->id_komponen,
                            'nominal'     => $nominal
                        ]);
                        $totalPenghasilan += $nominal;
                    }

                    if ($komp->tipe == 'potongan') {
                        Potongan::create([
                            'id_gaji'     => $gaji->id,
                            'id_komponen' => $komp->id_komponen,
                            'nominal'     => $nominal
                        ]);
                        $totalPotongan += $nominal;
                    }
                }

                // Hitungan sesuai tabel kamu
                $jumlahKotor     = $gaji->gaji_pokok + $totalPenghasilan;
                $jumlahPotongan  = $totalPotongan;
                $jumlahBersih    = $jumlahKotor - $jumlahPotongan;

                $gaji->update([
                    'jumlah_kotor'     => $jumlahKotor,
                    'jumlah_potongan'  => $jumlahPotongan,
                    'jumlah_bersih'    => $jumlahBersih,
                    'total_potongan'   => $jumlahPotongan,
                    'gaji_diterima'    => $jumlahBersih
                ]);
            }

        });
    }
}
