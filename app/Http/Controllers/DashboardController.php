<?php

namespace App\Http\Controllers;

use App\Models\BarangIn;
use App\Models\BarangOut;
use App\Models\EndingBalance;
use App\Models\Item;
use App\Models\StartingBalance;
use App\Models\TotalInbound;
use App\Models\Trading;
use App\Models\Used;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $months = collect(range(1, 12));
        $labels = [];
        $startingData = [];
        $tradingData = [];
        $inboundData = [];
        $usedData = [];
        $endingData = [];

        foreach ($months as $m) {
            $labels[] = Carbon::create()->month($m)->format('M');

            $startingData[] = StartingBalance::whereMonth('periode', $m)
                ->whereYear('periode', $now->year)
                ->sum('total_harga');

            $tradingData[] = Trading::whereMonth('periode', $m)
                ->whereYear('periode', $now->year)
                ->sum('total_harga');

            $inboundData[] = TotalInbound::whereMonth('periode', $m)
                ->whereYear('periode', $now->year)
                ->sum('total_harga');

            $usedData[] = Used::whereMonth('periode', $m)
                ->whereYear('periode', $now->year)
                ->sum('total_harga');

            $endingData[] = EndingBalance::whereMonth('periode', $m)
                ->whereYear('periode', $now->year)
                ->sum('total_harga');
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Starting Balance',
                    'data' => $startingData,
                    'backgroundColor' => 'rgba(255, 206, 86, 0.7)', // kuning muda
                ],
                [
                    'label' => 'Trading',
                    'data' => $tradingData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)', // biru muda
                ],
                [
                    'label' => 'Total Inbound',
                    'data' => $inboundData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.7)', // biru muda
                ],
                [
                    'label' => 'Used',
                    'data' => $usedData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.7)', // merah muda
                ],
                [
                    'label' => 'Ending Balance',
                    'data' => $endingData,
                    'backgroundColor' => 'rgba(144, 238, 144, 0.7)', // hijau muda
                ],
            ]
        ];


        $totalItems = Item::count();
        $totalBarangIn = BarangIn::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->sum('jumlah');
        $totalBarangOut = BarangOut::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->sum('jumlah');

        // Data grafik masuk vs keluar
        $monthlyData = collect(range(1, 12))->map(function ($month) use ($now) {
            $barangIn = BarangIn::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $now->year)
                ->sum('jumlah');

            $barangOut = BarangOut::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $now->year)
                ->sum('jumlah');

            return [
                'month' => Carbon::create()->month($month)->format('M'),
                'barang_in' => $barangIn,
                'barang_out' => $barangOut,
            ];
        });

        return view('index', [
            'totalItems' => $totalItems,
            'chartData' => $chartData,
            'periode' => $now->format('F Y'),
            'totalBarangIn' => $totalBarangIn,
            'totalBarangOut' => $totalBarangOut,
            'monthlyData' => $monthlyData,
        ]);
    }
}
