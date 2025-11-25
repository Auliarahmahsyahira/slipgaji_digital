<?php

    namespace App\Http\Controllers;

    use App\Models\Pegawai;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;

    class PegawaiController extends Controller
    {
        public function index(Request $request)
        {
            $search = $request->input('search');
            $pegawai = Pegawai::when($search, function ($query, $search) {
            $query->where('nip_pegawai', 'like', "%{$search}%")
              ->orWhere('nama', 'like', "%{$search}%");
        })->get();

            return view('admin.pegawai.index', compact('pegawai'));
        }

        // Tampilkan form input pegawai baru
        public function create()
        {
            return view('admin.pegawai.create');
        }

        // Simpan data pegawai baru ke database
        public function store(Request $request)
        {
            $request->validate([
                'nip_pegawai' => 'required|unique:pegawai,nip_pegawai',
                'nama' => 'required|string|max:100',
                'kdsatker' => 'required|string|max:50',
                'jabatan' => 'required|string|max:100',
                'golongan' => 'required|string|max:50',
                'nama_golongan' => 'required|string|max:100',
                'no_rekening' => 'required|string|max:50',
                'password' => 'required|confirmed|min:6',
            ]);

            // Simpan data pegawai
            $pegawai = Pegawai::create([
                'nip_pegawai' => $request->nip_pegawai,
                'nama' => $request->nama,
                'kdsatker' => $request->kdsatker,
                'jabatan' => $request->jabatan,
                'golongan' => $request->golongan,
                'nama_golongan' => $request->nama_golongan,
                'no_rekening' => $request->no_rekening,
            ]);

            // Simpan data user (akun login)
            User::create([
            'nip_pegawai' => $pegawai->nip_pegawai,
            'password' => Hash::make($request->password),
            'role' => 'pegawai', // default role
            ]);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan!');

        }

        public function edit($id)
        {
            $pegawai = Pegawai::findOrFail($id);
            return view('admin.pegawai.edit', compact('pegawai'));
        }

        public function update(Request $request, $nip)
        {
        $pegawai = Pegawai::with('user')->findOrFail($nip);

        $request->validate([
            'nama'        => 'required|string|max:100',
            'kdsatker'     => 'required|string|max:50',
            'jabatan'     => 'required|string|max:100',
            'golongan'    => 'required|string|max:20',
            'nama_golongan'  => 'required|string|max:150',
            'no_rekening' => 'required|string|max:50',
            'password'    => 'nullable|confirmed|min:6',
        ]);

        // Update data pegawai
        $pegawai->update([
            'nama'        => $request->nama,
            'kdsatker'     => $request->kdsatker,
            'jabatan'     => $request->jabatan,
            'golongan'    => $request->golongan,
            'nama_golongan'  => $request->nama_golongan,
            'no_rekening' => $request->no_rekening,
        ]);

        // Update password user jika diisi
        if ($request->filled('password') && $pegawai->user) {
            $pegawai->user->update([
                'password' => Hash::make($request->password),
            ]);
        }

            return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
        }

        public function destroy ($id) {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

            return redirect()->route('pegawai.index')->with('success', 'Data berhasil dihapus!');
        }
    }
    
