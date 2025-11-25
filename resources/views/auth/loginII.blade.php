<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slip Gaji Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4c842; /* warna kuning */
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background-color: #10594d; /* hijau tua */
      border-radius: 50px;
      padding: 40px 50px;
      text-align: center;
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
      width: 350px;
    }

    .login-box h2 {
      color: white;
      font-size: 16px;
      font-weight: 750;
      margin-bottom: 30px;
      line-height: 1.4;
    }

    .form-control {
      border-radius: 20px;
      text-align: center;
      background: transparent;
      color: white;
      border: 1px solid white;
    }

    .form-control::placeholder {
      color: #cfcfcf;
    }

    .btn-login {
      background-color: #f4c842;
      color: black;
      font-weight: 600;
      border-radius: 15px;
      padding: 8px 30px;
      border: none;
      transition: 0.2s;
    }

    .btn-login:hover {
      background-color: #f9d95e;
    }

    .logo-kemenag {
      width: 70px;
      height: 70px;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <img src="{{ asset('image/logokemanag.png') }}" alt="Logo Kemenag" class="logo-kemenag">
    <h2>SLIP GAJI PEGAWAI<br>KANWIL KEMENAG PROV. KEPRI</h2>

    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <input type="hidden" name="nip_pegawai" value="{{ $nip }}">
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Masukkan Password">
        @error('password')
          <div class="text-danger mt-1 small">{{ $message }}</div>
        @enderror
        </div>
      <button type="submit" class="btn btn-login">Masuk</button>
    </form>
  </div>
</body>
</html>
