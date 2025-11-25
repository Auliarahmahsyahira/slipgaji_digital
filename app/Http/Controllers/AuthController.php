<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gaji;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class AuthController extends Controller
{
    public function showLoginNip()
{
    return view('auth.login');
}

public function checkNip(Request $request)
{
    $request->validate([
        'nip_pegawai' => 'required'
    ]);

    $user = User::where('nip_pegawai', $request->nip_pegawai)->first();

    if (!$user) {
        return back()->withErrors(['nip_pegawai' => 'NIP tidak ditemukan.']);
    }

    // Jika ditemukan, arahkan ke halaman password
    return redirect()->route('login.password', ['nip' => $request->nip_pegawai]);
}

public function showLoginPassword($nip)
{
    return view('auth.loginII', compact('nip'));
}

public function login(Request $request)
{
    $request->validate([
        'nip_pegawai' => 'required',
        'password' => 'required',
    ]);

    if (Auth::attempt(['nip_pegawai' => $request->nip_pegawai, 'password' => $request->password])) {
        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'pegawai') {
            return redirect()->route('pegawai.dashboard');
        }
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
        ->orderBy('created_at', 'desc') // ambil slip gaji terbaru berdasarkan waktu input
        ->first();

    return view('pegawai.dashboard', compact('gaji'));
    }

    public function admin () {
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
            ->header('Content-Disposition', 'attachment; filename="slip-gaji.pdf"');
    }

}
