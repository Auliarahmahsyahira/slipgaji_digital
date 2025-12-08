@extends('layouts.admin')

@section('content')

<div class="text-center mb-4">
  <h3 class="fw-bold text-dark text-shadow-sm">
    Daftar Slip Gaji Pegawai Kanwil Kemenag Prov. Kepri
  </h3>
</div>

<div class="position-relative mb-4" style="min-height: 100px;">
  <a href="{{ route('slipgaji.create') }}" class="btn btn-dark position-absolute" style="top: 0; left: 60px; background-color: #006316;">Create</a>

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


<div class="d-flex justify-content-between align-items-center mb-3 px-4">
    
    <!-- Tombol Import Atas -->
    <div>
        <form action="{{ route('gaji.import.tetap') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls" required onchange="this.form.submit()" id="importTetap" style="display:none;">
            <label for="importTetap" class="btn btn-import">Import</label>
        </form>
    </div>

    <!-- SEARCH -->
    <form action="{{ route('slipgaji.index')}}" method="GET" style="width:260px;">
    <div class="input-group search-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input 
            type="text" 
            name="search" 
            class="form-control" 
            placeholder="Cari NIP..." 
            value="{{ request('search') }}"
        >
    </div>
</form>

</div>

<div class="table-responsive mb-4" style="max-height: 400px; overflow-y: auto; margin-top:40px;">
  <table class="table table-bordered bg-white text-center align-middle">
    <thead class="table-light" style="font-family: 'Times New Roman', Times, serif;">
      <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>No</th>
        <th>Periode</th>
        <th>Nama</th>
        <th>Gaji Pokok</th>
        <th>Tun. Istri/Suami</th>
        <th>Tun. Anak</th>
        <th>Tun. Umum</th>
        <th>Tun. Ta. Umum</th>
        <th>Tun. Papua</th>
        <th>Tun. Terpencil</th>
        <th>Tun. Struktur</th>
        <th>Tun. Fungsi</th>
        <th>Tun. Lain</th>
        <th>Tun. Bulat</th>
        <th>Tun. Beras</th>
        <th>Tun. Pajak</th>
        <th>Jumlah Kotor</th>
        <th>Pot. Beras</th>
        <th>IWP</th>
        <th>BPJS</th>
        <th>BPJS Lain</th>
        <th>Pot. Pph</th>
        <th>Sewa Rumah</th>
        <th>Tunggakan</th>
        <th>Utang</th>
        <th>Pot. Lain</th>
        <th>Taperum</th>
        <th>Jumlah Potongan</th>
        <th>Gaji Bersih</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($gaji as $index => $item)
      @php
        $tunjIstri      = $item->penghasilan->where('id_komponen', 1)->sum('nominal');
        $tunjAnak       = $item->penghasilan->where('id_komponen', 2)->sum('nominal');
        $tunjUmum       = $item->penghasilan->where('id_komponen', 3)->sum('nominal');
        $tunjTaUmum     = $item->penghasilan->where('id_komponen', 4)->sum('nominal');
        $tunjPapua      = $item->penghasilan->where('id_komponen', 5)->sum('nominal');
        $tunjTerpencil  = $item->penghasilan->where('id_komponen', 6)->sum('nominal');
        $tunjStruktur   = $item->penghasilan->where('id_komponen', 7)->sum('nominal');
        $tunjFungsi     = $item->penghasilan->where('id_komponen', 8)->sum('nominal');
        $tunjLain       = $item->penghasilan->where('id_komponen', 9)->sum('nominal');
        $tunjBulat      = $item->penghasilan->where('id_komponen', 10)->sum('nominal');
        $tunjBeras      = $item->penghasilan->where('id_komponen', 11)->sum('nominal');
        $tunjPajak      = $item->penghasilan->where('id_komponen', 12)->sum('nominal');

        $potBeras       = $item->potongan->where('id_komponen', 13)->sum('nominal');
        $IWP            = $item->potongan->where('id_komponen', 38)->sum('nominal');
        $BPJS           = $item->potongan->where('id_komponen', 15)->sum('nominal');
        $BPJSlain       = $item->potongan->where('id_komponen', 16)->sum('nominal');
        $PotPph         = $item->potongan->where('id_komponen', 17)->sum('nominal');
        $SewaRmh        = $item->potongan->where('id_komponen', 18)->sum('nominal');
        $Tunggakan      = $item->potongan->where('id_komponen', 19)->sum('nominal');
        $Utang          = $item->potongan->where('id_komponen', 20)->sum('nominal');
        $PotLain        = $item->potongan->where('id_komponen', 21)->sum('nominal');
        $Taperum        = $item->potongan->where('id_komponen', 22)->sum('nominal');
      @endphp

        <tr>
          <td><input type="checkbox" name="ids[]" form="form-selected" value="{{ $item->id_gaji }}"></td>
          <td>{{ $index + 1 }}</td>
          <td>{{ \Carbon\Carbon::parse($item->periode)->translatedFormat('F Y') }}</td>
          <td>{{ $item->pegawai->nama ?? '-' }}</td>
          <td>{{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjIstri, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjAnak, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjUmum, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjTaUmum, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjPapua, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjTerpencil, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjStruktur, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjFungsi, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjLain, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjBulat, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjBeras, 0, ',', '.') }}</td>
          <td>{{ number_format($tunjPajak, 0, ',', '.') }}</td>
          <td>{{ number_format($item->jumlah_kotor, 0, ',', '.') }}</td>
          <td>{{ number_format($potBeras, 0, ',', '.') }}</td>
          <td>{{ number_format($IWP, 0, ',', '.') }}</td>
          <td>{{ number_format($BPJS, 0, ',', '.') }}</td>
          <td>{{ number_format($BPJSlain, 0, ',', '.') }}</td>
          <td>{{ number_format($PotPph, 0, ',', '.') }}</td>
          <td>{{ number_format($SewaRmh, 0, ',', '.') }}</td>
          <td>{{ number_format($Tunggakan, 0, ',', '.') }}</td>
          <td>{{ number_format($Utang, 0, ',', '.') }}</td>
          <td>{{ number_format($PotLain, 0, ',', '.') }}</td>
          <td>{{ number_format($Taperum, 0, ',', '.') }}</td>
          <td>{{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
          <td>{{ number_format($item->jumlah_bersih, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="12">Data belum tersedia</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
</div>

 <div class="mb-3 px-4 text-start">
    <form action="{{ route('gaji.import.bulanan') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".xlsx,.xls" required onchange="this.form.submit()" id="importBulanan" style="display:none;">
        <label for="importBulanan" class="btn btn-import">Import</label>
    </form>
</div>


<div class="table-responsive" style="max-height: 400px; overflow-y: auto; margin-top: 40px;">
  <table class="table table-bordered bg-white text-center align-middle">
    <thead class="table-light" style="font-family: 'Times New Roman', Times, serif;">
      <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>No</th>
        <th>Periode</th>
        <th>Nama</th>
        <th>Gaji Bersih</th>
        <th>Bank Riau</th>
        <th>Bank Syariah Riau</th>
        <th>Bank BTN</th>
        <th>Koperasi Kanwil</th>
        <th>Koperasi Bintan</th>
        <th>Zakat</th>
        <th>Infak</th>
        <th>Donatur Masjid</th>
        <th>BPR Bintan</th>
        <th>Qurban</th>
        <th>Wakaf Uang</th>
        <th>Dharma Wanita</th>
        <th>Dansos</th>
        <th>BPJS 1%</th>
        <th>Lain-lain</th>
        <th>Total Potongan</th>
        <th>Gaji Diterima</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($gaji as $index => $item)
      @php
          // DETAIL POTONGAN
          $bankRiau       = $item->potongan->where('id_komponen', 21)->sum('nominal');
          $bankSyariah    = $item->potongan->where('id_komponen', 22)->sum('nominal');
          $bankBTN        = $item->potongan->where('id_komponen', 23)->sum('nominal');
          $koperasiKanwil = $item->potongan->where('id_komponen', 24)->sum('nominal');
          $koperasiBintan = $item->potongan->where('id_komponen', 25)->sum('nominal');
          $zakat          = $item->potongan->where('id_komponen', 26)->sum('nominal');
          $infak          = $item->potongan->where('id_komponen', 27)->sum('nominal');
          $donaturMasjid  = $item->potongan->where('id_komponen', 28)->sum('nominal');
          $bprBintan      = $item->potongan->where('id_komponen', 29)->sum('nominal');
          $qurban         = $item->potongan->where('id_komponen', 30)->sum('nominal');
          $wakafUang      = $item->potongan->where('id_komponen', 31)->sum('nominal');
          $dharmaWanita   = $item->potongan->where('id_komponen', 32)->sum('nominal');
          $dansos         = $item->potongan->where('id_komponen', 33)->sum('nominal');
          $bpjs1          = $item->potongan->where('id_komponen', 34)->sum('nominal');
          $lainLain       = $item->potongan->where('id_komponen', 35)->sum('nominal');
        @endphp

        <tr>
          <td><input type="checkbox" name="ids[]" form="form-selected" value="{{ $item->id_gaji }}"></td>
          <td>{{ $index + 1 }}</td>
          <td>{{ \Carbon\Carbon::parse($item->periode)->translatedFormat('F Y') }}</td>
          <td>{{ $item->pegawai->nama ?? '-' }}</td>
          <td>{{ number_format($item->jumlah_bersih, 0, ',', '.') }}</td>
          <td>{{ number_format($bankRiau, 0, ',', '.') }}</td>
          <td>{{ number_format($bankSyariah, 0, ',', '.') }}</td>
          <td>{{ number_format($bankBTN, 0, ',', '.') }}</td>
          <td>{{ number_format($koperasiKanwil, 0, ',', '.') }}</td>
          <td>{{ number_format($koperasiBintan, 0, ',', '.') }}</td>
          <td>{{ number_format($zakat, 0, ',', '.') }}</td>
          <td>{{ number_format($infak, 0, ',', '.') }}</td>
          <td>{{ number_format($donaturMasjid, 0, ',', '.') }}</td>
          <td>{{ number_format($bprBintan, 0, ',', '.') }}</td>
          <td>{{ number_format($qurban, 0, ',', '.') }}</td>
          <td>{{ number_format($wakafUang, 0, ',', '.') }}</td>
          <td>{{ number_format($dharmaWanita, 0, ',', '.') }}</td>
          <td>{{ number_format($dansos, 0, ',', '.') }}</td>
          <td>{{ number_format($bpjs1, 0, ',', '.') }}</td>
          <td>{{ number_format($lainLain, 0, ',', '.') }}</td>
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
.btn-import {
    background-color: #006316;
    color: #fff;
    padding: 6px 18px;
    border-radius: 6px;
    font-weight: 600;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
    transition: 0.2s ease;
}
.btn-import:hover {
    background-color: #004b12;
    transform: translateY(-2px);
}

/* Search Box */
.search-box {
    width: 230px;
}
.search-group .input-group-text {
        background: white;
        border-right: none;
        height: 38px;           /* Samain tinggi icon di sini */
        display: flex;
        align-items: center;
        font-size: 1rem;
    }

    .search-group .form-control {
        height: 38px;           /* Samain tinggi input di sini */
        border-left: none;
        box-shadow: none;
    }

    .search-group .form-control:focus {
        box-shadow: none;
    }

.table th {
    white-space: nowrap;
    vertical-align: middle;
    text-align: center;
}

.table td {
    vertical-align: middle;
    text-align: center;
}

.table-responsive {
    overflow-x: auto;
    white-space: nowrap;
}

/* Header Title */
h3 {
    font-size: 26px;
    letter-spacing: 0.5px;
}
</style>

@endsection
