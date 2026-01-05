<?php

namespace App\Http\Controllers;

use App\Models\BarangIn;
use App\Models\Item;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarangInController extends Controller
{
    public function index()
    {

        $item = Item::orderBy('nama')->get();
        $supplier = Supplier::orderBy('nama')->get();

        return view('in.index', compact('item', 'supplier'));
    }

    public function data()
    {
        $item = BarangIn::with('item', 'supplier')->orderBy('created_at', 'desc')
            ->get();

        return datatables()
            ->of($item)
            ->addColumn('tanggal', function ($item) {
                return formatTanggalIndo($item->tanggal);
            })
            ->addColumn('item_id', function ($item) {
                return $item->item->code ?? '-';
            })
            ->addColumn('item', function ($item) {
                return $item->item->nama ?? '-';
            })
            ->addColumn('harga', function ($item) {
                return formatRupiah($item->harga);
            })
            ->addColumn('total_harga', function ($item) {
                return formatRupiah($item->total_harga);
            })
            ->addColumn('supplier_id', function ($item) {
                return $item->supplier->nama ?? '-';
            })
            ->addIndexColumn()
            ->addColumn('aksi', function ($item) {
                $noteButton = '<button type="button" onclick="showNote(`' . $item->note . '`)" class="btn btn-sm btn-warning btn-flat"><i class="fa fa-sticky-note"></i></button>';

                if (in_array(auth()->user()->role, ['master', 'admin'])) {
                    return '
                        <div class="btn-group">
                            ' . $noteButton . '
                            <button type="button" onclick="editForm(`' . route('in.update', $item->id) . '`)" class="btn btn-sm btn-info btn-flat"><i class="fa fa-pen"></i></button>
                            <button type="button" onclick="deleteData(`' . route('in.destroy', $item->id) . '`)" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                        </div>';
                }

                return $noteButton;
            })
            ->rawColumns(['aksi', 'tanggal'])
            ->make(true);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $harga = str_replace(['.', ','], ['', '.'], $request->harga);
            $harga = (float) $harga;

            $totalHarga = $request->jumlah * $harga;

            $latest = BarangIn::latest('id')->first();
            $number = $latest ? ((int) substr($latest->code, -4)) + 1 : 1;
            $kode = 'IN-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            $barangIn = BarangIn::create([
                'tanggal' => $request->tanggal,
                'code' => $kode,
                'item_id' => $request->item_id,
                'supplier_id' => $request->supplier_id,
                'jumlah' => $request->jumlah,
                'harga' => $harga,
                'total_harga' => $totalHarga,
                'note' => $request->note,
            ]);

            $item = Item::find($request->item_id);
            $item->stok_akhir += $request->jumlah;
            $item->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data barang masuk berhasil disimpan dan stok diperbarui',
                'data' => $barangIn
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $in = BarangIn::select('id', 'tanggal', 'item_id', 'supplier_id', 'jumlah', 'harga', 'note', 'total_harga')
            ->findOrFail($id);

        return response()->json($in);
    }



    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $harga = str_replace(['.', ','], ['', '.'], $request->harga);
            $harga = (float) $harga;
            $barangIn = BarangIn::findOrFail($id);
            $item = Item::findOrFail($barangIn->item_id);

            $item->stok_akhir -= $barangIn->jumlah;
            $item->save();

            $totalHarga = $request->jumlah * $harga;

            $barangIn->update([
                'tanggal' => $request->tanggal,
                'supplier_id' => $request->supplier_id,
                'jumlah' => $request->jumlah,
                'harga' => $harga,
                'total_harga' => $totalHarga,
                'note' => $request->note,
            ]);

            $itemBaru = Item::findOrFail($request->item_id);
            $itemBaru->stok_akhir += $request->jumlah;
            $itemBaru->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data barang masuk berhasil diperbarui dan stok disesuaikan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $barangIn = BarangIn::findOrFail($id);

            $item = Item::findOrFail($barangIn->item_id);

            $item->stok_akhir -= $barangIn->jumlah;
            if ($item->stok_akhir < 0) {
                $item->stok_akhir = 0;
            }
            $item->save();

            $barangIn->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data barang masuk berhasil dihapus dan stok dikoreksi'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function exportExcel(Request $request)
    {
        // Validasi input bulan
        $request->validate([
            'bulan' => 'required|date_format:Y-m'
        ]);

        // Load template
        $template = storage_path('app/templates/templatein.xlsx');
        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();

        // Header judul laporan (sesuaikan sel template)
        $sheet->setCellValue(
            'A1',
            'Report In Trading Sparepart MTC ' . Carbon::parse($request->input('bulan'))->translatedFormat('F Y')
        );

        // Ambil bulan dan tahun
        $bulan = $request->bulan;
        $tahun = substr($bulan, 0, 4);
        $angkaBulan = substr($bulan, 5, 2);

        // Ambil data dari database sesuai bulan
        $data = BarangIn::with('item', 'supplier')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $angkaBulan)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Baris mulai input data
        $row = 4;

        foreach ($data as $index => $item) {

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", formatTanggalIndo($item->tanggal));
            $sheet->setCellValue("C{$row}", $item->code);
            $sheet->setCellValue("D{$row}", $item->supplier->nama ?? '-');
            $sheet->setCellValue("E{$row}", $item->item->code ?? '-');
            $sheet->setCellValue("F{$row}", $item->item->nama);
            $sheet->setCellValue("G{$row}", $item->item->satuan->nama);
            $sheet->setCellValue("H{$row}", $item->jumlah);
            $sheet->setCellValue("I{$row}", $item->harga);
            $sheet->setCellValue("J{$row}", $item->total_harga);
            $sheet->setCellValue("K{$row}", $item->note);

            $row++;
        }

        // Buat nama file export
        $filename = "Report_InTrading_" . now()->format('Ymd_His') . ".xlsx";
        $path = storage_path("app/temp/{$filename}");

        // Simpan ke storage
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        // Download & hapus setelah selesai
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
