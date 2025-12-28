<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GajiImport;
use App\Models\Gaji;
use App\Models\Master;
use App\Models\Pegawai;
use App\Models\Penghasilan;
use App\Models\Potongan;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{

    public function import_duatahun(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    $spreadsheet = IOFactory::load($request->file('file'));
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $header = array_map('trim', $rows[0]);
    unset($rows[0]);

    DB::beginTransaction();

    try {

        foreach ($rows as $row) {

            $data = array_combine($header, $row);

            // NORMALISASI NOMINAL
            foreach ($data as $k => $v) {
                if (is_string($v)) {
                    $v = str_replace(['.', ','], '', $v);
                }
                $data[$k] = is_numeric($v) ? (float)$v : $v;
            }

            $pegawai = Pegawai::where('nip_pegawai', $data['nip_pegawai'])->first();
            if (!$pegawai) continue;

            $jumlahKotor = $data['gaji_pokok'];
            $jumlahPotongan = 0;

            $penghasilan = [];
            $potongan = [];

            foreach (Master::all() as $m) {
                $nilai = $data[$m->id_komponen] ?? 0;

                if ($m->tipe === 'penghasilan') {
                    $jumlahKotor += $nilai;
                    $penghasilan[] = [
                        'id_komponen' => $m->id_komponen,
                        'nominal'     => $nilai
                    ];
                }

                if ($m->tipe === 'potongan') {
                    $jumlahPotongan += $nilai;
                    $potongan[] = [
                        'id_komponen' => $m->id_komponen,
                        'nominal'     => $nilai
                    ];
                }
            }

            $gaji = Gaji::create([
                'nip_pegawai'     => $data['nip_pegawai'],
                'periode'         => $data['periode'],
                'gaji_pokok'      => $data['gaji_pokok'],
                'jumlah_kotor'    => $jumlahKotor,
                'jumlah_potongan' => $jumlahPotongan,
                'jumlah_bersih'   => $jumlahKotor - $jumlahPotongan
            ]);

            foreach ($penghasilan as &$p) $p['id_gaji'] = $gaji->id_gaji;
            foreach ($potongan as &$p)    $p['id_gaji'] = $gaji->id_gaji;

            Penghasilan::insert($penghasilan);
            Potongan::insert($potongan);
        }

        DB::commit();

        return back()->with('success', 'Import slip gaji dua tahun berhasil!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal import: '.$e->getMessage());
    }
}

    public function importBulanan(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(
                new GajiImport,
                $request->file('file')
            );

            return redirect()
                ->route('slipgaji.index')
                ->with('success', 'Import slip gaji bulanan berhasil');
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
