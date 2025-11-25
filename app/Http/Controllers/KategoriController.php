<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        return view('kategori.index');
    }

    public function data()
    {
        $kategori = Kategori::orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($kategori)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                if (in_array(auth()->user()->role, ['master', 'admin'])) {
                    return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('kategori.update', $kategori->id) . '`)" class="btn btn-sm btn-info btn-flat"><i class="fa fa-pen"></i></button>
                    <button type="button" onclick="deleteData(`' . route('kategori.destroy', $kategori->id) . '`)" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
                }

                return '-';
            })
            ->rawColumns(['aksi', 'tanggal'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $kategori = new Kategori();
        $kategori->nama = $request->nama;
        $kategori->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);

        return response()->json($kategori);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);
        $kategori->nama = $request->nama;
        $kategori->update();

        return response()->json('Data berhasil diubah', 200);
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);
        $kategori->delete();

        return response()->json('Data berhasil dihapus', 200);
    }
}
