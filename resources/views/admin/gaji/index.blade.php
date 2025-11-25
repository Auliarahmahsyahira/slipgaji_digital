@extends('layouts.admin')

@section('content')

<div class="text-center mb-4">
  <h3 class="fw-bold text-dark text-shadow-sm">
    Daftar Slip Gaji Pegawai Kanwil Kemenag Prov. Kepri
  </h3>
</div>

<div class="position-relative mb-4" style="min-height: 100px;">
  <a href="{{ route('slipgaji.create') }}" class="btn btn-dark position-absolute" style="top: 0; left: 60px; background-color: #006316;">Create</a>
  <form action="{{ route('gaji.import') }}" method="POST" enctype="multipart/form-data" class="position-absolute" style="top: 45px; left: 210px; background-color: #006316;">
  @csrf
  <input type="file" name="file" accept=".xlsx,.xls" required onchange="this.form.submit()" id="importFile" style="display:none;">
  <label for="importFile" class="btn btn-dark" style="background-color:#006316; cursor:pointer;">Import</label>
</form>

  <form id="form-selected" action="{{ route('slipgaji.deleteSelected') }}" method="POST" class="position-absolute" style="top: 45px; left: 110px;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-dark" style="background-color: #006316;" onclick="return confirm('Yakin mau hapus data terpilih?')">Delete</button>
  </form>

  <form id="form-edit" action="{{ route('slipgaji.editSelected') }}" method="GET" class="position-absolute" style="top: 0; left: 160px;">
  @csrf
  <button type="submit" class="btn btn-dark" style="background-color: #006316;">Update</button>
</form>
</div>

<div class="d-flex justify-content-end mb-2">
  <form method="GET" action="{{ route('slipgaji.index') }}" class="input-group w-auto">
    <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="height: 30px;">
      <i class="bi bi-search"></i>
    </span>
    <input 
      type="text" 
      name="search" 
      class="form-control border-start-0" style="width: 150px; height: 30px;"
      placeholder="Cari NIP..."
      value="{{ request('search') }}"
    >
  </form>
</div>

<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
  <table class="table table-bordered bg-white text-center align-middle">
    <thead class="table-light" style="font-family: 'Times New Roman', Times, serif;">
      <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>No</th>
        <th>Periode</th>
        <th>Nama</th>
        <th>NIP</th>
        <th>Gaji Pokok</th>
        <th>Jumlah Kotor</th>
        <th>Jumlah Potongan</th>
        <th>Gaji Bersih</th>
        <th>Total Potongan</th>
        <th>Gaji Diterima</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($gaji as $index => $item)
        <tr>
          <td><input type="checkbox" name="ids[]" form="form-selected" value="{{ $item->id_gaji }}"></td>
          <td>{{ $index + 1 }}</td>
          <td>{{ \Carbon\Carbon::parse($item->periode)->translatedFormat('F Y') }}</td>
          <td>{{ $item->pegawai->nama ?? '-' }}</td>
          <td>{{ $item->nip_pegawai ?? '-' }}</td>
          <td>{{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
          <td>{{ number_format($item->jumlah_kotor, 0, ',', '.') }}</td>
          <td>{{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
          <td>{{ number_format($item->jumlah_bersih, 0, ',', '.') }}</td>
          <td>{{ number_format($item->total_potongan, 0, ',', '.') }}</td>
          <td>{{ number_format($item->gaji_diterima, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="12">Data belum tersedia</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<script>
  document.getElementById('select-all').addEventListener('change', function(e) {
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = e.target.checked);
  });

  document.getElementById('form-selected').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('input[name="ids[]"]:checked');
    if (checked.length === 0) {
      e.preventDefault();
      alert('Pilih data yang mau dihapus dulu!');
    }
  });

  document.getElementById('form-edit').addEventListener('submit', function(e) {
  const checked = document.querySelectorAll('input[name="ids[]"]:checked');
  if (checked.length === 0) {
    e.preventDefault();
    alert('Pilih satu data yang ingin di-update!');
  } else if (checked.length > 1) {
    e.preventDefault();
    alert('Hanya boleh pilih satu data untuk di-update!');
  } else {
    const id = checked[0].value;
    this.action = '/slipgaji/edit/' + id;
    this.submit();
  }
});
</script>

<style>
  .text-shadow-sm { text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.3); }
  .btn-dark { color: white; text-decoration: none; }
  .btn-dark:hover { color: #ffc107; }
  .form-control::placeholder {
    font-size: 14px;
    font-family: 'Times New Roman', Times, serif;
    font-style: italic;
    color: #888;
  }
  th {
    white-space: nowrap;
    text-align: center;
    font-weight: bold;
    background-color: #f0f0f0;
    border: 2px solid #ddd;
    padding: 12px 15px;
    font-size: 14px;
  }
  tr:hover { background-color: #f4f4f9; cursor: pointer; transition: background-color 0.3s ease; }
  tr:nth-child(even) { background-color: #fafafa; }
  td {
    border: 2px solid #ddd;
    padding: 12px 15px;
    font-size: 14px;
  }
  .table-responsive {
    box-shadow: 0 4px 8px 0 rgb(0 0 0 / .2);
    border-radius: 8px;
    border: 2px solid #ddd;
  }
  td:hover { background-color: #f9f9f9; transition: background-color 0.2s ease-in-out; }
</style>

@endsection
