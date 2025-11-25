@extends('layouts.admin')

@section('content')
  <h4 class="text-center fw-bold mb-4 text-dark">Tambah Rincian Gaji</h4>

  <div class="card shadow-sm mx-auto" style="max-width: 600px;">
    <div class="card-body">
      <form action="{{ route('komponen.store') }}" method="POST">
        @csrf

        {{-- Nama Komponen --}}
        <div class="mb-3">
          <label for="nama_komponen" class="form-label fw-bold">Nama Komponen</label>
          <input 
            type="text" 
            class="form-control @error('nama_komponen') is-invalid @enderror" 
            id="nama_komponen" 
            name="nama_komponen" 
            placeholder="Contoh: Tunjangan Kinerja" 
            value="{{ old('nama_komponen') }}">
          @error('nama_komponen')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Tipe --}}
        <div class="mb-3">
          <label for="tipe" class="form-label fw-bold">Tipe</label>
          <select name="tipe" id="tipe" class="form-select @error('tipe') is-invalid @enderror">
            <option value="">-- Pilih Tipe --</option>
            <option value="penghasilan" {{ old('tipe') == 'penghasilan' ? 'selected' : '' }}>Penghasilan</option>
            <option value="potongan" {{ old('tipe') == 'potongan' ? 'selected' : '' }}>Potongan</option>
          </select>
          @error('tipe')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Kategori --}}
        <div class="mb-3">
          <label for="kategori" class="form-label fw-bold">Kategori</label>
          <select name="kategori" id="kategori" class="form-select @error('kategori') is-invalid @enderror">
            <option value="">-- Pilih Kategori --</option>
            <option value="wajib" {{ old('kategori') == 'wajib' ? 'selected' : '' }}>Wajib</option>
            <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
          </select>
          @error('kategori')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Tombol Aksi --}}
        <div class="text-end">
          <a href="{{ route('komponen.index') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-dark">Simpan</button>
        </div>
      </form>
    </div>
  </div>
@endsection
