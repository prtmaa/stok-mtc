<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satuan;

class SatuanController extends Controller
{
    public function index()
    {
        return view('uom.index');
    }

    public function data()
    {
        $uom = Satuan::orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($uom)
            ->addIndexColumn()
            ->addColumn('aksi', function ($uom) {
                if (in_array(auth()->user()->role, ['master', 'admin'])) {
                    return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('uom.update', $uom->id) . '`)" class="btn btn-sm btn-info btn-flat"><i class="fa fa-pen"></i></button>
                    <button type="button" onclick="deleteData(`' . route('uom.destroy', $uom->id) . '`)" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-trash"></i></button>
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
        $uom = new Satuan();
        $uom->nama = $request->nama;
        $uom->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id)
    {
        $uom = Satuan::find($id);

        return response()->json($uom);
    }

    public function update(Request $request, $id)
    {
        $uom = Satuan::find($id);
        $uom->nama = $request->nama;
        $uom->update();

        return response()->json('Data berhasil diubah', 200);
    }

    public function destroy($id)
    {
        $uom = Satuan::find($id);
        $uom->delete();

        return response()->json('Data berhasil dihapus', 200);
    }
}
