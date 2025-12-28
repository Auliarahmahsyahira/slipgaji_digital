<?php

namespace App\Http\Controllers;

use App\Models\Master;
use Illuminate\Http\Request;

class KomponenController extends Controller
{
    //
    public function index(Request $request)
    {
         $search = $request->input('search');
        $komponen = Master::when($search, function ($query, $search) {
            $query->where('nama_komponen', 'like', "%{$search}%");
        })->orderBy('created_at', 'desc')->get();

        return view('admin.rincian gaji.index', compact('komponen'));
    }

    public function create()
    {
        return view('admin.rincian gaji.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_komponen' => 'required|string|max:100',
            'tipe' => 'required|in:penghasilan,potongan',
            'kategori' => 'required|in:wajib,lainnya',
            'periode' => 'required|in:24,1',
        ]);

        Master::create([
            'nama_komponen' => $request->nama_komponen,
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'periode' => $request->periode,
        ]);

        return redirect()->route('komponen.index')->with('success', 'Komponen gaji berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $komponen = Master::findOrFail($id);
        return view('admin.rincian gaji.edit', compact('komponen'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_komponen' => 'required|string|max:100',
            'tipe' => 'required|in:penghasilan,potongan',
            'kategori' => 'required|in:wajib,lainnya',
            'periode' => 'required|in:24,1',
        ]);

        $komponen = Master::findOrFail($id);
        $komponen->update([
            'nama_komponen' => $request->nama_komponen,
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'periode' => $request->periode,
        ]);

        return redirect()->route('komponen.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $komponen = Master::findOrFail($id);
        $komponen->delete();

        return redirect()->route('komponen.index')->with('success', 'Data berhasil dihapus!');
    }
}