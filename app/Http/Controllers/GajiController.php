<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Gaji;
use App\Models\Master;
use App\Models\Penghasilan;
use App\Models\Potongan;
use App\Models\Pegawai;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $gaji = Gaji::with('pegawai') 
        ->when($search, function ($query, $search) {
            return $query->where('nip_pegawai', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return view('admin.gaji.index', compact('gaji', 'search'));
    }

    public function create()
    {
        $komponen = Master::all();
        return view('admin.gaji.create', compact('komponen'));
    }

    public function store(Request $request)
{
  $nominal = $request->nominal ?? [];

  $jumlahKotor = 0;
  $jumlahPotongan = 0;
  $totalPotongan = 0;
  $gajiPokok = (float) ($request->gaji_pokok ?? 0); // langsung dari input form

  // ✅ Cek NIP
  $pegawai = Pegawai::where('nip_pegawai', $request->nip_pegawai)->first();
  if (!$pegawai) {
    return redirect()->back()->with('error', 'NIP pegawai tidak ditemukan!');
  }

  // ✅ Simpan data awal ke tabel gaji (sementara total 0 dulu)
  $gaji = Gaji::create([
    'periode'         => $request->periode,
    'nip_pegawai'     => $request->nip_pegawai,
    'nama'            => $pegawai->nama,
    'jumlah_kotor'    => 0,
    'jumlah_potongan' => 0,
    'jumlah_bersih'   => 0,
    'total_potongan'  => 0,
    'gaji_diterima'   => 0,
    'gaji_pokok'      => $gajiPokok, // disimpan langsung
  ]);

  $penghasilanData = [];
  $potonganData = [];

  // ✅ Loop semua komponen dari master
  foreach (Master::all() as $item) {
    $nilai = isset($nominal[$item->id_komponen]) && $nominal[$item->id_komponen] !== ''
      ? (float) $nominal[$item->id_komponen]
      : 0;

    if ($item->tipe === 'penghasilan') {
      $jumlahKotor += $nilai;
      $penghasilanData[] = [
        'id_gaji' => $gaji->id_gaji,
        'id_komponen' => $item->id_komponen,
        'nominal' => $nilai,
      ];
    }

    if ($item->tipe === 'potongan' && $item->kategori === 'wajib') {
      $jumlahPotongan += $nilai;
      $potonganData[] = [
        'id_gaji' => $gaji->id_gaji,
        'id_komponen' => $item->id_komponen,
        'nominal' => $nilai,
      ];
    }

    if ($item->tipe === 'potongan' && $item->kategori === 'lainnya') {
      $totalPotongan += $nilai;
      $potonganData[] = [
        'id_gaji' => $gaji->id_gaji,
        'id_komponen' => $item->id_komponen,
        'nominal' => $nilai,
      ];
    }
  }

  // ✅ Tambahkan gaji pokok ke total kotor
  $jumlahKotor += $gajiPokok;

  // ✅ Simpan detail ke tabel masing-masing
  if (!empty($penghasilanData)) Penghasilan::insert($penghasilanData);
  if (!empty($potonganData)) Potongan::insert($potonganData);

  // ✅ Hitung total akhir dan update tabel gaji utama
  $jumlahBersih = $jumlahKotor - $jumlahPotongan;
  $gajiDiterima = $jumlahBersih - $totalPotongan;

  $gaji->update([
    'jumlah_kotor'    => $jumlahKotor,
    'jumlah_potongan' => $jumlahPotongan,
    'total_potongan'  => $totalPotongan,
    'jumlah_bersih'   => $jumlahBersih,
    'gaji_diterima'   => $gajiDiterima,
  ]);

  return redirect()->route('slipgaji.index')->with('success', 'Data slip gaji berhasil disimpan!');
}


    // ✅ 6. Endpoint AJAX untuk cek NIP
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

   public function deleteSelected(Request $request)
{
  $ids = $request->ids ?? [];

  if (empty($ids)) {
    return redirect()->back()->with('error', 'Pilih dulu data yang mau dihapus!');
  }

  foreach ($ids as $id) {
    $gaji = Gaji::find($id);

    if ($gaji) {
      // Hapus semua data relasi berdasarkan id_gaji
      DB::table('detailpenghasilan')->where('id_gaji', $gaji->id_gaji)->delete();
      DB::table('detailpotongan')->where('id_gaji', $gaji->id_gaji)->delete();

      // Baru hapus data di tabel gaji
      $gaji->delete();
    }
  }

  return redirect()->back()->with('success', 'Data gaji dan detail terkait berhasil dihapus!');
}

  public function update(Request $request, $id)
{
    $gaji = Gaji::findOrFail($id);
    $nominal = $request->nominal ?? [];

    $jumlahKotor = 0;
    $jumlahPotongan = 0;
    $totalPotongan = 0;
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

        if ($item->tipe === 'potongan' && $item->kategori === 'lainnya') {
            $totalPotongan += $nilai;
            $potonganData[] = [
                'id_gaji' => $id,
                'id_komponen' => $item->id_komponen,
                'nominal' => $nilai,
            ];
        }
    }

    $jumlahKotor += $gajiPokok;
    $jumlahBersih = $jumlahKotor - $jumlahPotongan;
    $gajiDiterima = $jumlahBersih - $totalPotongan;

    // Hapus data lama
    DB::table('detailpenghasilan')->where('id_gaji', $id)->delete();
    DB::table('detailpotongan')->where('id_gaji', $id)->delete();

    // Simpan data baru
    if ($penghasilanData) Penghasilan::insert($penghasilanData);
    if ($potonganData) Potongan::insert($potonganData);

    // Update tabel utama
    $gaji->update([
        'nip_pegawai'     => $request->nip_pegawai,
        'periode'         => $request->periode,
        'gaji_pokok'      => $gajiPokok,
        'jumlah_kotor'    => $jumlahKotor,
        'jumlah_potongan' => $jumlahPotongan,
        'jumlah_bersih'   => $jumlahBersih,
        'total_potongan'  => $totalPotongan,
        'gaji_diterima'   => $gajiDiterima,
    ]);

    return redirect()->route('slipgaji.index')->with('success', 'Data slip gaji berhasil diperbarui!');
}

  public function editSelected(Request $request)
{
    $ids = $request->input('ids', []);

    // Validasi: harus pilih satu data saja
    if (empty($ids)) {
        return redirect()->back()->with('error', 'Pilih satu data yang ingin di-update!');
    }

    if (count($ids) > 1) {
        return redirect()->back()->with('error', 'Hanya boleh pilih satu data untuk di-update!');
    }

    // Ambil ID pertama (karena cuma satu yang boleh)
    $id = $ids[0];

    // Redirect langsung ke halaman edit yang sudah ada
    return redirect()->route('slipgaji.edit', ['id' => $id]);
}

public function edit($id)
{
    $gaji = Gaji::with(['pegawai', 'penghasilan', 'potongan'])->findOrFail($id);
    $komponen = Master::all();
    return view('admin.gaji.edit', compact('gaji', 'komponen'));
}
}

  


