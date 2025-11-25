@extends('layouts.admin')

@section('content')

<div class="container mt-4">
  <h4 class="text-center fw-bold mb-4" style="font-family: 'Times New Roman', Times, serif; color: #000;">
    Input Pegawai
  </h4>
  <div class="d-flex justify-content-center">
    <div class="mx-auto p-4 shadow-lg" 
         style="max-width: 750px; background-color: #fff8dc; border-radius: 15px; border: 1px solid #d4af37;">

      <form action="{{ route('pegawai.store') }}" method="POST">
        @csrf

        {{-- NIP --}}
        <div class="row mb-3 align-items-center">
          <label class="col-sm-3 col-form-label fw-semibold text-dark">NIP</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="nip_pegawai" placeholder="Masukkan NIP pegawai" required>
          </div>
        </div>

        {{-- Nama --}}
        <div class="row mb-3 align-items-center">
          <label class="col-sm-3 col-form-label fw-semibold text-dark">Nama</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="nama" placeholder="Masukkan nama lengkap" required>
          </div>
        </div>

        {{-- Kdsatker --}}
        <div class="row mb-3 align-items-center">
          <label class="col-sm-3 col-form-label fw-semibold text-dark">Kdsatker</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="kdsatker" placeholder="Masukkan kdsatker" required>
          </div>
        </div>

        {{-- Jabatan --}}
        <div class="row mb-3 align-items-center">
          <label class="col-sm-3 col-form-label fw-semibold text-dark">Jabatan</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="jabatan" placeholder="Masukkan jabatan" required>
          </div>
        </div>

        {{-- Golongan --}}
        <div class="row mb-3 align-items-center">
          <label class="col-sm-3 col-form-label fw-semibold text-dark">Golongan</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="golongan" placeholder="Masukkan golongan" required>
          </div>
        </div>

        {{-- Nama Golongan --}}
        <div class="row mb-3 align-items-center">
          <label class="col-sm-3 col-form-label fw-semibold text-dark">Nama Golongan</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="nama_golongan" placeholder="Masukkan nama golongan" required>
          </div>
        </div>

        {{-- No. Rek --}}
        <div class="row mb-4 align-items-center">
          <label class="col-sm-3 col-form-label fw-semibold text-dark">No. Rek</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="no_rekening" placeholder="Masukkan nomor rekening" required>
          </div>
        </div>

        {{-- Password dan Konfirmasi --}}
        <div class="row mb-4">
          <div class="col-sm-6 text-center">
            <label class="fw-semibold text-dark">Password</label>
            <input type="password" class="form-control mt-1" id="password" name="password" required>
          </div>
          <div class="col-sm-6 text-center">
            <label class="fw-semibold text-dark">Ulangi Password</label>
            <input type="password" class="form-control mt-1" id="password_confirmation" name="password_confirmation" required>
          </div>
        </div>

        {{-- Checkbox tampilkan password --}}
        <div class="form-check mb-3 text-center">
          <input type="checkbox" class="form-check-input" id="showPassword">
          <label class="form-check-label text-dark" for="showPassword">Tampilkan Password</label>
        </div>

        {{-- Tombol Simpan --}}
        <div class="d-flex justify-content-end mt-4">
          <button type="submit" class="btn fw-semibold px-5 py-2"
                  style="background-color: #ffd700; color: black; border-radius: 25px; border: 1px solid #b8860b;">
            Simpan
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- Script untuk tampilkan password --}}
<script>
  document.getElementById('showPassword').addEventListener('change', function () {
    const password = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const type = this.checked ? 'text' : 'password';
    password.type = confirm.type = type;
  });
</script>

@endsection
