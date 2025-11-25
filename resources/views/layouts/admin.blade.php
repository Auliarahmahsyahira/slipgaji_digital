<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slip Gaji Pegawai</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8c146;
    }
    header {
      background-color: #ffc107;
    }
    .navbar-menu {
      background-color: #f5b700;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      position: sticky;
    }
    .navbar-menu a {
      color: #000;
      font-weight: 500;
      padding: 10px 20px;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .navbar-menu a:hover {
      background-color: rgba(0,0,0,0.1);
      border-radius: 8px;
    }
    main {
      padding: 100px 50px;
      text-align: center;
    }
    .welcome {
      color: rgba(0,0,0,0.7);
      font-weight: 700;
      text-shadow: 1px 1px rgba(0,0,0,0.2);
    }
    .subtext {
      color: rgba(0,0,0,0.6);
      font-size: 18px;
    }
    nav {
    padding:5px;
    position:sticky;
    top:0;
    z-index: 2000; 
}
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="py-3 shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <img src="{{ asset('image/logokemanag.png') }}" alt="Logo Kemenag" class="img-fluid ms-3" style="height:55px;">
      <div class="text-center flex-grow-1">
        <h1 class="h5 fw-bold mb-0 text-dark">SLIP GAJI PEGAWAI</h1>
        <p class="mb-0 text-dark">KANWIL KEMENAG PROV. KEPRI</p>
      </div>
      <div class="me-3">
        <a href="#" class="d-flex align-items-center text-decoration-none" id="dropdownProfile" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="{{ asset('image/avatar.png') }}" alt="Profile" class="rounded-circle border border-light" style="height:35px; width:35px;">
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow position-absolute" style="right:0; top:60px; z-index:3000;" aria-labelledby="dropdownProfile">
          @if(Auth::check())
            <li class="dropdown-header text-center">
              <strong>{{ Auth::user()->pegawai->nama }}</strong><br>
              <small>NIP: {{ Auth::user()->pegawai->nip_pegawai }}</small><br>
              <small>Kdsatker: {{ Auth::user()->pegawai->kdsatker }}</small><br>
              <small>Jabatan: {{ Auth::user()->pegawai->jabatan }}</small><br>
              <small>Golongan: {{ Auth::user()->pegawai->nama_golongan }}/{{ Auth::user()->pegawai->golongan }}</small> 
            </li>
          @else
            <li class="dropdown-header text-center text-muted">Belum login</li>
          @endif
          <li><hr class="dropdown-divider"></li>
          <li>
            <a href="#" class="dropdown-item text-danger"
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </li>
        </ul>
      </div>
    </div>
  </header>

  <!-- SIDEBAR JADI NAVBAR BAWAH HEADER -->
  <nav class="navbar-menu d-flex justify-content-center py-2">
    <a href="{{ route('admin.dashboard') }}">🏠 Beranda</a>
    <a href="{{ route('pegawai.index') }}">👥 Pegawai</a>
    <a href="{{ route('slipgaji.index') }}">💰 Slip Gaji</a>
    <a href="{{ route('komponen.index') }}">🧾 Rincian Gaji</a>
  </nav>

  <main>
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</body>
</html>
