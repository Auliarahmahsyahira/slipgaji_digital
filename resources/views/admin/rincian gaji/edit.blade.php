@extends('layouts.admin')

@section('content')
  <h4 class="text-center fw-bold mb-4 text-dark">Edit Rincian Gaji</h4>

  <div class="card shadow-sm mx-auto" style="max-width: 600px;">
    <div class="card-body">
      <form action="{{ route('komponen.update', $komponen->id_komponen) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nama Komponen --}}
        <div class="mb-3">
          <label for="nama_komponen" class="form-label fw-bold">Nama Komponen</label>
          <input 
            type="text" 
            class="form-control @error('nama_komponen') is-invalid @enderror" 
            id="nama_komponen" 
            name="nama_komponen" 
            value="{{ old('nama_komponen', $komponen->nama_komponen) }}">
          @error('nama_komponen')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Tipe --}}
        <div class="mb-3">
          <label for="tipe" class="form-label fw-bold">Tipe</label>
          <select name="tipe" id="tipe" class="form-select @error('tipe') is-invalid @enderror">
            <option value="penghasilan" {{ $komponen->tipe == 'penghasilan' ? 'selected' : '' }}>Penghasilan</option>
            <option value="potongan" {{ $komponen->tipe == 'potongan' ? 'selected' : '' }}>Potongan</option>
          </select>
          @error('tipe')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Kategori --}}
        <div class="mb-3">
          <label for="kategori" class="form-label fw-bold">Kategori</label>
          <select name="kategori" id="kategori" class="form-select @error('kategori') is-invalid @enderror">
            <option value="wajib" {{ $komponen->kategori == 'wajib' ? 'selected' : '' }}>Wajib</option>
            <option value="lainnya" {{ $komponen->kategori == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
          </select>
          @error('kategori')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Tombol Aksi --}}
        <div class="text-end">
          <a href="{{ route('komponen.index') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-dark">Update</button>
        </div>
      </form>
    </div>
  </div>
@endsection
