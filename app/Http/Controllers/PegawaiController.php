<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use App\Imports\PegawaiImport;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\Http\Requests\ImportPegawaiRequest;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->search;

    $pegawai = Pegawai::when($search, function ($query, $search) {
      $query->where('nip_pegawai', 'like', "%{$search}%")
            ->orWhere('nama', 'like', "%{$search}%");
    })->get();

    return view('admin.pegawai.index', compact('pegawai'));
  }

  public function create()
  {
    return view('admin.pegawai.create');
  }

  public function store(StorePegawaiRequest $request)
  {
    $pegawai = Pegawai::create($request->except('password'));

    User::create([
      'nip_pegawai' => $pegawai->nip_pegawai,
      'password'    => Hash::make($request->password),
      'role'        => 'pegawai',
    ]);

    return redirect()
      ->route('pegawai.index')
      ->with('success', 'Data pegawai berhasil ditambahkan!');
  }

  public function edit($id)
  {
    $pegawai = Pegawai::findOrFail($id);
    return view('admin.pegawai.edit', compact('pegawai'));
  }

  public function update(UpdatePegawaiRequest $request, $nip)
  {
    $pegawai = Pegawai::with('user')->findOrFail($nip);

    $pegawai->update($request->except('password'));

    if ($request->filled('password') && $pegawai->user) {
      $pegawai->user->update([
        'password' => Hash::make($request->password),
      ]);
    }

    return redirect()
      ->route('pegawai.index')
      ->with('success', 'Data pegawai berhasil diperbarui.');
  }

  public function destroy($id)
  {
    Pegawai::findOrFail($id)->delete();

    return redirect()
      ->route('pegawai.index')
      ->with('success', 'Data berhasil dihapus!');
  }

  public function import(ImportPegawaiRequest $request)
  {
    Excel::import(new PegawaiImport, $request->file('file'));

    return redirect()
      ->route('pegawai.index')
      ->with('success', 'Data pegawai berhasil diimport!');
  }

  public function data()
  {
    return DataTables::of(Pegawai::query())
      ->addIndexColumn()
      ->addColumn('aksi', function ($row) {
        return '
          <a href="'.route('pegawai.edit', $row->nip_pegawai).'" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil-square"></i>
          </a>
          <form action="'.route('pegawai.destroy', $row->nip_pegawai).'" method="POST" style="display:inline">
            '.csrf_field().method_field('DELETE').'
            <button class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin hapus data ini?\')">
              <i class="bi bi-trash"></i>
            </button>
          </form>
        ';
      })
      ->addColumn('checkbox', fn () => '<input type="checkbox" class="checkItem">')
      ->rawColumns(['aksi', 'checkbox'])
      ->make(true);
  }
}
