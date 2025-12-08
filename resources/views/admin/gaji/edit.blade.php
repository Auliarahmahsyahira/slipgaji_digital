@extends('layouts.admin')

@section('content')

  <div class="text-center mb-5">
    <h4 class="fw-bold text-dark text-shadow-sm">Edit Slip Gaji Pegawai</h4>
  </div>

  <form action="{{ route('slipgaji.update', $gaji->id_gaji) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- IDENTITAS PEGAWAI --}}
    <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
      <div class="card-header fw-semibold bg-white border-0 text-secondary">Identitas Pegawai</div>
      <div class="card-body bg-white rounded">
        <div class="row mb-3">
          <div class="col-md-3 fw-semibold">Periode</div>
          <div class="col-md-9">
            <input type="month" name="periode" class="form-control border-0 border-bottom bg-light-subtle"
              value="{{ \Carbon\Carbon::parse($gaji->periode)->format('Y-m') }}" required>
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

    {{-- PENGHASILAN --}}
    <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
      <div class="card-header fw-semibold bg-white border-0 text-secondary">Penghasilan</div>
      <div class="card-body bg-white rounded">

        {{-- Gaji Pokok --}}
        <div class="row mb-3">
          <div class="col-md-3 fw-semibold">Gaji Pokok</div>
          <div class="col-md-9">
            <div class="input-group">
              <input type="number" name="gaji_pokok" class="form-control border-0 border-bottom bg-light-subtle text-end hitung-kotor"
                value="{{ $gaji->gaji_pokok }}" step="any">
              <span class="input-group-text bg-light-subtle border-0 border-bottom">Rp</span>
            </div>
          </div>
        </div>

        {{-- Komponen penghasilan lainnya --}}
        @foreach($komponen as $item)
          @if($item->tipe == 'penghasilan' && $item->kategori != 'gaji_pokok')
            @php
              $nilai = ($gaji->penghasilan ?? collect())->where('id_komponen', $item->id_komponen)->first()->nominal ?? 0;
            @endphp
            <div class="row mb-3">
              <div class="col-md-3 fw-semibold">{{ $item->nama_komponen }}</div>
              <div class="col-md-9">
                <div class="input-group">
                  <input type="number" step="any" class="form-control border-0 border-bottom bg-light-subtle text-end hitung-kotor"
                    name="nominal[{{ $item->id_komponen }}]" value="{{ $nilai }}" placeholder="0">
                  <span class="input-group-text bg-light-subtle border-0 border-bottom">Rp</span>
                </div>
              </div>
            </div>
          @endif
        @endforeach

        {{-- Jumlah Kotor --}}
        <div class="row mt-4">
          <div class="col-md-3 fw-semibold text-danger">Jumlah Kotor</div>
          <div class="col-md-9">
            <input type="text" id="jumlah_kotor_tampil" readonly
              class="form-control border-0 border-bottom bg-light-subtle text-end"
              value="{{ number_format($gaji->jumlah_kotor, 0, ',', '.') }}">
            <input type="hidden" name="jumlah_kotor" id="jumlah_kotor" value="{{ $gaji->jumlah_kotor }}">
          </div>
        </div>
      </div>
    </div>

    {{-- POTONGAN WAJIB --}}
    <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
      <div class="card-header fw-semibold bg-white border-0 text-secondary">Potongan Wajib</div>
      <div class="card-body bg-white rounded">
        @foreach($komponen as $item)
          @if($item->tipe == 'potongan' && $item->kategori == 'wajib')
            @php
              $nilai = ($gaji->potongan ?? collect())->where('id_komponen', $item->id_komponen)->first()->nominal ?? 0;
            @endphp
            <div class="row mb-3">
              <div class="col-md-3 fw-semibold">{{ $item->nama_komponen }}</div>
              <div class="col-md-9">
                <div class="input-group">
                  <input type="number" step="any" class="form-control border-0 border-bottom bg-light-subtle text-end hitung-potongan"
                    name="nominal[{{ $item->id_komponen }}]" value="{{ $nilai }}" placeholder="0">
                  <span class="input-group-text bg-light-subtle border-0 border-bottom">Rp</span>
                </div>
              </div>
            </div>
          @endif
        @endforeach

        {{-- Jumlah Potongan --}}
        <div class="row mt-4">
          <div class="col-md-3 fw-semibold text-danger">Jumlah Potongan</div>
          <div class="col-md-9">
            <input type="text" id="jumlah_potongan_tampil" readonly
              class="form-control border-0 border-bottom bg-light-subtle text-end"
              value="{{ number_format($gaji->jumlah_potongan, 0, ',', '.') }}">
            <input type="hidden" name="jumlah_potongan" id="jumlah_potongan" value="{{ $gaji->jumlah_potongan }}">
          </div>
        </div>
      </div>
    </div>

    {{-- POTONGAN LAINNYA --}}
    <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
      <div class="card-header fw-semibold bg-white border-0 text-secondary">Potongan Lainnya</div>
      <div class="card-body bg-white rounded">

        {{-- Gaji Bersih --}}
        <div class="row mb-3">
          <div class="col-md-3 fw-semibold text-success">Gaji Bersih</div>
          <div class="col-md-9">
            <input type="text" id="jumlah_bersih_tampil" readonly
              class="form-control border-0 border-bottom bg-light-subtle text-end"
              value="{{ number_format($gaji->jumlah_bersih, 0, ',', '.') }}">
            <input type="hidden" name="jumlah_bersih" id="jumlah_bersih" value="{{ $gaji->jumlah_bersih }}">
          </div>
        </div>

    {{-- TOMBOL UPDATE --}}
    <div class="text-center mt-5 mb-4">
      <button type="submit" class="btn btn-dark px-5 py-2 fw-semibold shadow-sm">Update</button>
      <a href="{{ route('slipgaji.index') }}" class="btn btn-secondary px-5 py-2 fw-semibold shadow-sm">Batal</a>
    </div>
  </form>

  <script>
    // Auto isi nama dari NIP
    document.getElementById('nip_pegawai').addEventListener('change', function() {
      const nip = this.value.trim();
      const namaInput = document.getElementById('nama');
      if (nip !== '') {
        fetch(`{{ url('/cek-nip') }}/${nip}`)
          .then(res => res.json())
          .then(data => {
            if (data.success) namaInput.value = data.nama;
            else {
              namaInput.value = '';
              alert(data.message);
            }
          });
      }
    });

    // Hitung otomatis
    function hitungGaji() {
      let totalKotor = 0;
      document.querySelectorAll('.hitung-kotor').forEach(el => totalKotor += Number(el.value) || 0);
      document.getElementById('jumlah_kotor_tampil').value = totalKotor.toLocaleString('id-ID');
      document.getElementById('jumlah_kotor').value = totalKotor;

      let totalPotongan = 0;
      document.querySelectorAll('.hitung-potongan').forEach(el => totalPotongan += Number(el.value) || 0);
      document.getElementById('jumlah_potongan_tampil').value = totalPotongan.toLocaleString('id-ID');
      document.getElementById('jumlah_potongan').value = totalPotongan;

      const gajiBersih = totalKotor - totalPotongan;
      document.getElementById('jumlah_bersih_tampil').value = gajiBersih.toLocaleString('id-ID');
      document.getElementById('jumlah_bersih').value = gajiBersih;
    }

    document.addEventListener('input', e => {
      if (e.target.classList.contains('hitung-kotor') || e.target.classList.contains('hitung-potongan') || e.target.classList.contains('hitung-total')) {
        hitungGaji();
      }
    });
  </script>

@endsection
