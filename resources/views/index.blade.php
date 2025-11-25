@extends('layouts.master')

<title>Dashboard</title>

@section('content')
    <div class="container-fluid">

        <!-- Ringkasan -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 mb-3">

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

            <div class="col-12 col-sm-6 col-md-4 mb-3">

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

            <div class="col-12 col-sm-6 col-md-4 mb-3">

                <a href="{{ url('item') }}">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $totalItems }}</h3>
                            <p>Total Item Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Grafik Ringkasan Bulanan -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Ringkasan Stok per Bulan ({{ now()->year }})</h3>
            </div>
            <div class="card-body">
                <canvas id="stokSummaryChart" height="50"></canvas>
            </div>
        </div>

        <!-- Grafik Barang Masuk vs Keluar -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Barang Masuk dan Keluar ({{ now()->year }})</h3>
            </div>
            <div class="card-body">
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
@endsection
