@extends('layouts.pegawai')

@section('content')
<div class="container py-4" style="font-family: 'Times New Roman', Times, serif; max-width: 800px;">

  <h4 class="text-center fw-bold mb-4 text-dark">Slip Gaji</h4>

  @if ($gaji)
    {{-- CARD 1 --}}
    <div class="card shadow-sm mb-3 p-3" style="font-size: 0.9rem;">
      <div class="mb-3">
        <p class="mb-1"><strong>Pembayaran:</strong> Gaji Induk Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $gaji->periode)->translatedFormat('F Y') }}</p>
        <p class="mb-1"><strong>Pegawai:</strong> {{ $gaji->pegawai->nama ?? '-' }} ({{ $gaji->pegawai->nip_pegawai ?? '-' }})</p>
      </div>

      <div class="row g-2">
        {{-- PENGHASILAN --}}
        <div class="col-md-6">
          <div class="card border-0 shadow-sm p-2" style="font-size: 0.85rem;">
            <div class="card-body p-2">
              <h6 class="fw-bold text-center mb-2">PENGHASILAN</h6>
              <table class="table table-sm table-bordered mb-0">
                <tbody>
                  @php
                    $penghasilan = [
                      'Gaji Pokok' => $gaji->gaji_pokok ?? 0,
                      'Tun. Istri/Suami' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Istri/Suami')->first()->nominal ?? 0,
                      'Tun. Anak' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Anak')->first()->nominal ?? 0,
                      'Tun. Umum' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Umum')->first()->nominal ?? 0,
                      'Tun. Ta. Umum' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Ta. Umum')->first()->nominal ?? 0,
                      'Tun. Papua' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Papua')->first()->nominal ?? 0,
                      'Tun. Terpencil' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Terpencil')->first()->nominal ?? 0,
                      'Tun. Struktur' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Struktur')->first()->nominal ?? 0,
                      'Tun. Fungsi' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Fungsi')->first()->nominal ?? 0,
                      'Tun. Lain' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Lain')->first()->nominal ?? 0,
                      'Tun. Bulat' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Bulat')->first()->nominal ?? 0,
                      'Tun. Beras' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Beras')->first()->nominal ?? 0,
                      'Tun. Pajak' => $gaji->penghasilan->where('master.nama_komponen', 'Tun. Pajak')->first()->nominal ?? 0,
                    ];
                  @endphp

                  @foreach ($penghasilan as $nama => $nominal)
                    <tr>
                      <td>{{ $nama }}</td>
                      <td class="text-end">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                    </tr>
                  @endforeach

                  <tr class="table-light fw-bold">
                    <td>Jumlah Kotor</td>
                    <td class="text-end">Rp {{ number_format($gaji->jumlah_kotor, 0, ',', '.') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- POTONGAN --}}
        <div class="col-md-6">
          <div class="card border-0 shadow-sm p-2" style="font-size: 0.85rem;">
            <div class="card-body p-2">
              <h6 class="fw-bold text-center mb-2">POTONGAN</h6>
              <table class="table table-sm table-bordered mb-0">
                <tbody>
                  @php
                    $potongan = [
                      'Pot. Beras' => $gaji->potongan->where('master.nama_komponen', 'Pot. Beras')->first()->nominal ?? 0,
                      'IWP' => $gaji->potongan->where('master.nama_komponen', 'IWP')->first()->nominal ?? 0,
                      'BPJS' => $gaji->potongan->where('master.nama_komponen', 'BPJS')->first()->nominal ?? 0,
                      'BPJS Lain' => $gaji->potongan->where('master.nama_komponen', 'BPJS Lain')->first()->nominal ?? 0,
                      'Pot. Pph' => $gaji->potongan->where('master.nama_komponen', 'Pot. Pph')->first()->nominal ?? 0,
                      'Sewa Rumah' => $gaji->potongan->where('master.nama_komponen', 'Sewa Rumah')->first()->nominal ?? 0,
                      'Tunggakan' => $gaji->potongan->where('master.nama_komponen', 'Tunggakan')->first()->nominal ?? 0,
                      'Utang' => $gaji->potongan->where('master.nama_komponen', 'Utang')->first()->nominal ?? 0,
                      'Pot. Lain' => $gaji->potongan->where('master.nama_komponen', 'Pot. Lain')->first()->nominal ?? 0,
                      'Taperum' => $gaji->potongan->where('master.nama_komponen', 'Taperum')->first()->nominal ?? 0,
                    ];
                  @endphp

                  @foreach ($potongan as $nama => $nominal)
                    <tr>
                      <td>{{ $nama }}</td>
                      <td class="text-end">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                    </tr>
                  @endforeach

                  <tr class="table-light fw-bold">
                    <td>Jumlah Potongan</td>
                    <td class="text-end">Rp {{ number_format($gaji->total_potongan, 0, ',', '.') }}</td>
                  </tr>
                  <tr class="table-secondary fw-bold">
                    <td>Gaji Bersih</td>
                    <td class="text-end">Rp {{ number_format($gaji->jumlah_bersih, 0, ',', '.') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- CARD 2 --}}
<div class="card shadow-sm p-3" style="max-width: 400px; margin: 0 auto; font-size: 14px;">
  <div class="mb-3">
    <p class="mb-1"><strong>Pembayaran Gaji Bulan:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m', $gaji->periode)->translatedFormat('F Y') }}</p>
    <p class="mb-1"><strong>Nama Pegawai:</strong> {{ $gaji->pegawai->nama ?? '-' }}</p>
    <p class="mb-1"><strong>No. Rekening BSI:</strong> {{ $gaji->pegawai->No_rekening ?? '-' }}</p>
  </div>

  <table class="table table-sm table-bordered" style="margin: 0; border-collapse: collapse;">
    <tbody>
      <tr class="table-secondary fw-bold">
                    <td>Gaji Bersih</td>
                    <td class="text-end">Rp {{ number_format($gaji->jumlah_bersih, 0, ',', '.') }}</td>
                  </tr>
                    @php
                    $potongan = [
                      'Bank Riau' => $gaji->potongan->where('master.nama_komponen', 'Bank Riau')->first()->nominal ?? 0,
                      'Bank Syariah Riau' => $gaji->potongan->where('master.nama_komponen', 'Bank Syariah Riau')->first()->nominal ?? 0,
                      'Bank BTN' => $gaji->potongan->where('master.nama_komponen', 'Bank BTN')->first()->nominal ?? 0,
                      'Koperasi Kanwil' => $gaji->potongan->where('master.nama_komponen', 'Koperasi Kanwil')->first()->nominal ?? 0,
                      'Koperasi Bintan' => $gaji->potongan->where('master.nama_komponen', 'Koperasi Bintan')->first()->nominal ?? 0,
                      'Zakat' => $gaji->potongan->where('master.nama_komponen', 'Zakat')->first()->nominal ?? 0,
                      'Infak' => $gaji->potongan->where('master.nama_komponen', 'Infak')->first()->nominal ?? 0,
                      'Donatur Masjid' => $gaji->potongan->where('master.nama_komponen', 'Donatur Masjid')->first()->nominal ?? 0,
                      'BPR Bintan' => $gaji->potongan->where('master.nama_komponen', 'BPR Bintan')->first()->nominal ?? 0,
                      'Qurban' => $gaji->potongan->where('master.nama_komponen', 'Qurban')->first()->nominal ?? 0,
                      'Wakaf Uang' => $gaji->potongan->where('master.nama_komponen', 'Wakaf Uang')->first()->nominal ?? 0,
                      'Dharma Wanita' => $gaji->potongan->where('master.nama_komponen', 'Dharma Wanita')->first()->nominal ?? 0,
                      'Dansos' => $gaji->potongan->where('master.nama_komponen', 'Dansos')->first()->nominal ?? 0,
                      'BPJS 1%' => $gaji->potongan->where('master.nama_komponen', 'BPJS 1%')->first()->nominal ?? 0,
                      'Lain-Lain' => $gaji->potongan->where('master.nama_komponen', 'Lain-lain')->first()->nominal ?? 0,
                    ];
                  @endphp

                  @foreach ($potongan as $nama => $nominal)
                    <tr>
                      <td>{{ $nama }}</td>
                      <td class="text-end">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                    </tr>
                  @endforeach
      <tr class="table-light fw-bold">
        <td style="padding: 4px 6px;">Total Potongan</td>
        <td class="text-end" style="padding: 4px 6px;">Rp {{ number_format($gaji->total_potongan, 0, ',', '.') }}</td>
      </tr>
      <tr class="table-secondary fw-bold">
        <td style="padding: 4px 6px;">Gaji Diterima</td>
        <td class="text-end" style="padding: 4px 6px;">Rp {{ number_format($gaji->gaji_diterima, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>
</div>


  @else
    <div class="alert alert-warning text-center">
      Data gaji untuk periode ini belum tersedia.
    </div>
  @endif
</div>

<a href="{{ route('pegawai.slip.pdf', $gaji->id_gaji) }}" class="btn btn-primary">
  Cetak PDF
</a>
@endsection
