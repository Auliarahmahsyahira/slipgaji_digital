<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Gaji;
use App\Models\GajiB;
use App\Models\Master;
use App\Models\Penghasilan;
use App\Models\Potongan;
use App\Models\PotonganB;
use App\Models\Pegawai;
use App\Imports\GajiImport;
use App\Imports\GajiImportII;
use App\Exports\TemplateGajiExport;
use App\Exports\TemplateGajiWajibExport;
use Maatwebsite\Excel\Facades\Excel;

class GajiController extends Controller
{
    // manampilkan halaman gaji indeh
public function index(Request $request)
{
    $search = $request->input('search');

    // GAJI WAJIB
    $gajiWajib = Gaji::with(['pegawai', 'penghasilan', 'potongan'])
        ->when($search, function ($query, $search) {
            $query->where('nip_pegawai', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->get();

    // GAJI BULANAN
    $gajiBulanan = GajiB::with(['pegawai', 'gaji', 'potonganB'])
        ->when($search, function ($query, $search) {
            $query->where('nip_pegawai', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin.gaji.index', compact('gajiWajib', 'gajiBulanan', 'search'));
}


    // menampilkan halaman create
    public function createDuaTahun() {
      $komponen = Master::all();
      return view('admin.gaji.create_duatahun', compact('komponen'));
    }

    public function createBulanan(Request $request) {
      $komponen = Master::all();

    $gajiWajib = null;

    if ($request->has('nip')) {
        $gajiWajib = DB::table('gaji_wajib')
            ->where('nip_pegawai', $request->nip)
            ->orderBy('periode', 'desc') // ambil paling terbaru
            ->first();
    }

    return view('admin.gaji.create_bulanan', compact('komponen', 'gajiWajib'));
    }   

    public function cekGajiTerbaru($nip)
    {
      $data = Gaji::where('nip_pegawai', $nip)
          ->orderBy('periode', 'desc')
          ->first();

      if (!$data) {
          return response()->json([
              'success' => false,
              'jumlah_bersih' => null,
              'message' => 'Data slip gaji 2 tahun tidak ditemukan'
          ]);
      }

      return response()->json([
          'success' => true,
          'jumlah_bersih' => $data->jumlah_bersih
      ]);
    }


    // pengecekan nip
    public function cekNip($nip)
    {
        $pegawai = Pegawai::where('nip_pegawai', $nip)->first();

        if ($pegawai) {
            return response()->json([
                'success' => true,
                'nama' => $pegawai->nama,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'NIP tidak ditemukan',
            ]);
        }
    }

    // memproses create bulanan
public function store_bulanan(Request $request)
{
    /**
     * 1. NORMALISASI INPUT NOMINAL
     */
    $inputNominal = $request->nominal ?? [];

    foreach ($inputNominal as $key => $value) {
        // hilangkan format 1.500.000
        $value = str_replace(['.', ','], '', $value);

        if ($value === '' || !is_numeric($value)) {
            $value = 0;
        }

        $inputNominal[$key] = (float) $value;
    }

    $request->merge(['nominal' => $inputNominal]);


    /**
     * 2. VALIDASI
     */
    $request->validate([
        'nip_pegawai' => 'required',
        'periode'     => 'required',
        'nominal'     => 'nullable|array',
        'nominal.*'   => 'numeric'
    ]);

    $nip     = $request->nip_pegawai;
    $periode = $request->periode;


    /**
     * 3. AMBIL GAJI WAJIB TERAKHIR
     */
    $slip = Gaji::where('nip_pegawai', $nip)
                ->orderBy('periode', 'desc')
                ->first();

    if (!$slip) {
        return back()->with('error', 'Data gaji wajib tidak ditemukan!');
    }


    /**
     * 4. HITUNG GAJI BULANAN
     */
    $gajiBersih     = $slip->jumlah_bersih;
    $potongan       = $request->nominal;
    $totalPotongan  = array_sum($potongan);
    $gajiDiterima   = $gajiBersih - $totalPotongan;


    /**
     * 5. SIMPAN GAJI BULANAN
     */
    $gajiBulanan = GajiB::create([
        'nip_pegawai'    => $nip,
        'periode'        => $periode,
        'id_gaji'        => $slip->id_gaji,
        'total_potongan' => $totalPotongan,
        'gaji_diterima'  => $gajiDiterima
    ]);


    /**
     * 6. SIMPAN DETAIL POTONGAN BULANAN
     */
    foreach ($potongan as $idKomponen => $nilai) {
        if ($nilai > 0) {
            PotonganB::create([
                'id_gaji_bulanan'   => $gajiBulanan->id, // FK ke gaji_bulanan
                'id_komponen'       => $idKomponen,
                'nominal'           => $nilai
            ]);
        }
    }


    return redirect()
        ->route('slipgaji.index')
        ->with('success', 'Gaji bulanan berhasil disimpan!');
}


    public function store_duatahun(Request $request)
{
    $inputNominal = $request->nominal ?? [];

    foreach ($inputNominal as $key => $value) {
        // hilangkan format angka Indonesia seperti: 1.500.000 menjadi 1500000
        $value = str_replace(['.', ','], '', $value);

        // kalau bukan angka, set ke 0
        if ($value === '' || !is_numeric($value)) {
            $value = 0;
        }

        $inputNominal[$key] = (float) $value;
    }

    // masukkan kembali ke request
    $request->merge(['nominal' => $inputNominal]);

        $request->validate([
            'nip_pegawai' => 'required',
            'periode' => 'required|string|max:50',
            'gaji_pokok' => 'required|numeric',
            'nominal' => 'nullable|array',
            'nominal.*' => 'nullable|numeric'
        ]);

        $pegawai = Pegawai::where('nip_pegawai', $request->nip_pegawai)->first();
        if (!$pegawai) {
            return back()->with('error', 'NIP pegawai tidak ditemukan!');
        }

        $gajiPokok = (float) $request->gaji_pokok;
        $nominal = $request->nominal ?? [];

        $jumlahKotor = $gajiPokok;
        $jumlahPotongan = 0;

        $penghasilanData = [];
        $potonganData = [];

        foreach (Master::all() as $item) {

            $nilai = isset($nominal[$item->id_komponen])
                ? (float) $nominal[$item->id_komponen]
                : 0;

            if ($item->tipe == 'penghasilan') {

                $jumlahKotor += $nilai;

                $penghasilanData[] = [
                    'id_gaji'     => null,
                    'id_komponen' => $item->id_komponen,
                    'nominal'     => $nilai
                ];
            }

            if ($item->tipe == 'potongan') {

                $jumlahPotongan += $nilai;

                $potonganData[] = [
                    'id_gaji'     => null,
                    'id_komponen' => $item->id_komponen,
                    'nominal'     => $nilai
                ];
            }
        }

        $jumlahBersih = $jumlahKotor - $jumlahPotongan;

        $gaji = Gaji::create([
            'nip_pegawai'     => $request->nip_pegawai,
            'periode'         => $request->periode,
            'gaji_pokok'      => $gajiPokok,
            'jumlah_kotor'    => $jumlahKotor,
            'jumlah_potongan' => $jumlahPotongan,
            'jumlah_bersih'   => $jumlahBersih
        ]);

        foreach ($penghasilanData as &$p) $p['id_gaji'] = $gaji->id_gaji;
        foreach ($potonganData as &$p) $p['id_gaji'] = $gaji->id_gaji;

        if (!empty($penghasilanData)) {
            Penghasilan::insert($penghasilanData);
        }

        if (!empty($potonganData)) {
            Potongan::insert($potonganData);
        }

        return redirect()
            ->route('slipgaji.index')
            ->with('success', 'Slip gaji dua tahun berhasil disimpan!');
      }

    // menampilkan halaman edit
    public function edit($id)
    {
      $gaji = Gaji::with(['pegawai', 'penghasilan', 'potongan'])->findOrFail($id);
      $komponen = Master::all();
      return view('admin.gaji.edit', compact('gaji', 'komponen'));
    }

    public function edit_bulanan($id)
    {
      $gaji = GajiB::with(['pegawai','gaji', 'potonganB'])->findOrFail($id);
      $komponen = Master::all();
      return view('admin.gaji.edit_bulanan', compact('gaji', 'komponen'));
    }

    // proses update
    public function update(Request $request, $id)
    {
      $gaji = Gaji::findOrFail($id);
      $nominal = $request->nominal ?? [];

      $jumlahKotor = 0;
      $jumlahPotongan = 0;
      $gajiPokok = (float) ($request->gaji_pokok ?? 0);

      $penghasilanData = [];
      $potonganData = [];

      foreach (Master::all() as $item) {
          $nilai = isset($nominal[$item->id_komponen]) ? (float)$nominal[$item->id_komponen] : 0;

          if ($item->tipe === 'penghasilan') {
              $jumlahKotor += $nilai;
              $penghasilanData[] = [
                  'id_gaji' => $id,
                  'id_komponen' => $item->id_komponen,
                  'nominal' => $nilai,
              ];
          }

          if ($item->tipe === 'potongan' && $item->kategori === 'wajib') {
              $jumlahPotongan += $nilai;
              $potonganData[] = [
                  'id_gaji' => $id,
                  'id_komponen' => $item->id_komponen,
                  'nominal' => $nilai,
              ];
          }

      }

      $jumlahKotor += $gajiPokok;
      $jumlahBersih = $jumlahKotor - $jumlahPotongan;

      DB::table('detailpenghasilan')->where('id_gaji', $id)->delete();
      DB::table('detailpotongan')->where('id_gaji', $id)->delete();

      if ($penghasilanData) Penghasilan::insert($penghasilanData);
      if ($potonganData) Potongan::insert($potonganData);

      $gaji->update([
          'periode'         => $request->periode,
          'gaji_pokok'      => $gajiPokok,
          'jumlah_kotor'    => $jumlahKotor,
          'jumlah_potongan' => $jumlahPotongan,
          'jumlah_bersih'   => $jumlahBersih,
      ]);

      return redirect()->route('slipgaji.index')->with('success', 'Data slip gaji berhasil diperbarui!');
    }


    // proses update 2
    public function update2(Request $request, $id)
{
    $gaji = GajiB::findOrFail($id);
    $nominal = $request->nominal ?? [];

    DB::transaction(function () use ($id, $nominal, $gaji) {

        $totalPotongan = 0;
        $potonganData = [];

        $komponenPotongan = Master::where('tipe', 'potongan')
            ->where('kategori', 'lainnya')
            ->get();

        foreach ($komponenPotongan as $item) {
            $nilai = isset($nominal[$item->id_komponen])
                ? (float) $nominal[$item->id_komponen]
                : 0;

            $totalPotongan += $nilai;

            $potonganData[] = [
                'id_gaji_bulanan' => $gaji->id,
                'id_komponen' => $item->id_komponen,
                'nominal' => $nilai,
            ];
        }

        // hapus potongan lama (khusus kategori lainnya)
        DB::table('potonganbulanan')
            ->where('id_gaji_bulanan', $gaji->id)
            ->whereIn('id_komponen', $komponenPotongan->pluck('id_komponen'))
            ->delete();

        // insert potongan baru
        if (!empty($potonganData)) {
            PotonganB::insert($potonganData);
        }

        // update gaji bulanan
        $gaji->update([
            'total_potongan' => $totalPotongan,
            'gaji_diterima' => $gaji->gaji->jumlah_bersih - $totalPotongan,
        ]);
    });

    return redirect()
        ->route('slipgaji.index')
        ->with('success', 'Potongan bulanan berhasil diperbarui!');
}


    // memilih data yang mau di hapus
    public function destroy($id)
{
    // hapus detail potongan dulu
    DB::table('potonganbulanan')
        ->where('id_gaji_bulanan', $id)
        ->delete();

    // hapus gaji bulanan
    GajiB::where('id', $id)->delete();

    return redirect()
        ->back()
        ->with('success', 'Data gaji bulanan berhasil dihapus!');
}

    // menampilkan halaman import file
    public function showImportForm()
    {
          return view('admin.gaji.import');
    }

    // import file /2 tahun
    public function importTetap(Request $request)
    {
        $request->validate(['file'=>'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new GajiImportII, $request->file('file'));
            return back()->with('success','Import 2-tahunan sukses.');
        } catch (\Exception $e) {
            return back()->with('error','Import gagal: '.$e->getMessage());
        }
    }

    // import file per-bulan
    public function importBulanan(Request $request)
    {
        $request->validate(['file'=>'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new GajiImport, $request->file('file'));
            return back()->with('success','Import bulanan sukses.');
        } catch (\Exception $e) {
            return back()->with('error','Import gagal: '.$e->getMessage());
        }
    }
}



  


