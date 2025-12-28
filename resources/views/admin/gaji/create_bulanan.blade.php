  @extends('layouts.admin')

  @section('content')

    <div class="text-center mb-5">
      <h4 class="fw-bold text-dark text-shadow-sm">Input Slip Gaji Bulanan Pegawai</h4>
    </div>

    <form action="{{ route('slipgaji.store.bulanan') }}" method="POST">
      @csrf

      {{-- IDENTITAS PEGAWAI --}}
      <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
        <div class="card-header fw-semibold bg-white border-0 text-secondary">
          Identitas Pegawai
        </div>
        <div class="card-body bg-white rounded">
          <div class="row mb-3">
            <div class="col-md-3 fw-semibold">Periode</div>
        <div class="col-md-9">
            <select name="periode" class="form-control border-0 border-bottom bg-light-subtle" required>
                <option value="">-- Pilih Bulan --</option>
                <option value="Januari">Januari</option>
                <option value="Febuari">Februari</option>
                <option value="Maret">Maret</option>
                <option value="April">April</option>
                <option value="Maret">Mei</option>
                <option value="Juni">Juni</option>
                <option value="Juli">Juli</option>
                <option value="Agustus">Agustus</option>
                <option value="September">September</option>
                <option value="Oktober">Oktober</option>
                <option value="November">November</option>
                <option value="Desember">Desember</option>
            </select>
        </div>
    </div>
        <div class="row mb-3">
    <div class="col-md-3 fw-semibold">NIP</div>
    <div class="col-md-9">
      <input type="text" name="nip_pegawai" id="nip_pegawai" class="form-control border-0 border-bottom bg-light-subtle" placeholder="Masukkan NIP" required>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3 fw-semibold">Nama</div>
    <div class="col-md-9">
      <input type="text" name="nama" id="nama" class="form-control border-0 border-bottom bg-light-subtle" placeholder="Masukkan Nama" readonly required>
    </div>
  </div>
  </div>
      </div>

      {{-- POTONGAN LAINNYA --}}
      <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
        <div class="card-header fw-semibold bg-white border-0 text-secondary">
          Potongan Lainnya
        </div>
        <div class="card-body bg-white rounded">
          {{-- Gaji Bersih --}}
          <div class="row mb-3">
            <div class="col-md-3 fw-semibold text-success">Gaji Bersih</div>
            <div class="col-md-9">
              <input type="text" id="jumlah_bersih" readonly class="form-control border-0 border-bottom bg-light-subtle text-end">
            </div>
          </div>

          @foreach($komponen as $item)
            @if($item->tipe == 'potongan' && $item->kategori == 'lainnya')
              <div class="row mb-3">
                <div class="col-md-3 fw-semibold">{{ $item->nama_komponen }}</div>
                <div class="col-md-9">
                  <div class="input-group">
                    <input type="number" step="any" class="form-control border-0 border-bottom bg-light-subtle text-end hitung-total"
                          name="nominal[{{ $item->id_komponen }}]" placeholder="0">
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
              <input type="text" id="total_potongan" readonly class="form-control border-0 border-bottom bg-light-subtle text-end">
            </div>
          </div>

          {{-- Gaji Diterima --}}
          <div class="row mt-3">
            <div class="col-md-3 fw-semibold text-primary">Gaji Diterima</div>
            <div class="col-md-9">
              <input type="text" id="gaji_diterima" readonly class="form-control border-0 border-bottom bg-light-subtle text-end">
            </div>
          </div>
        </div>
      </div>

      {{-- TOMBOL SIMPAN --}}
      <div class="text-center mt-5 mb-4">
        <a href="{{ route('slipgaji.index') }}" class="btn btn-dark px-5 py-2 fw-semibold shadow-sm">Batal</a>
        <button type="submit" class="btn btn-dark px-5 py-2 fw-semibold shadow-sm">Simpan</button>
      </div>
    </form>

    <script>
// === Cek NIP & Ambil Gaji Bersih Wajib ===
document.getElementById('nip_pegawai').addEventListener('change', function () {

    const nip = this.value.trim();
    const namaInput = document.getElementById('nama');

    if (nip === '') return;

    // 1) Ambil nama pegawai
    fetch(`{{ url('/cek-nip') }}/${nip}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                namaInput.value = data.nama;
            } else {
                namaInput.value = '';
                alert(data.message);
            }
        });

    // 2) Ambil jumlah bersih dari gaji wajib
    fetch(`/cek-gaji/${nip}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('jumlah_bersih').value = '';
                document.getElementById('jumlah_bersih_raw').value = 0;
                alert(data.message);
                return;
            }

            // SIMPAN nilai angka murni ke hidden input
            document.getElementById('jumlah_bersih_raw').value = data.jumlah_bersih;

            // TAMPILKAN ke user dalam bentuk Rupiah
            document.getElementById('jumlah_bersih').value = formatRupiah(data.jumlah_bersih);

            hitungGaji();
        })
        .catch(error => console.error(error));
});

// Format Rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(angka);
}

// Hitung potongan & gaji diterima
function hitungGaji() {

    const gajiBersih = Number(document.getElementById('jumlah_bersih_raw').value) || 0;

    let potonganLain = 0;
    document.querySelectorAll('.hitung-total').forEach(el => {
        potonganLain += Number(el.value) || 0;
    });

    document.getElementById('total_potongan').value = formatRupiah(potonganLain);

    const gajiDiterima = gajiBersih - potonganLain;
    document.getElementById('gaji_diterima').value = formatRupiah(gajiDiterima);
}

// Event input
document.addEventListener('input', e => {
    if (e.target.classList.contains('hitung-total')) {
        hitungGaji();
    }
});
</script>

<!-- Tambahkan hidden input untuk angka murni -->
<input type="hidden" id="jumlah_bersih_raw" name="jumlah_bersih_raw" value="0">
;

  @endsection
