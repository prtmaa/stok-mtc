@extends('layouts.master')

<title>Dashboard</title>

@section('content')
    <div class="container-fluid">

        <!-- Ringkasan -->
        <div class="row">
            <div class="col-6 col-sm-6 col-md-6 mb-3">

                <a href="{{ url('in') }}">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalBarangIn }}</h3>
                            <p>Barang Masuk Bulan Ini</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-sm-6 col-md-6 mb-3">

                <a href="{{ url('out') }}">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $totalBarangOut }}</h3>
                            <p>Barang Keluar Bulan Ini</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <!-- Ringkasan Total Harga Bulanan -->
        <div class="row">
            <div class="col-6 col-sm-6 col-md-2 mb-3">
                <a href="{{ url('item') }}">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h5>{{ $totalItems }}
                            </h5>
                            <p>Total Item</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-sm-6 col-md-2 mb-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h5>Rp {{ number_format($chartData['datasets'][0]['data'][$now->month - 1] ?? 0, 0, ',', '.') }}
                        </h5>
                        <p>Starting Balance</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-6 col-md-2 mb-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h5>Rp {{ number_format($chartData['datasets'][1]['data'][$now->month - 1] ?? 0, 0, ',', '.') }}
                        </h5>
                        <p>In Trading</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-6 col-md-2 mb-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h5>Rp {{ number_format($chartData['datasets'][2]['data'][$now->month - 1] ?? 0, 0, ',', '.') }}
                        </h5>
                        <p>Total Inbound</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-6 col-md-2 mb-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h5>Rp {{ number_format($chartData['datasets'][3]['data'][$now->month - 1] ?? 0, 0, ',', '.') }}
                        </h5>
                        <p>Out Used</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-6 col-md-2 mb-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h5>Rp {{ number_format($chartData['datasets'][4]['data'][$now->month - 1] ?? 0, 0, ',', '.') }}
                        </h5>
                        <p>Ending Balance</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6 col-sm-6 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pemakaian Item Perdivisi ({{ now()->year }})</h3>
                    </div>
                    <div class="card-body" style="height:300px">
                        <canvas id="pieChart" height="50"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-6 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Item Fast Moving</h3>
                    </div>

                    <div class="card-body" style="height:300px">
                        <table id="stokTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Item</th>
                                    <th style="text-align: center;">Stok Akhir</th>
                                    <th style="text-align: center;">Min Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order as $i => $item)
                                    <tr class="">
                                        <td>{{ $item->nama }}</td>
                                        <td style="text-align: center;">{{ $item->stok_akhir }}</td>
                                        <td style="text-align: center;">{{ $item->min }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Ringkasan Bulanan -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Stokflow Perbulan ({{ now()->year }})</h3>
            </div>
            <div class="card-body" style="height:250px;">
                <canvas id="stokSummaryChart" height="50"></canvas>
            </div>
        </div>

        <!-- Grafik Barang Masuk vs Keluar -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Barang Masuk dan Keluar ({{ now()->year }})</h3>
            </div>
            <div class="card-body" style="height:250px;">
                <canvas id="stokChart" height="50"></canvas>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('stokChart');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyData->pluck('month')) !!},
                datasets: [{
                        label: 'Barang Masuk',
                        data: {!! json_encode($monthlyData->pluck('barang_in')) !!},
                        borderColor: 'rgba(54, 162, 235, 0.9)',
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Barang Keluar',
                        data: {!! json_encode($monthlyData->pluck('barang_out')) !!},
                        borderColor: 'rgba(255, 99, 132, 0.9)',
                        backgroundColor: 'rgba(255, 99, 132, 0.4)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });


        // === Grafik Ringkasan Stok Bulanan ===
        const ctxSummary = document.getElementById('stokSummaryChart');

        new Chart(ctxSummary, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: {!! json_encode($chartData['datasets']) !!}
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let val = context.raw || 0;
                                return context.dataset.label + ': Rp ' + val.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const ctxDivisi = document.getElementById('pieChart').getContext('2d');

            new Chart(ctxDivisi, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($labeldivisi) !!},
                    datasets: [{
                        data: {!! json_encode($totaldivisi) !!},
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(144, 238, 144, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                        ]
                    }]

                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

        });

        $(function() {
            $("#stokTable").DataTable({
                responsive: true,
                autoWidth: false,
                scrollY: "200px",
                searching: false,
                info: false,
                scrollCollapse: true,
                paging: false,
                responsive: true,

            });
        });
    </script>
@endsection
