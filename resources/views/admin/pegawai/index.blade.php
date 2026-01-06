@extends('layouts.admin')

@section('content')

<h4 class="text-center fw-bold mb-4 text-dark">
  Daftar Pegawai Kanwil Kemenag Prov. Kepri
</h4>

{{-- Tombol atas --}}
<div class="d-flex justify-content-between align-items-center mb-3"
     style="font-family: 'Times New Roman', Times, serif;">
  <div class="d-flex gap-2">
    <a href="{{ route('pegawai.create') }}"
       class="btn btn-success px-4"
       style="background-color: #006316;">
      Create
    </a>

    <button class="btn btn-success px-4"
            style="background-color: #006316;"
            data-bs-toggle="modal"
            data-bs-target="#modalImport">
      Import
    </button>
  </div>
</div>

{{-- CARD --}}
<div class="card shadow-sm">
  <div class="card-body">

    <div style="overflow-x:auto; max-height:500px;">
      <table id="tabelPegawai"
             class="table table-bordered table-striped align-middle text-center bg-white">
        <thead class="table-dark">
          <tr>
            <th style="width:110px;">Aksi</th>
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
        </tbody>
      </table>
    </div>

  </div>
</div>

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

<script>
$(function () {
    let table = $('#tabelPegawai').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('pegawai.data') }}",

    rowId: 'nip_pegawai', // PENTING
    scrollX: true,

    dom: 'lfrtip',
    paging: true,
    pagingType: 'simple_numbers',
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],

    columns: [
        { data: 'aksi', orderable: false, searchable: false },
        { data: 'checkbox', orderable: false, searchable: false },
        { data: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'nip_pegawai' },
        { data: 'nama' },
        { data: 'kdsatker' },
        { data: 'jabatan' },
        { data: 'no_rekening' },
        { data: 'golongan' },
    ],

    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/id.json"
    }
});
$('#checkAll').on('change', function () {
    $('.checkItem').prop('checked', this.checked);
});

$('#tabelPegawai').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});
});
</script>

<style>
/* Paksa DataTables control muncul */
.dataTables_length,
.dataTables_filter,
.dataTables_info,
.dataTables_paginate {
    display: block !important;
    visibility: visible !important;
}

/* Rapikan posisi */
.dataTables_length {
    float: left;
}

.dataTables_filter {
    float: right;
    text-align: right;
}

.dataTables_paginate {
    float: right;
}

.dataTables_info {
    float: left;
}

/* Biar tidak ketutup card */
.dataTables_wrapper {
    width: 100%;
    overflow-x: auto;
}
</style>




@endsection
