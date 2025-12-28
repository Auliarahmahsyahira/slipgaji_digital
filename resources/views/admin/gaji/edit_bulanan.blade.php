@extends('layouts.admin')

@section('content')

  <div class="text-center mb-5">
    <h4 class="fw-bold text-dark text-shadow-sm">Edit Slip Gaji Bulanan Pegawai</h4>
  </div>

  <form action="{{ route('slipgaji.update2', $gaji->id) }}" method="POST">
    @method('PUT')
    @csrf

    {{-- IDENTITAS PEGAWAI --}}
    <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
      <div class="card-header fw-semibold bg-white border-0 text-secondary">Identitas Pegawai</div>
      <div class="card-body bg-white rounded">
        <div class="row mb-3">
          <div class="col-md-3 fw-semibold">Periode</div>
          <div class="col-md-9">
            <input type="text" name="periode" class="form-control border-0 border-bottom bg-light-subtle" value="{{ $gaji->periode }}" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3 fw-semibold">Nama</div>
          <div class="col-md-9">
            <input type="text" name="nama" id="nama" class="form-control border-0 border-bottom bg-light-subtle"
              value="{{ $gaji->pegawai->nama ?? '' }}" readonly required>
          </div>
        </div>
      </div>
    </div>


    {{-- POTONGAN LAINNYA --}}
    <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
      <div class="card-header fw-semibold bg-white border-0 text-secondary">Potongan Bulanan</div>
      <div class="card-body bg-white rounded">

        {{-- Gaji Bersih --}}
        <div class="row mb-3">
          <div class="col-md-3 fw-semibold text-success">Gaji Bersih</div>
          <div class="col-md-9">
            <input type="text" id="jumlah_bersih_tampil" readonly
              class="form-control border-0 border-bottom bg-light-subtle text-end"
              value="{{ number_format($gaji->gaji->jumlah_bersih, 0, ',', '.') }}">
            <input type="hidden" name="jumlah_bersih" id="jumlah_bersih" value="{{ $gaji->gaji->jumlah_bersih }}">
          </div>
        </div>

        @foreach($komponen as $item)
          @if($item->tipe == 'potongan' && $item->kategori == 'lainnya')
            @php
              $nilai = ($gaji->potonganB ?? collect())->where('id_komponen', $item->id_komponen)->first()->nominal ?? 0;
            @endphp
            <div class="row mb-3">
              <div class="col-md-3 fw-semibold">{{ $item->nama_komponen }}</div>
              <div class="col-md-9">
                <div class="input-group">
                  <input type="number" step="any" class="form-control border-0 border-bottom bg-light-subtle text-end hitung-total"
                    name="nominal[{{ $item->id_komponen }}]" value="{{ $nilai }}" placeholder="0">
                  <span class="input-group-text bg-light-subtle border-0 border-bottom">Rp</span>
                </div>
              </div>
            </div>
          @endif
        @endforeach

        {{-- Total Potongan --}}
        <div class="row mt-4">
          <div class="col-md-3 fw-semibold text-danger">Total Potongan</div>
          <div class="col-md-9">
            <input type="text" id="total_potongan_tampil" readonly
              class="form-control border-0 border-bottom bg-light-subtle text-end"
              value="{{ number_format($gaji->total_potongan, 0, ',', '.') }}">
            <input type="hidden" name="total_potongan" id="total_potongan" value="{{ $gaji->total_potongan }}">
          </div>
        </div>

        {{-- Gaji Diterima --}}
        <div class="row mt-3">
          <div class="col-md-3 fw-semibold text-primary">Gaji Diterima</div>
          <div class="col-md-9">
            <input type="text" id="gaji_diterima_tampil" readonly
              class="form-control border-0 border-bottom bg-light-subtle text-end"
              value="{{ number_format($gaji->gaji_diterima, 0, ',', '.') }}">
            <input type="hidden" name="gaji_diterima" id="gaji_diterima" value="{{ $gaji->gaji_diterima }}">
          </div>
        </div>
      </div>
    </div>

    {{-- TOMBOL UPDATE --}}
    <div class="text-center mt-5 mb-4">
      <button type="submit" class="btn btn-dark px-5 py-2 fw-semibold shadow-sm">Update</button>
      <a href="{{ route('slipgaji.index') }}" class="btn btn-secondary px-5 py-2 fw-semibold shadow-sm">Batal</a>
    </div>
  </form>

  <script>
  function hitungGaji() {
    // ambil jumlah bersih dari hidden input
    const jumlahBersih = Number(document.getElementById('jumlah_bersih').value) || 0;

    // hitung total potongan
    let totalPotongan = 0;
    document.querySelectorAll('.hitung-total').forEach(el => {
      totalPotongan += Number(el.value) || 0;
    });

    // tampilkan total potongan
    document.getElementById('total_potongan_tampil').value =
      totalPotongan.toLocaleString('id-ID');
    document.getElementById('total_potongan').value = totalPotongan;

    // hitung gaji diterima
    const gajiDiterima = jumlahBersih - totalPotongan;

    document.getElementById('gaji_diterima_tampil').value =
      gajiDiterima.toLocaleString('id-ID');
    document.getElementById('gaji_diterima').value = gajiDiterima;
  }

  // jalankan saat input potongan berubah
  document.querySelectorAll('.hitung-total').forEach(el => {
    el.addEventListener('input', hitungGaji);
  });

  // hitung saat halaman pertama kali dibuka
  document.addEventListener('DOMContentLoaded', hitungGaji);
</script>


@endsection
