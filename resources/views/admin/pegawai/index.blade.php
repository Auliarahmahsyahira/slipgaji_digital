@extends('layouts.admin')

@section('content')
  <h4 class="text-center fw-bold mb-4 text-dark">
    Daftar Pegawai Kanwil Kemenag Prov. Kepri
  </h4>

   {{-- Baris atas: Create di kiri, Search di kanan --}}
  <div class="d-flex justify-content-between align-items-center mb-3" style="font-family: 'Times New Roman', Times, serif;">
    
    {{-- Tombol Create di kiri --}}
    <div class="d-flex gap-2">
    {{-- Tombol Create di kiri --}}
    <a href="{{ route('pegawai.create') }}" class="btn btn-success px-4" style="background-color: #006316;">Create</a>

    {{-- Tombol Import --}}
    <button class="btn btn-success px-4" style="background-color: #006316;" data-bs-toggle="modal" data-bs-target="#modalImport">Import</button>
  </div>

    {{-- Pencarian di kanan --}}
    <form method="GET" action="{{ route('pegawai.index') }}" class="input-group w-auto">
      <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="height: 35px;">
        <i class="bi bi-search"></i>
      </span>
      <input 
        type="text" 
        name="search" 
        class="form-control border-start-0" style="width: 180px; height: 35px;"
        placeholder="Cari NIP..."
        value="{{ request('search') }}"
      >
    </form>
  </div>

  {{-- Tabel Pegawai --}}
<div style="overflow-x: auto; overflow-y: auto; max-height: 500px; white-space: nowrap;">
  <table class="table table-bordered table-striped align-middle text-center bg-white shadow-sm" style="min-width: 900px;">
    <thead class="table-dark">
      <tr>
        <th style="width: 110px;"></th>
        <th><input type="checkbox" id="checkAll"></th>
        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Kdsatker</th>
        <th>Jabatan</th>
        <th>No Rekening BSI</th>
        <th>Golongan</th>
        <th>Nama Golongan</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pegawai as $p)
      <tr>
        <td>
          <div class="d-flex justify-content-center align-items-center gap-2">
            <a href="{{ route('pegawai.edit', $p->nip_pegawai) }}" class="btn btn-sm btn-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
              <i class="bi bi-pencil-square"></i>
            </a>
            <form action="{{ route('pegawai.destroy', $p->nip_pegawai) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="return confirm('Yakin hapus data ini?')">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </div>
        </td>
        <td><input type="checkbox" class="checkItem" value="{{ $p->nip_pegawai }}"></td>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $p->nip_pegawai }}</td>
        <td>{{ $p->nama }}</td>
        <td>{{ $p->kdsatker }}</td>
        <td>{{ $p->jabatan }}</td>
        <td>{{ $p->no_rekening }}</td>
        <td>{{ $p->golongan }}</td>
        <td>{{ $p->nama_golongan }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>


{{-- Script untuk checkbox pilih semua --}}
<script>
  document.getElementById('checkAll').addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.checkItem').forEach(cb => cb.checked = checked);
  });
</script>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('pegawai.import') }}" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Import Data Pegawai</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <label for="file" class="form-label">Upload File Excel (.xlsx)</label>
        <input type="file" name="file" class="form-control" required>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Import</button>
      </div>
    </form>
  </div>
</div>

@endsection
