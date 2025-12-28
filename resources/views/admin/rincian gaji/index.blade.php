@extends('layouts.admin')

@section('content')
  <h4 class="text-center fw-bold mb-5 text-dark">Rincian Gaji Kanwil Kemenag Prov. Kepri</h4>
{{-- Baris atas: Create di kiri, Search di kanan --}}
  <div class="d-flex justify-content-between align-items-center mb-3" style="font-family: 'Times New Roman', Times, serif;">
    
    {{-- Tombol Create di kiri --}}
    <a href="{{ route('komponen.create') }}" class="btn btn-success px-4" style="background-color: #006316;">Create</a>

    {{-- Pencarian di kanan --}}
    <form method="GET" action="{{ route('komponen.index') }}" class="input-group w-auto">
      <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="height: 35px;">
        <i class="bi bi-search"></i>
      </span>
      <input 
        type="text" 
        name="search" 
        class="form-control border-start-0" style="width: 180px; height: 35px;"
        placeholder="Cari Rincian.."
        value="{{ request('search') }}"
      >
    </form>
  </div>


  {{-- Pesan sukses --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Tabel komponen gaji --}}
  <div class="table-responsive">
    <table class="table table-bordered text-center align-middle bg-white shadow-sm">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama Komponen</th>
          <th>Tipe</th>
          <th>Kategori</th>
          <th>Periode</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($komponen as $index => $item)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $item->nama_komponen }}</td>
          <td>
            @if($item->tipe == 'penghasilan')
              <span class="badge bg-success">Penghasilan</span>
            @else
              <span class="badge bg-danger">Potongan</span>
            @endif
          </td>
          <td>
            @if($item->kategori == 'wajib')
              <span class="badge bg-primary">Wajib</span>
            @else
              <span class="badge bg-secondary">Lainnya</span>
            @endif
          </td>
          <td>
            @if($item->periode == '24')
              <span class="badge bg-warning">2 Tahun</span>
            @else
              <span class="badge bg-info">Bulanan</span>
            @endif
          </td>
          <td>
           <a href="{{ route('komponen.edit', $item->id_komponen) }}" class="btn btn-sm btn-warning">Edit</a>
          </form>
            <form action="{{ route('komponen.destroy', $item->id_komponen) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-muted">Belum ada data komponen gaji.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
