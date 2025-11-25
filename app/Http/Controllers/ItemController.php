<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        $satuan = Satuan::all();

        return view('item.index', compact('kategori', 'satuan'));
    }

    public function data()
    {
        $item = Item::with(['kategori', 'satuan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return datatables()
            ->of($item)
            ->addIndexColumn()
            ->addColumn('kategori_id', function ($item) {
                return $item->kategori->nama ?? '-';
            })
            ->addColumn('satuan_id', function ($item) {
                return $item->satuan->nama ?? '-';
            })
            ->addColumn('status', function ($item) {
                return $item->stok_akhir < 1 ?
                    '<span class="badge badge-danger">Order</span>' :
                    '<span class="badge badge-success">Aman</span>';
            })
            ->addColumn('aksi', function ($item) {
                if (in_array(auth()->user()->role, ['master', 'admin'])) {
                    return '
            <div class="btn-group">
                <button type="button" onclick="editForm(`' . route('item.update', $item->id) . '`)" class="btn btn-sm btn-info btn-flat">
                    <i class="fa fa-pen"></i>
                </button>
                <button type="button" onclick="deleteData(`' . route('item.destroy', $item->id) . '`)" class="btn btn-sm btn-danger btn-flat">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            ';
                }

                return '-';
            })
            ->rawColumns(['aksi', 'status'])
            ->make(true);
    }



    public function store(Request $request)
    {

        $item = new Item();

        $latest = Item::latest('id')->first();
        $number = $latest ? ((int) substr($latest->code, -4)) + 1 : 1;
        $kode = 'ITM-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        $item->nama = $request->nama;
        $item->kategori_id = $request->kategori_id;
        $item->satuan_id = $request->satuan_id;
        $item->code = $kode;
        $item->stok_akhir = 0;

        $item->save();

        return response()->json('Data berhasil disimpan', 200);
    }


    public function show($id)
    {
        $item = Item::find($id);

        return response()->json($item);
    }


    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        $item->nama = $request->nama;
        $item->kategori_id = $request->kategori_id;
        $item->satuan_id = $request->satuan_id;

        $item->update();

        return response()->json('Data berhasil disimpan', 200);
    }


    public function destroy($id)
    {
        $item = Item::find($id);
        $item->delete();

        return response(null, 204);
    }

    public function getSatuan($id)
    {
        $item = Item::with('satuan:id,nama')
            ->select('id', 'satuan_id', 'stok_akhir')
            ->find($id);

        if (!$item) {
            return response()->json(['error' => 'Item tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $item->id,
            'satuan' => $item->satuan ? $item->satuan->nama : '-',
            'stok_akhir' => $item->stok_akhir,
        ]);
    }

    public function exportExcel(Request $request)
    {
        // Load template
        $template = storage_path('app/templates/templatereport.xlsx');

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();

        // Judul laporan
        \Carbon\Carbon::setLocale('id');
        $tanggalIndo = \Carbon\Carbon::now()->translatedFormat('d F Y');

        $sheet->setCellValue('B7', $tanggalIndo);

        // Ambil data
        $data = Item::with(['kategori', 'satuan'])
            ->orderBy('stok_akhir', 'desc')
            ->get();

        // Baris awal input data
        $row = 16;

        foreach ($data as $index => $item) {

            $status = $item->stok_akhir < 1 ? 'Order' : 'Aman';

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $item->nama);
            $sheet->setCellValue("C{$row}", $item->satuan->nama);
            $sheet->setCellValue("D{$row}", $item->stok_akhir);
            $sheet->setCellValue("E{$row}", '1');
            $sheet->setCellValue("F{$row}", $status);

            $style = $sheet->getStyle('F' . $row);

            if (strtoupper($status) == 'AMAN') {
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('C6EFCE'); // hijau
            } else if (strtoupper($status) == 'ORDER') {
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFC7CE'); // merah
            }


            $row++;
        }

        // Nama file
        $filename = "Report_Item_" . now()->format('Ymd_His') . ".xlsx";
        $path = storage_path("app/temp/{$filename}");

        // Simpan hasil
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
