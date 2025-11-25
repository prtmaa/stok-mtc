<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index()
    {
        return view('divisi.index');
    }

    public function data()
    {
        $divisi = Divisi::orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($divisi)
            ->addIndexColumn()
            ->addColumn('aksi', function ($divisi) {
                if (in_array(auth()->user()->role, ['master', 'admin'])) {
                    return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('divisi.update', $divisi->id) . '`)" class="btn btn-sm btn-info btn-flat"><i class="fa fa-pen"></i></button>
                    <button type="button" onclick="deleteData(`' . route('divisi.destroy', $divisi->id) . '`)" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-trash"></i></button>
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
        $divisi = new Divisi();
        $divisi->nama = $request->nama;
        $divisi->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id)
    {
        $divisi = Divisi::find($id);

        return response()->json($divisi);
    }

    public function update(Request $request, $id)
    {
        $divisi = Divisi::find($id);
        $divisi->nama = $request->nama;
        $divisi->update();

        return response()->json('Data berhasil diubah', 200);
    }

    public function destroy($id)
    {
        $divisi = Divisi::find($id);
        $divisi->delete();

        return response()->json('Data berhasil dihapus', 200);
    }
}
