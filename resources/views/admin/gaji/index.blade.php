@extends('layouts.admin')

@section('content')

<div class="text-center mb-4">
  <h3 class="fw-bold text-dark text-shadow-sm">
    Daftar Slip Gaji Pegawai Kanwil Kemenag Prov. Kepri
  </h3>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 px-4">
    
    <!-- Tombol Import tahun -->
    <div>
        <button class="btn btn-success px-4" style="background-color: #006316;" data-bs-toggle="modal" data-bs-target="#modalImporttahun">Import</button>
    </div>

    <!-- SEARCH -->
     <div class="d-flex align-items-center gap-2">
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

<!-- CREATE ICON -->
        <button class="btn btn-success btn-icon"
                title="Tambah Slip Gaji"
                data-bs-toggle="modal"
                data-bs-target="#modalCreate">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>
</div>


<div class="table-responsive mb-4" style="max-height: 400px; overflow-y: auto; margin-top:40px;">
  <table class="table table-bordered bg-white text-center align-middle">
    <thead class="table-light" style="font-family: 'Times New Roman', Times, serif;">
      <tr>
        <th>Aksi</th>
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
      @forelse ($gajiWajib as $index => $item)
      @php
        $tunjIstri      = $item->penghasilan->where('id_komponen', 29)->sum('nominal');
        $tunjAnak       = $item->penghasilan->where('id_komponen', 30)->sum('nominal');
        $tunjUmum       = $item->penghasilan->where('id_komponen', 31)->sum('nominal');
        $tunjTaUmum     = $item->penghasilan->where('id_komponen', 32)->sum('nominal');
        $tunjPapua      = $item->penghasilan->where('id_komponen', 33)->sum('nominal');
        $tunjTerpencil  = $item->penghasilan->where('id_komponen', 34)->sum('nominal');
        $tunjStruktur   = $item->penghasilan->where('id_komponen', 35)->sum('nominal');
        $tunjFungsi     = $item->penghasilan->where('id_komponen', 36)->sum('nominal');
        $tunjLain       = $item->penghasilan->where('id_komponen', 37)->sum('nominal');
        $tunjBulat      = $item->penghasilan->where('id_komponen', 38)->sum('nominal');
        $tunjBeras      = $item->penghasilan->where('id_komponen', 39)->sum('nominal');
        $tunjPajak      = $item->penghasilan->where('id_komponen', 40)->sum('nominal');

        $potBeras       = $item->potongan->where('id_komponen', 41)->sum('nominal');
        $IWP            = $item->potongan->where('id_komponen', 42)->sum('nominal');
        $BPJS           = $item->potongan->where('id_komponen', 43)->sum('nominal');
        $BPJSlain       = $item->potongan->where('id_komponen', 44)->sum('nominal');
        $PotPph         = $item->potongan->where('id_komponen', 45)->sum('nominal');
        $SewaRmh        = $item->potongan->where('id_komponen', 46)->sum('nominal');
        $Tunggakan      = $item->potongan->where('id_komponen', 47)->sum('nominal');
        $Utang          = $item->potongan->where('id_komponen', 48)->sum('nominal');
        $PotLain        = $item->potongan->where('id_komponen', 49)->sum('nominal');
        $Taperum        = $item->potongan->where('id_komponen', 50)->sum('nominal');
      @endphp

        <tr>
          <td><a href="{{ route('slipgaji.edit', $item->id_gaji) }}" class="btn btn-sm btn-warning" title="Edit Slip Gaji"> <i class="bi bi-pencil-square"></i></a></td>
          <td>{{ $index + 1 }}</td>
          <td>{{ $item->periode }}</td>
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
        <tr><td colspan="30">Data belum tersedia</td></tr>
      @endforelse
    </tbody>
</table>
</div>
</div>

 <div class="mb-3 px-4 text-start">
    <button class="btn btn-success px-4" style="background-color: #006316;" data-bs-toggle="modal" data-bs-target="#modalImportBulanan">Import</button>
</div>

<div class="table-responsive" style="max-height: 400px; overflow-y: auto; margin-top: 40px;">
  <table class="table table-bordered bg-white text-center align-middle">
    <thead class="table-light" style="font-family: 'Times New Roman', Times, serif;">
      <tr>
        <th>Aksi</th>
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
      @forelse ($gajiBulanan as $index => $item)
      @php

          $pot = $item->potonganB ?? collect();
          // DETAIL POTONGAN
          $bankRiau       = $pot->where('id_komponen', 51)->sum('nominal');
          $bankSyariah    = $pot->where('id_komponen', 52)->sum('nominal');
          $bankBTN        = $pot->where('id_komponen', 53)->sum('nominal');
          $koperasiKanwil = $pot->where('id_komponen', 54)->sum('nominal');
          $koperasiBintan = $pot->where('id_komponen', 55)->sum('nominal');
          $zakat          = $pot->where('id_komponen', 56)->sum('nominal');
          $infak          = $pot->where('id_komponen', 57)->sum('nominal');
          $donaturMasjid  = $pot->where('id_komponen', 58)->sum('nominal');
          $bprBintan      = $pot->where('id_komponen', 59)->sum('nominal');
          $qurban         = $pot->where('id_komponen', 60)->sum('nominal');
          $wakafUang      = $pot->where('id_komponen', 61)->sum('nominal');
          $dharmaWanita   = $pot->where('id_komponen', 62)->sum('nominal');
          $dansos         = $pot->where('id_komponen', 63)->sum('nominal');
          $bpjs1          = $pot->where('id_komponen', 64)->sum('nominal');
          $lainLain       = $pot->where('id_komponen', 65)->sum('nominal');
        @endphp

        <tr>
          <td><a href="{{ route('slipgaji.edit_bulanan', $item->id) }}" class="btn btn-sm btn-warning me-1" title="Edit Slip Bulanan"> <i class="bi bi-pencil-square"></i></a>

          <form action="{{ route('slipgaji.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')"> 
            @csrf 
            @method('DELETE') 
            <button type="submit" class="btn btn-sm btn-danger" title="Hapus"> <i class="bi bi-trash"></i> </button> 
          </form>
          </td>
          <td>{{ $index + 1 }}</td>
          <td>{{ $item->periode }}</td>
          <td>{{ $item->pegawai->nama ?? '-' }}</td>
          <td>{{ number_format($item->gaji->jumlah_bersih, 0, ',', '.') }}</td>
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
        <tr><td colspan="22">Data belum tersedia</td></tr>
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

  document.querySelectorAll('.check-wajib').forEach(cb => {
  cb.addEventListener('change', () => {
    if (cb.checked) {
      document.querySelectorAll('.check-bulanan').forEach(b => b.checked = false);
      document.getElementById('select-all-bulanan').checked = false;
    }
  });
});

document.getElementById('form-edit').addEventListener('submit', function (e) {
    const checked = document.querySelectorAll('input[name="ids[]"]:checked');

    if (checked.length === 0) {
        e.preventDefault();
        alert('Pilih satu data yang ingin di-update!');
        return;
    }

    if (checked.length > 1) {
        e.preventDefault();
        alert('Hanya boleh pilih satu data!');
        return;
    }

    const mode = checked[0].dataset.mode;
    document.getElementById('edit-mode').value = mode;
});

document.querySelectorAll('.check-bulanan').forEach(cb => {
  cb.addEventListener('change', () => {
    if (cb.checked) {
      document.querySelectorAll('.check-wajib').forEach(w => w.checked = false);
      document.getElementById('select-all-wajib').checked = false;
    }
  });
});

document.querySelectorAll('input[type="checkbox"][name="ids[]"]').forEach(cb => {
  cb.addEventListener('change', function () {
    this.closest('tr').classList.toggle('selected', this.checked);
  });
});
</script>

<!-- Modal Import 2 tahun-->
<div class="modal fade" id="modalImportDuaTahun" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST"
          action="{{ route('slipgaji.store.duatahun') }}"
          enctype="multipart/form-data"
          class="modal-content">
      @csrf

      <div class="modal-header">
        <h5 class="modal-title">Import Slip Gaji Dua Tahun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <label class="form-label">Upload File Excel (.xlsx)</label>
        <input type="file" name="file" class="form-control" required>
        <small class="text-muted">
          Format kolom harus sesuai ID komponen master
        </small>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Import</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Import Bulanan-->
<div class="modal fade" id="modalImportBulanan" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST"
          action="{{ route('slipgaji.store.bulanan') }}"
          enctype="multipart/form-data"
          class="modal-content">
      @csrf

      <div class="modal-header">
        <h5 class="modal-title">Import Slip Gaji Bulanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <label class="form-label">Upload File Excel (.xlsx)</label>
        <input type="file" name="file" class="form-control" required>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Import</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Pilih Jenis Input</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">

                <p class="mb-4">Silakan pilih jenis input slip gaji:</p>

                <a href="{{ route('slipgaji.create.duatahun') }}"
                   class="btn btn-success w-100 mb-3"
                   style="background-color:#006316;">
                    Input 2 Tahun
                </a>

                <a href="{{ route('slipgaji.create.bulanan') }}"
                   class="btn btn-primary w-100"
                   style="background-color:#0d6efd;">
                    Input Bulanan
                </a>

            </div>

        </div>
    </div>
</div>


<style>
  tr.selected {
  background-color: #e8f5e9 !important;
}
  .btn-icon {
    width: 38px;
    height: 38px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.btn-icon i {
    font-size: 1.1rem;
}

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
