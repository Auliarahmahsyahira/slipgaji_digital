<html>
<head>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 5px; }
    h2 { text-align: center; }
  </style>
</head>
<body>

  <h2>Slip Gaji</h2>

  <p>Pembayaran: Gaji Induk Bulan {{ $gaji->periode}}</p>
  <p>Pegawai: {{ $gaji->pegawai->nama }}</p>

  <br>

  {{-- tabel penghasilan --}}
  <table>
    <tr>
      <th>PENGHASILAN</th>
      <th>Jumlah</th>
    </tr>
    @foreach ($gaji->penghasilan as $p)
      <tr>
        <td>{{ $p->nama }}</td>
        <td>Rp {{ number_format($p->jumlah,0,',','.') }}</td>
      </tr>
    @endforeach
  </table>

  <br>

  {{-- tabel potongan --}}
  <table>
    <tr>
      <th>POTONGAN</th>
      <th>Jumlah</th>
    </tr>
    @foreach ($gaji->potongan as $pt)
      <tr>
        <td>{{ $pt->nama }}</td>
        <td>Rp {{ number_format($pt->jumlah,0,',','.') }}</td>
      </tr>
    @endforeach
  </table>

</body>
</html>
