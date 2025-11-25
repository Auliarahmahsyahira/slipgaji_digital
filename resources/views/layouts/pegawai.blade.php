<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slip Gaji Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-warning bg-opacity-75">
  <header class="bg-warning py-3 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
      <img src="{{ asset('image/logokemanag1.png') }}" alt="Logo Kemenag" class="img-fluid" style="height:55px;">
      <div class="text-center flex-grow-1">
        <h1 class="h5 fw-bold mb-0 text-dark">SLIP GAJI DIGITAL</h1>
        <p class="mb-0 text-dark">KANWIL KEMENAG PROV. KEPRI</p>
      </div>
      <div>
         <a href="#" class="d-flex align-items-center text-decoration-none" id="dropdownProfile" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="{{ asset('image/avatar.png') }}" alt="Profile" class="rounded-circle border border-light" style="height:35px; width:35px;">
      </a>

       <ul class="dropdown-menu dropdown-menu-end shadow position-absolute" style="right:0; top:60px;" aria-labelledby="dropdownProfile">
    @if(Auth::check())
      <li class="dropdown-header text-center">
              <strong>{{ Auth::user()->pegawai->nama }}</strong><br>
              <small>NIP: {{ Auth::user()->pegawai->nip_pegawai }}</small><br>
              <small>Kdsatker: {{ Auth::user()->pegawai->kdsatker }}</small><br>
              <small>Jabatan: {{ Auth::user()->pegawai->jabatan }}</small><br>
              <small>Golongan: {{ Auth::user()->pegawai->nama_golongan }}/{{ Auth::user()->pegawai->golongan }}</small> 
            </li>
      @else
      <li class="dropdown-header text-center text-muted">
        Belum login
      </li>
    @endif
    <li><hr class="dropdown-divider"></li>
    <li><a href="#" class="dropdown-item text-danger"
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
    </div>
  </header>

  <main class="container py-5">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
