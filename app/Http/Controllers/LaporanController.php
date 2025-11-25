<?php

namespace App\Http\Controllers;

use App\Models\BarangIn;
use App\Models\BarangOut;
use App\Models\EndingBalance;
use App\Models\Item;
use App\Models\StartingBalance;
use App\Models\Trading;
use App\Models\Used;
use App\Models\TotalInbound;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanController extends Controller
{


    // 1. PROCESS & GENERATE LAPORAN

    public function generateLaporanBulanan(Request $request)
    {
        $periodeInput = $request->input('periode'); // 2025-12
        $periode = Carbon::parse($periodeInput . '-01')->startOfMonth();
        $periodeSebelumnya = $periode->copy()->subMonth();

        $items = Item::all();

        // PRELOAD SEMUA DATA SEKALI
        $prevEnding = EndingBalance::whereMonth('periode', $periodeSebelumnya->month)
            ->whereYear('periode', $periodeSebelumnya->year)
            ->get()
            ->keyBy('item_id');

        $barangMasuk = BarangIn::whereMonth('tanggal', $periode->month)
            ->whereYear('tanggal', $periode->year)
            ->selectRaw('item_id, SUM(jumlah) as total_jumlah, SUM(jumlah * harga) as total_harga')
            ->groupBy('item_id')->get()->keyBy('item_id');

        $barangKeluar = BarangOut::whereMonth('tanggal', $periode->month)
            ->whereYear('tanggal', $periode->year)
            ->selectRaw('item_id, SUM(jumlah) as total_jumlah')
            ->groupBy('item_id')->get()->keyBy('item_id');

        DB::transaction(function () use ($items, $periode, $prevEnding, $barangMasuk, $barangKeluar) {

            foreach ($items as $item) {

                // == STARTING ==
                $prev = $prevEnding[$item->id] ?? null;
                $jumlahAwal = $prev->jumlah ?? 0;
                $hargaAwal = $prev->harga ?? 0;
                $totalHargaAwal = $prev->total_harga ?? 0;

                StartingBalance::updateOrCreate(
                    ['item_id' => $item->id, 'periode' => $periode],
                    [
                        'jumlah' => $jumlahAwal,
                        'harga' => $hargaAwal,
                        'total_harga' => $totalHargaAwal,
                    ]
                );

                // == TRADING ==
                $in = $barangMasuk[$item->id] ?? null;
                $jumlahMasuk = $in->total_jumlah ?? 0;
                $totalHargaMasuk = $in->total_harga ?? 0;
                $hargaMasuk = $jumlahMasuk > 0 ? $totalHargaMasuk / $jumlahMasuk : 0;

                Trading::updateOrCreate(
                    ['item_id' => $item->id, 'periode' => $periode],
                    [
                        'jumlah' => $jumlahMasuk,
                        'harga' => $hargaMasuk,
                        'total_harga' => $totalHargaMasuk,
                    ]
                );

                // == INBOUND (Average) ==
                $jumlahInbound = $jumlahAwal + $jumlahMasuk;
                $totalHargaInbound = $totalHargaAwal + $totalHargaMasuk;
                $hargaInbound = $jumlahInbound > 0 ? $totalHargaInbound / $jumlahInbound : 0;

                TotalInbound::updateOrCreate(
                    ['item_id' => $item->id, 'periode' => $periode],
                    [
                        'jumlah' => $jumlahInbound,
                        'harga' => $hargaInbound,
                        'total_harga' => $totalHargaInbound,
                    ]
                );

                // == USED ==
                $out = $barangKeluar[$item->id] ?? null;
                $jumlahKeluar = $out->total_jumlah ?? 0;

                $hargaRataUsed = $jumlahInbound > 0 ? $totalHargaInbound / $jumlahInbound : 0;
                $totalHargaKeluar = $jumlahKeluar * $hargaRataUsed;

                Used::updateOrCreate(
                    ['item_id' => $item->id, 'periode' => $periode],
                    [
                        'jumlah' => $jumlahKeluar,
                        'harga' => $hargaRataUsed,
                        'total_harga' => $totalHargaKeluar,
                    ]
                );

                // == ENDING ==
                $endingJumlah = $jumlahInbound - $jumlahKeluar;
                $endingTotalHarga = $totalHargaInbound - $totalHargaKeluar;
                $endingHarga = $endingJumlah > 0 ? $endingTotalHarga / $endingJumlah : 0;

                EndingBalance::updateOrCreate(
                    ['item_id' => $item->id, 'periode' => $periode],
                    [
                        'jumlah' => $endingJumlah,
                        'harga' => $endingHarga,
                        'total_harga' => $endingTotalHarga,
                    ]
                );

                // Update stok di tabel item
                $item->update(['stok_akhir' => $endingJumlah]);
            }
        });

        return redirect()
            ->route('laporan.index', ['periode' => $periode->format('Y-m')])
            ->with('success', "Laporan bulan {$periode->format('F Y')} berhasil dibuat.");
    }


    // 2. LOAD DATA LAPORAN (tanpa duplikat)

    private function fetchReport($periode, $itemId = null)
    {
        $periodeDate = Carbon::parse($periode . '-01');

        $items = Item::with(['kategori', 'satuan'])
            ->when($itemId, fn($q) => $q->where('id', $itemId))
            ->orderBy('nama')
            ->get();

        $starting = StartingBalance::whereMonth('periode', $periodeDate->month)
            ->whereYear('periode', $periodeDate->year)
            ->get()->keyBy('item_id');

        $trading = Trading::whereMonth('periode', $periodeDate->month)
            ->whereYear('periode', $periodeDate->year)
            ->get()->keyBy('item_id');

        $totalInbound = TotalInbound::whereMonth('periode', $periodeDate->month)
            ->whereYear('periode', $periodeDate->year)
            ->get()->keyBy('item_id');

        $used = Used::whereMonth('periode', $periodeDate->month)
            ->whereYear('periode', $periodeDate->year)
            ->get()->keyBy('item_id');

        $ending = EndingBalance::whereMonth('periode', $periodeDate->month)
            ->whereYear('periode', $periodeDate->year)
            ->get()->keyBy('item_id');

        return $items->map(function ($item) use ($starting, $trading, $totalInbound, $used, $ending) {

            $s = $starting[$item->id] ?? null;
            $t = $trading[$item->id] ?? null;
            $i = $totalInbound[$item->id] ?? null;
            $u = $used[$item->id] ?? null;
            $e = $ending[$item->id] ?? null;

            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'kategori' => $item->kategori->nama ?? '-',
                'satuan' => $item->satuan->nama ?? '-',
                'kode' => $item->code,

                'starting_jumlah' => $s->jumlah ?? 0,
                'starting_harga' => $s->harga ?? 0,
                'starting_total' => $s->total_harga ?? 0,

                'trading_jumlah' => $t->jumlah ?? 0,
                'trading_harga' => $t->harga ?? 0,
                'trading_total' => $t->total_harga ?? 0,

                'inbound_jumlah' => $i->jumlah ?? 0,
                'inbound_harga' => $i->harga ?? 0,
                'inbound_total' => $i->total_harga ?? 0,

                'used_jumlah' => $u->jumlah ?? 0,
                'used_harga' => $u->harga ?? 0,
                'used_total' => $u->total_harga ?? 0,

                'ending_jumlah' => $e->jumlah ?? 0,
                'ending_harga' => $e->harga ?? 0,
                'ending_total' => $e->total_harga ?? 0,
            ];
        });
    }


    // 3. TAMPILKAN LAPORAN

    public function index(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        $itemId = $request->input('item_id');

        $combined = $this->fetchReport($periode, $itemId);

        // Hitung total
        $totals = [
            'starting' => $combined->sum('starting_total'),
            'trading' => $combined->sum('trading_total'),
            'inbound' => $combined->sum('inbound_total'),
            'used' => $combined->sum('used_total'),
            'ending' => $combined->sum('ending_total'),
        ];

        $total_jml = [
            'starting' => $combined->sum('starting_jumlah'),
            'trading' => $combined->sum('trading_jumlah'),
            'inbound' => $combined->sum('inbound_jumlah'),
            'used' => $combined->sum('used_jumlah'),
            'ending' => $combined->sum('ending_jumlah'),
        ];

        $total_hrg = [
            'starting' => $combined->sum('starting_harga'),
            'trading' => $combined->sum('trading_harga'),
            'inbound' => $combined->sum('inbound_harga'),
            'used' => $combined->sum('used_harga'),
            'ending' => $combined->sum('ending_harga'),
        ];

        $items = Item::orderBy('nama')->get();

        return view('laporan.index', compact(
            'combined',
            'periode',
            'items',
            'itemId',
            'totals',
            'total_jml',
            'total_hrg'
        ));
    }


    // 4. EXPORT EXCEL

    public function exportExcel(Request $request)
    {
        $template = storage_path('app/templates/template.xlsx');

        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue(
            'A1',
            'Stokflow Sparepart MTC ' . Carbon::parse($request->input('periode'))->translatedFormat('F Y')
        );

        $data = $this->fetchReport($request->input('periode'), $request->input('item_id'));

        $row = 6;
        foreach ($data as $index => $item) {

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $item['nama']);
            $sheet->setCellValue("C{$row}", $item['kode']);
            $sheet->setCellValue("D{$row}", $item['kategori']);
            $sheet->setCellValue("E{$row}", $item['satuan']);

            $sheet->setCellValue("F{$row}", $item['starting_jumlah']);
            $sheet->setCellValue("G{$row}", $item['starting_harga']);
            $sheet->setCellValue("H{$row}", $item['starting_total']);

            $sheet->setCellValue("I{$row}", $item['trading_jumlah']);
            $sheet->setCellValue("J{$row}", $item['trading_harga']);
            $sheet->setCellValue("K{$row}", $item['trading_total']);

            $sheet->setCellValue("L{$row}", $item['inbound_jumlah']);
            $sheet->setCellValue("M{$row}", $item['inbound_harga']);
            $sheet->setCellValue("N{$row}", $item['inbound_total']);

            $sheet->setCellValue("O{$row}", $item['used_jumlah']);
            $sheet->setCellValue("P{$row}", $item['used_harga']);
            $sheet->setCellValue("Q{$row}", $item['used_total']);

            $sheet->setCellValue("R{$row}", $item['ending_jumlah']);
            $sheet->setCellValue("S{$row}", $item['ending_harga']);
            $sheet->setCellValue("T{$row}", $item['ending_total']);

            $row++;
        }

        $filename = "Report_Stockflow_" . now()->format('Ymd_His') . ".xlsx";
        $path = storage_path("app/temp/{$filename}");

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
