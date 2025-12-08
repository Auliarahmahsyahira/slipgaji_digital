@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <h4>Import Data Gaji</h4>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

  <div class="row">
    <div class="col-md-6">
      <div class="card p-3 mb-3">
        <h5>Import 2-Tahunan (Tetap / Wajib)</h5>
        <form action="{{ route('gaji.import.tetap') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-2">
            <label>File Excel</label>
            <input type="file" name="file" class="form-control" required>
          </div>
          <button class="btn btn-primary">Import Tetap (2 tahun)</button>
        </form>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-3 mb-3">
        <h5>Import Bulanan (Potongan Lainnya)</h5>
        <form action="{{ route('gaji.import.bulanan') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-2">
            <label>Periode (YYYY-MM)</label>
            <input type="text" name="periode" class="form-control" placeholder="2025-12">
          </div>
          <div class="mb-2">
            <label>File Excel</label>
            <input type="file" name="file" class="form-control" required>
          </div>
          <button class="btn btn-primary">Import Bulanan</button>
        </form>
      </div>
    </div>
  </div>

  <div class="card p-3">
    <h5>Petunjuk singkat</h5>
    <ol>
      <li>Download template yang sesuai (full atau wajib).</li>
      <li>Isi baris untuk setiap pegawai. Pastikan header tepat sama (case sensitive disarankan). Kolom <code>periode</code> bisa disi manual atau kosong.</li>
      <li>Untuk import bulanan, pastikan nilai <code>jumlah_bersih</code> di Excel sama dengan nilai di database—jika tidak, import akan gagal untuk baris tersebut.</li>
      <li>Setelah import bulanan sukses, sistem akan menyimpan potongan kategori <code>lainnya</code> dan update <code>total_potongan</code> & <code>gaji_diterima</code>.</li>
    </ol>
  </div>
</div>
@endsection
