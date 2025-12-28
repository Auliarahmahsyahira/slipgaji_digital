  @extends('layouts.admin')

  @section('content')

    <div class="text-center mb-5">
      <h4 class="fw-bold text-dark text-shadow-sm">Input Slip Gaji Pegawai</h4>
    </div>

    <form action="{{ route('slipgaji.store.duatahun') }}" method="POST">
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
              <input type="text" name="periode" class="form-control border-0 border-bottom bg-light-subtle" placeholder="Misal 2024-2025" required>
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

      {{-- PENGHASILAN --}}
      <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
        <div class="card-header fw-semibold bg-white border-0 text-secondary">
          Penghasilan
        </div>
        <div class="card-body bg-white rounded">

          {{-- Gaji Pokok --}}
          <div class="row mb-3">
            <div class="col-md-3 fw-semibold">Gaji Pokok</div>
            <div class="col-md-9">
              <div class="input-group">
                <input type="number" name="gaji_pokok" class="form-control border-0 border-bottom bg-light-subtle text-end hitung-kotor" placeholder="0" step="any">
                <span class="input-group-text bg-light-subtle border-0 border-bottom">Rp</span>
              </div>
            </div>
          </div>

          {{-- Tunjangan dan penghasilan lainnya --}}
          @foreach($komponen as $item)
            @if($item->tipe == 'penghasilan' && $item->kategori != 'gaji_pokok')
              <div class="row mb-3">
                <div class="col-md-3 fw-semibold">{{ $item->nama_komponen }}</div>
                <div class="col-md-9">
                  <div class="input-group">
                    <input type="number" step="any" class="form-control border-0 border-bottom bg-light-subtle text-end hitung-kotor" 
                          name="nominal[{{ $item->id_komponen }}]" placeholder="0">
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
              <input type="text" id="jumlah_kotor" readonly class="form-control border-0 border-bottom bg-light-subtle text-end">
            </div>
          </div>
        </div>
      </div>

      {{-- POTONGAN WAJIB --}}
      <div class="card shadow-sm mb-4 border-0 w-75 mx-auto bg-body-tertiary">
        <div class="card-header fw-semibold bg-white border-0 text-secondary">
          Potongan
        </div>
        <div class="card-body bg-white rounded">
          @foreach($komponen as $item)
            @if($item->tipe == 'potongan' && $item->kategori == 'wajib')
              <div class="row mb-3">
                <div class="col-md-3 fw-semibold">{{ $item->nama_komponen }}</div>
                <div class="col-md-9">
                  <div class="input-group">
                    <input type="number" step="any" class="form-control border-0 border-bottom bg-light-subtle text-end hitung-potongan"
                          name="nominal[{{ $item->id_komponen }}]" placeholder="0">
                    <span class="input-group-text bg-light-subtle border-0 border-bottom">Rp</span>
                  </div>
                </div>
              </div>
            @endif
          @endforeach

          <div class="row mt-4">
            {{-- Jumlah Potongan --}}
            <div class="col-md-3 fw-semibold text-danger">Jumlah Potongan</div>
            <div class="col-md-9">
              <input type="text" id="jumlah_potongan" readonly class="form-control border-0 border-bottom bg-light-subtle text-end">
            </div>
            {{-- Gaji Bersih --}}
            <div class="col-md-3 fw-semibold text-success">Gaji Bersih</div>
            <div class="col-md-9">
              <input type="text" id="jumlah_bersih" readonly class="form-control border-0 border-bottom bg-light-subtle text-end">
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

    {{-- JS OTOMATIS HITUNG --}}
    <script>
    // === Cek NIP Otomatis ===
    document.getElementById('nip_pegawai').addEventListener('change', function() {
      const nip = this.value.trim();
      const namaInput = document.getElementById('nama');

      if (nip !== '') {
        fetch(`{{ url('/cek-nip') }}/${nip}`)  
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              namaInput.value = data.nama;
            } else {
              namaInput.value = '';
              alert(data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      }
    });
      function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
      }

      function hitungGaji() {
        let totalKotor = 0;
        document.querySelectorAll('.hitung-kotor').forEach(el => {
          totalKotor += Number(el.value) || 0;
        });
        document.getElementById('jumlah_kotor').value = formatRupiah(totalKotor);

        let totalPotongan = 0;
        document.querySelectorAll('.hitung-potongan').forEach(el => {
          totalPotongan += Number(el.value) || 0;
        });
        document.getElementById('jumlah_potongan').value = formatRupiah(totalPotongan);

        const gajiBersih = totalKotor - totalPotongan;
        document.getElementById('jumlah_bersih').value = formatRupiah(gajiBersih);
      }

      document.addEventListener('input', e => {
        if (e.target.classList.contains('hitung-kotor') || e.target.classList.contains('hitung-potongan') || e.target.classList.contains('hitung-total')) {
          hitungGaji();
        }
      });
    </script>

  @endsection
