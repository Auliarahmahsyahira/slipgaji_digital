<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Gaji;
use App\Models\Master;
use App\Models\Penghasilan;
use App\Models\Potongan;
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

        $gaji = Gaji::with('pegawai') 
        ->when($search, function ($query, $search) {
            return $query->where('nip_pegawai', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return view('admin.gaji.index', compact('gaji', 'search'));
    }

    // menampilkan halaman create
    public function create()
    {
        $komponen = Master::all();
        return view('admin.gaji.create', compact('komponen'));
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

    // memproses create
    public function store(Request $request)
    {
      $nominal = $request->nominal ?? [];

      $jumlahKotor = 0;
      $jumlahPotongan = 0;
      $totalPotongan = 0;
      $gajiPokok = (float) ($request->gaji_pokok ?? 0);

      // Cek NIP
      $pegawai = Pegawai::where('nip_pegawai', $request->nip_pegawai)->first();
      if (!$pegawai) {
        return redirect()->back()->with('error', 'NIP pegawai tidak ditemukan!');
      }

      // Simpan data awal ke tabel gaji 
      $gaji = Gaji::create([
        'periode'         => $request->periode,
        'nip_pegawai'     => $request->nip_pegawai,
        'nama'            => $pegawai->nama,
        'jumlah_kotor'    => 0,
        'jumlah_potongan' => 0,
        'jumlah_bersih'   => 0,
        'total_potongan'  => 0,
        'gaji_diterima'   => 0,
        'gaji_pokok'      => $gajiPokok, 
      ]);

      $penghasilanData = [];
      $potonganData = [];

      //  Loop semua komponen dari master
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

      // Tambahkan gaji pokok ke total kotor
      $jumlahKotor += $gajiPokok;

      //  Simpan detail ke tabel masing-masing
      if (!empty($penghasilanData)) Penghasilan::insert($penghasilanData);
      if (!empty($potonganData)) Potongan::insert($potonganData);

      //  Hitung total akhir dan update tabel gaji utama
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

    // menampilkan halaman edit
    public function edit($id)
    {
      $gaji = Gaji::with(['pegawai', 'penghasilan', 'potongan'])->findOrFail($id);
      $komponen = Master::all();
      return view('admin.gaji.edit', compact('gaji', 'komponen'));
    }

    public function edit2($id)
    {
      $gaji = Gaji::with(['pegawai', 'penghasilan', 'potongan'])->findOrFail($id);
      $komponen = Master::all();
      return view('admin.gaji.edit2', compact('gaji', 'komponen'));
    }

    // memilih data yg mau di edit
    public function editSelected(Request $request)
    {
      $ids = $request->input('ids', []);

      if (empty($ids)) {
          return redirect()->back()->with('error', 'Pilih satu data yang ingin di-update!');
      }

      if (count($ids) > 1) {
          return redirect()->back()->with('error', 'Hanya boleh pilih satu data untuk di-update!');
      }

      $id = $ids[0];

      // cek mode: edit1 atau edit2
      $mode = $request->query('mode', 'edit1');

      if ($mode === 'edit2') {
          return redirect()->route('slipgaji.edit2', ['id' => $id]);
      }

      return redirect()->route('slipgaji.edit', ['id' => $id]);
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
          'nip_pegawai'     => $request->nip_pegawai,
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
      $gaji = Gaji::findOrFail($id);
      $nominal = $request->nominal ?? [];

      $totalPotongan = 0;
      $potonganData = [];

      foreach (Master::where('tipe', 'potongan')->where('kategori', 'lainnya')->get() as $item) {
          $nilai = isset($nominal[$item->id_komponen]) ? (float)$nominal[$item->id_komponen] : 0;

          $totalPotongan += $nilai;

          $potonganData[] = [
              'id_gaji' => $id,
              'id_komponen' => $item->id_komponen,
              'nominal' => $nilai,
          ];
      }

      // hapus potongan lainnya lama
      DB::table('detailpotongan')->where('id_gaji', $id)->whereIn(
          'id_komponen',
          Master::where('tipe','potongan')->where('kategori','lainnya')->pluck('id_komponen')
      )->delete();

      // simpan yang baru
      if ($potonganData) Potongan::insert($potonganData);

      $gajiDiterima = $gaji->jumlah_bersih - $totalPotongan;

      $gaji->update([
          'total_potongan' => $totalPotongan,
          'gaji_diterima' => $gajiDiterima,
      ]);

      return redirect()->route('slipgaji.index')->with('success', 'Potongan bulanan berhasil diperbarui!');
    }

    // memilih data yang mau di hapus
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



  


