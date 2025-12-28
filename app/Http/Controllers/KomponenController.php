<?php

namespace App\Http\Controllers;

use App\Models\Master;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKomponenRequest;
use App\Http\Requests\UpdateKomponenRequest;

class KomponenController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->search;

    $komponen = Master::when($search, function ($query, $search) {
      $query->where('nama_komponen', 'like', "%{$search}%");
    })
    ->orderBy('created_at', 'desc')
    ->get();

    return view('admin.rincian gaji.index', compact('komponen'));
  }

  public function create()
  {
    return view('admin.rincian gaji.create');
  }

  public function store(StoreKomponenRequest $request)
  {
    Master::create($request->validated());

    return redirect()
      ->route('komponen.index')
      ->with('success', 'Komponen gaji berhasil ditambahkan!');
  }

  public function edit($id)
  {
    $komponen = Master::findOrFail($id);
    return view('admin.rincian gaji.edit', compact('komponen'));
  }

  public function update(UpdateKomponenRequest $request, $id)
  {
    $komponen = Master::findOrFail($id);
    $komponen->update($request->validated());

    return redirect()
      ->route('komponen.index')
      ->with('success', 'Data berhasil diperbarui!');
  }

  public function destroy($id)
  {
    Master::findOrFail($id)->delete();

    return redirect()
      ->route('komponen.index')
      ->with('success', 'Data berhasil dihapus!');
  }
}
