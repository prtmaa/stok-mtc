<?php

namespace App\Http\Controllers;

use App\Models\BarangIn;
use App\Models\BarangOut;
use App\Models\Divisi;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class BarangOutController extends Controller
{
    public function index()
    {

        $item = Item::whereNotNull('stok_akhir')
            ->where('stok_akhir', '>', 0)
            ->orderBy('nama')
            ->get();

        $divisi = Divisi::orderBy('nama')->get();

        return view('out.index', compact('item', 'divisi'));
    }

    public function data()
    {
        $item = BarangOut::with('item.satuan', 'divisi')->orderBy('created_at', 'desc')
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
            ->addColumn('divisi_id', function ($item) {
                return $item->divisi->nama ?? '-';
            })
            ->addColumn('harga', function ($item) {
                return formatRupiah($item->harga);
            })
            ->addColumn('total_harga', function ($item) {
                return formatRupiah($item->total_harga);
            })
            ->addColumn('jumlah', function ($item) {
                return $item->jumlah . ' ' . ($item->item->satuan->nama ?? '-');
            })
            ->addIndexColumn()
            ->addColumn('aksi', function ($item) {
                $noteButton = '<button type="button" onclick="showNote(`' . $item->note . '`)" class="btn btn-sm btn-warning btn-flat"><i class="fa fa-sticky-note"></i></button>';

                if (in_array(auth()->user()->role, ['master', 'admin'])) {
                    return '
                        <div class="btn-group">
                            ' . $noteButton . '
                            <button type="button" onclick="editForm(`' . route('out.update', $item->id) . '`)" class="btn btn-sm btn-info btn-flat"><i class="fa fa-pen"></i></button>
                            <button type="button" onclick="deleteData(`' . route('out.destroy', $item->id) . '`)" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                        </div>';
                }

                return $noteButton;
            })
            ->rawColumns(['aksi', 'tanggal'])
            ->make(true);
    }

    public function getHargaRataRata($item_id)
    {
        $data = BarangIn::where('item_id', $item_id)
            ->selectRaw('
            SUM(jumlah) as total_jumlah,
            SUM(total_harga) as total_nilai
        ')
            ->first();

        if (!$data || $data->total_jumlah == 0) {
            return response()->json([
                'harga' => 0
            ]);
        }

        return response()->json([
            'harga' => round($data->total_nilai / $data->total_jumlah, 2)
        ]);
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $item = Item::findOrFail($request->item_id);

            if ($item->stok_akhir < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi!'
                ], 422);
            }

            // 🔹 Hitung harga rata-rata barang masuk
            $barangIn = BarangIn::where('item_id', $request->item_id)
                ->selectRaw('
                SUM(jumlah) as total_jumlah,
                SUM(total_harga) as total_nilai
            ')
                ->first();

            if (!$barangIn || $barangIn->total_jumlah == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harga barang belum tersedia'
                ], 422);
            }

            $harga = $barangIn->total_nilai / $barangIn->total_jumlah;
            $totalHarga = $harga * $request->jumlah;

            // 🔹 Generate kode
            $latest = BarangOut::latest('id')->first();
            $number = $latest ? ((int) substr($latest->code, -4)) + 1 : 1;
            $kode = 'OUT-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            // 🔹 Simpan barang keluar
            $barangOut = BarangOut::create([
                'tanggal' => $request->tanggal,
                'code' => $kode,
                'item_id' => $request->item_id,
                'divisi_id' => $request->divisi_id,
                'jumlah' => $request->jumlah,
                'harga' => round($harga, 2),
                'total_harga' => round($totalHarga, 2),
                'note' => $request->note,
            ]);

            // 🔹 Update stok
            $item->stok_akhir -= $request->jumlah;
            $item->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang keluar berhasil disimpan (harga rata-rata)',
                'data' => $barangOut
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $data = BarangOut::with(['item.satuan'])->findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $barangOut = BarangOut::findOrFail($id);
            $item = Item::findOrFail($barangOut->item_id);

            // 🔹 selisih jumlah (baru - lama)
            $selisih = $request->jumlah - $barangOut->jumlah;

            // 🔹 simulasi stok akhir
            $stokBaru = $item->stok_akhir - $selisih;

            if ($stokBaru < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi untuk perubahan ini!'
                ], 422);
            }

            // 🔹 update stok
            $item->stok_akhir = $stokBaru;
            $item->save();

            // 🔹 hitung ulang total harga (harga lama)
            $totalHarga = $barangOut->harga * $request->jumlah;

            // 🔹 update barang out
            $barangOut->update([
                'tanggal'     => $request->tanggal,
                'divisi_id'   => $request->divisi_id,
                'jumlah'      => $request->jumlah,
                'total_harga' => $totalHarga,
                'note'        => $request->note,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data barang keluar berhasil diperbarui dan stok disesuaikan'
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
            $barangOut = BarangOut::findOrFail($id);

            // Ambil item terkait
            $item = Item::findOrFail($barangOut->item_id);

            // Kembalikan stok
            $item->stok_akhir += $barangOut->jumlah;
            $item->save();

            // Hapus transaksi barang masuk
            $barangOut->delete();

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

    public function getItems(Request $request)
    {
        $term = $request->term; // ambil keyword dari Select2

        $query = Item::query()
            ->where('stok_akhir', '>', 0);

        // Jika user mengetik sesuatu, filter berdasarkan nama atau kode
        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('nama', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%");
            });
        }

        $items = $query->orderBy('nama')
            ->limit(20) // batasi hasil biar ringan
            ->get(['id', 'code', 'nama']);

        return response()->json($items);
    }

    public function exportExcel(Request $request)
    {
        // Validasi input bulan
        $request->validate([
            'bulan' => 'required|date_format:Y-m'
        ]);

        // Load template
        $template = storage_path('app/templates/templateout.xlsx');
        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();

        // Header judul laporan (sesuaikan sel template)
        $sheet->setCellValue(
            'A1',
            'Report Out Used Sparepart MTC ' . Carbon::parse($request->input('bulan'))->translatedFormat('F Y')
        );

        // Ambil bulan dan tahun
        $bulan = $request->bulan;
        $tahun = substr($bulan, 0, 4);
        $angkaBulan = substr($bulan, 5, 2);

        // Ambil data dari database sesuai bulan
        $data = BarangOut::with('item', 'divisi')
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
            $sheet->setCellValue("D{$row}", $item->divisi->nama ?? '-');
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
        $filename = "Report_OutUsed_" . now()->format('Ymd_His') . ".xlsx";
        $path = storage_path("app/temp/{$filename}");

        // Simpan ke storage
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        // Download & hapus setelah selesai
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
