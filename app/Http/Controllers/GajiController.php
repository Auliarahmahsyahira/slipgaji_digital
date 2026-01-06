<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreGajiBulananRequest;
use App\Http\Requests\StoreGajiDuaTahunRequest;
use App\Http\Requests\UpdateGajiRequest;
use App\Http\Requests\UpdateGajiBulananRequest;
use App\Http\Requests\ImportGajiDuaTahunRequest;
use App\Http\Requests\ImportGajiBulananRequest;
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
    ->when($search, function ($query) use ($search) {
        $query->whereHas('pegawai', function ($q) use ($search) {
            $q->where('nama', 'LIKE', "%{$search}%")
              ->orWhere('nip_pegawai', 'LIKE', "%{$search}%");
        });
    })
    ->orderBy('created_at', 'desc')
    ->get();

    // GAJI BULANAN
    $gajiBulanan = GajiB::with(['pegawai', 'gaji', 'potonganB'])
    ->when($search, function ($query) use ($search) {
        $query->whereHas('pegawai', function ($q) use ($search) {
            $q->where('nama', 'LIKE', "%{$search}%")
              ->orWhere('nip_pegawai', 'LIKE', "%{$search}%");
        });
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
public function store_bulanan(StoreGajiBulananRequest $request)
{
  $nominal = $request->nominal ?? [];

  foreach ($nominal as $k => $v) {
    $v = str_replace(['.', ','], '', $v);
    $nominal[$k] = is_numeric($v) ? (float)$v : 0;
  }

  $request->merge(['nominal' => $nominal]);

  $slip = Gaji::where('nip_pegawai', $request->nip_pegawai)
    ->orderBy('periode', 'desc')
    ->first();

  if (!$slip) {
    return back()->with('error', 'Data gaji wajib tidak ditemukan!');
  }

  $totalPotongan = array_sum($nominal);
  $gajiDiterima  = $slip->jumlah_bersih - $totalPotongan;

  $gajiBulanan = GajiB::create([
    'nip_pegawai'    => $request->nip_pegawai,
    'periode'        => $request->periode,
    'id_gaji'        => $slip->id_gaji,
    'total_potongan' => $totalPotongan,
    'gaji_diterima'  => $gajiDiterima,
  ]);

  foreach ($nominal as $id => $nilai) {
    if ($nilai > 0) {
      PotonganB::create([
        'id_gaji_bulanan' => $gajiBulanan->id,
        'id_komponen'     => $id,
        'nominal'         => $nilai,
      ]);
    }
  }

  return redirect()
    ->route('slipgaji.index')
    ->with('success', 'Gaji bulanan berhasil disimpan!');
}


    public function store_duatahun(StoreGajiDuaTahunRequest $request)
{
  // 1. Normalisasi nominal
  $nominal = $request->nominal ?? [];

  foreach ($nominal as $key => $value) {
    $value = str_replace(['.', ','], '', $value);
    $nominal[$key] = is_numeric($value) ? (float)$value : 0;
  }

  // 2. Hitung
  $gajiPokok       = (float) $request->gaji_pokok;
  $jumlahKotor     = $gajiPokok;
  $jumlahPotongan  = 0;
  $penghasilanData = [];
  $potonganData    = [];

  foreach (Master::all() as $item) {
    $nilai = $nominal[$item->id_komponen] ?? 0;

    if ($item->tipe === 'penghasilan') {
      $jumlahKotor += $nilai;
      $penghasilanData[] = [
        'id_gaji'     => null,
        'id_komponen' => $item->id_komponen,
        'nominal'     => $nilai,
      ];
    }

    if ($item->tipe === 'potongan') {
      $jumlahPotongan += $nilai;
      $potonganData[] = [
        'id_gaji'     => null,
        'id_komponen' => $item->id_komponen,
        'nominal'     => $nilai,
      ];
    }
  }

  $jumlahBersih = $jumlahKotor - $jumlahPotongan;

  // 3. Simpan gaji utama
  $gaji = Gaji::create([
    'nip_pegawai'     => $request->nip_pegawai,
    'periode'         => $request->periode,
    'gaji_pokok'      => $gajiPokok,
    'jumlah_kotor'    => $jumlahKotor,
    'jumlah_potongan' => $jumlahPotongan,
    'jumlah_bersih'   => $jumlahBersih,
  ]);

  // 4. Simpan detail
  foreach ($penghasilanData as &$p) {
    $p['id_gaji'] = $gaji->id_gaji;
  }

  foreach ($potonganData as &$p) {
    $p['id_gaji'] = $gaji->id_gaji;
  }

  if ($penghasilanData) Penghasilan::insert($penghasilanData);
  if ($potonganData) Potongan::insert($potonganData);

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
    public function update(UpdateGajiRequest $request, $id)
{
  $gaji = Gaji::findOrFail($id);

  // 1. Normalisasi nominal
  $nominal = $request->nominal ?? [];

  foreach ($nominal as $key => $value) {
    $value = str_replace(['.', ','], '', $value);
    $nominal[$key] = is_numeric($value) ? (float)$value : 0;
  }

  // 2. Hitung ulang
  $gajiPokok       = (float) $request->gaji_pokok;
  $jumlahKotor     = $gajiPokok;
  $jumlahPotongan  = 0;
  $penghasilanData = [];
  $potonganData    = [];

  foreach (Master::all() as $item) {
    $nilai = $nominal[$item->id_komponen] ?? 0;

    if ($item->tipe === 'penghasilan') {
      $jumlahKotor += $nilai;
      $penghasilanData[] = [
        'id_gaji'     => $id,
        'id_komponen' => $item->id_komponen,
        'nominal'     => $nilai,
      ];
    }

    if ($item->tipe === 'potongan' && $item->kategori === 'wajib') {
      $jumlahPotongan += $nilai;
      $potonganData[] = [
        'id_gaji'     => $id,
        'id_komponen' => $item->id_komponen,
        'nominal'     => $nilai,
      ];
    }
  }

  $jumlahBersih = $jumlahKotor - $jumlahPotongan;

  // 3. Hapus detail lama
  DB::table('detailpenghasilan')->where('id_gaji', $id)->delete();
  DB::table('detailpotongan')->where('id_gaji', $id)->delete();

  // 4. Simpan detail baru
  if ($penghasilanData) Penghasilan::insert($penghasilanData);
  if ($potonganData) Potongan::insert($potonganData);

  // 5. Update gaji utama
  $gaji->update([
    'periode'         => $request->periode,
    'gaji_pokok'      => $gajiPokok,
    'jumlah_kotor'    => $jumlahKotor,
    'jumlah_potongan' => $jumlahPotongan,
    'jumlah_bersih'   => $jumlahBersih,
  ]);

  return redirect()
    ->route('slipgaji.index')
    ->with('success', 'Data slip gaji berhasil diperbarui!');
}


    // proses update 2
    public function update2(UpdateGajiBulananRequest $request, $id)
{
  $gaji = GajiB::findOrFail($id);
  $nominal = $request->nominal ?? [];

  DB::transaction(function () use ($gaji, $nominal) {

    $totalPotongan = 0;
    $potonganData = [];

    $komponenPotongan = Master::where('tipe', 'potongan')
      ->where('kategori', 'lainnya')
      ->get();

    foreach ($komponenPotongan as $item) {
      $nilai = $nominal[$item->id_komponen] ?? 0;
      $totalPotongan += $nilai;

      $potonganData[] = [
        'id_gaji_bulanan' => $gaji->id,
        'id_komponen'     => $item->id_komponen,
        'nominal'         => $nilai,
      ];
    }

    DB::table('potonganbulanan')
      ->where('id_gaji_bulanan', $gaji->id)
      ->whereIn('id_komponen', $komponenPotongan->pluck('id_komponen'))
      ->delete();

    if ($potonganData) {
      PotonganB::insert($potonganData);
    }

    $gaji->update([
      'total_potongan' => $totalPotongan,
      'gaji_diterima'  => $gaji->gaji->jumlah_bersih - $totalPotongan,
    ]);
  });

  return redirect()
    ->route('slipgaji.index')
    ->with('success', 'Potongan bulanan berhasil diperbarui!');
}


    //menghapus data gaji bulanan
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
}