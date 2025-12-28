<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gaji;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\CheckNipRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
  public function showLoginNip()
  {
    return view('auth.login');
  }

  public function checkNip(CheckNipRequest $request)
  {
    $user = User::where('nip_pegawai', $request->nip_pegawai)->first();

    if (!$user) {
      return back()->withErrors([
        'nip_pegawai' => 'NIP tidak ditemukan.'
      ]);
    }

    return redirect()
      ->route('login.password', ['nip' => $request->nip_pegawai])
      ->with('success', 'NIP ditemukan');
  }

  public function showLoginPassword($nip)
  {
    return view('auth.loginII', compact('nip'));
  }

  public function login(LoginRequest $request)
  {
    if (Auth::attempt($request->only('nip_pegawai', 'password'))) {
      $request->session()->regenerate();
      $user = Auth::user();

      return $user->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('pegawai.dashboard');
    }

    return back()->withErrors([
      'password' => 'Password salah.'
    ]);
  }

  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login.nip');
  }

  public function pegawai()
  {
    $pegawai = Auth::user()->pegawai;

    $gaji = Gaji::with(['pegawai', 'penghasilan.master', 'potongan.master'])
      ->where('nip_pegawai', $pegawai->nip_pegawai)
      ->latest()
      ->first();

    return view('pegawai.dashboard', compact('gaji'));
  }

  public function admin()
  {
    return view('admin.dashboard');
  }

  public function cetakPdf($id)
  {
    $gaji = Gaji::with(['penghasilan', 'potongan', 'pegawai'])
      ->findOrFail($id);

    $pdf = Pdf::loadView('pegawai.slippdf', compact('gaji'))
      ->setPaper('A4', 'portrait');

    return response($pdf->output(), 200)
      ->header('Content-Type', 'application/pdf')
      ->header(
        'Content-Disposition',
        'attachment; filename="slip-gaji.pdf"'
      );
  }
}
