@extends('layouts.master')

@section('tittle')
    Stockflow Bulan {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Stok</li>
    <li class="breadcrumb-item active">Stokflow</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <section class="col-lg-12 connectedSortable">
                <div class="card">

                    <div class="card-header">

                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <form action="{{ route('laporan.generate') }}" method="POST"
                                class="row g-2 align-items-center mb-0">
                                @csrf
                                <div class="col-auto">
                                    <label for="periode" class="col-form-label fw-semibold">Pilih Bulan:</label>
                                </div>
                                <div class="col-auto">
                                    <input type="text" id="periode" name="periode" class="form-control flatpickr-input"
                                        placeholder="Pilih Bulan" value="{{ $periode ?? '' }}" autocomplete="off"
                                        style="min-width:150px;">
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-rotate-right me-1"></i> Update
                                    </button>
                                </div>
                            </form>

                            <div class="col-auto">
                                <a href="{{ route('laporan.export', ['periode' => $periode, 'item_id' => $itemId]) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </a>
                            </div>
                        </div>

                    </div>

                    <div class="card-body table-responsive">

                        <style>
                            .table {
                                width: 100%;
                                border: 1px solid #dee2e6;
                                border-collapse: separate;
                                border-spacing: 0;
                                overflow-y: auto;
                            }

                            .table th,
                            .table td {
                                padding: 6px;
                                border: 1px solid #dee2e6;
                                white-space: nowrap;
                                vertical-align: middle !important;
                                text-align: center !important;
                            }

                            .table td.text-start,
                            .table th.text-start {
                                text-align: left !important;
                            }
                        </style>

                        <table id="stockflowTable" class="table table-bordered text-center">

                            <thead class="table-secondary">
                                <tr>
                                    <th rowspan="2" style="background-color:  #fdfdfd;">No</th>
                                    <th rowspan="2" style="background-color:  #fdfdfd;">Item</th>
                                    <th rowspan="2" style="background-color:  #fdfdfd;">Id</th>
                                    <th rowspan="2" style="background-color:  #fdfdfd;">Kategori</th>
                                    <th rowspan="2" style="background-color:  #fdfdfd;">UoM</th>

                                    <th colspan="3" style="background-color: #fff3cd;">Starting Balance</th>
                                    <th colspan="3" style="background-color: #cce5ff;">In Trading</th>
                                    <th colspan="3" style="background-color: #cce5ff;">Total Inbound</th>
                                    <th colspan="3" style="background-color: #f8d7da;">Out Used</th>
                                    <th colspan="3" style="background-color: #d4edda;">Ending Balance</th>
                                </tr>
                                <tr>
                                    {{-- Starting --}}
                                    <th style="background-color: #fff3cd;">Jumlah</th>
                                    <th style="background-color: #fff3cd;">Harga</th>
                                    <th style="background-color: #fff3cd;">Total</th>

                                    {{-- Trading --}}
                                    <th style="background-color: #cce5ff;">Jumlah</th>
                                    <th style="background-color: #cce5ff;">Harga</th>
                                    <th style="background-color: #cce5ff;">Total</th>

                                    {{-- Total Inbound --}}
                                    <th style="background-color: #cce5ff;">Jumlah</th>
                                    <th style="background-color: #cce5ff;">Harga</th>
                                    <th style="background-color: #cce5ff;">Total</th>

                                    {{-- Used --}}
                                    <th style="background-color: #f8d7da;">Jumlah</th>
                                    <th style="background-color: #f8d7da;">Harga</th>
                                    <th style="background-color: #f8d7da;">Total</th>

                                    {{-- Ending --}}
                                    <th style="background-color: #d4edda;">Jumlah</th>
                                    <th style="background-color: #d4edda;">Harga</th>
                                    <th style="background-color: #d4edda;">Total</th>
                                </tr>

                                <tr style="background-color:#f4f4f4; font-weight:normal;">
                                    <th colspan="5" style="background-color:  #fdfdfd;">Total Semua Item</th>

                                    <th style="background-color:  #fdfdfd;"> {{ $total_jml['starting'] }} </th>
                                    <th style="background-color:  #fdfdfd;">
                                        {{ formatRupiah($total_hrg['starting']) }} </th>
                                    <th style="background-color:  #fdfdfd;"> {{ formatRupiah($totals['starting']) }}
                                    </th>

                                    <th style="background-color:  #fdfdfd;"> {{ $total_jml['trading'] }} </th>
                                    <th style="background-color:  #fdfdfd;">
                                        {{ formatRupiah($total_hrg['trading']) }} </th>
                                    <th style="background-color:  #fdfdfd;"> {{ formatRupiah($totals['trading']) }}
                                    </th>

                                    <th style="background-color:  #fdfdfd;"> {{ $total_jml['inbound'] }} </th>
                                    <th style="background-color:  #fdfdfd;">
                                        {{ formatRupiah($total_hrg['inbound']) }} </th>
                                    <th style="background-color:  #fdfdfd;"> {{ formatRupiah($totals['inbound']) }}
                                    </th>

                                    <th style="background-color:  #fdfdfd;"> {{ $total_jml['used'] }} </th>
                                    <th style="background-color:  #fdfdfd;">
                                        {{ formatRupiah($total_hrg['used']) }} </th>
                                    <th style="background-color:  #fdfdfd;"> {{ formatRupiah($totals['used']) }}
                                    </th>

                                    <th style="background-color:  #fdfdfd;"> {{ $total_jml['ending'] }} </th>
                                    <th style="background-color:  #fdfdfd;">
                                        {{ formatRupiah($total_hrg['ending']) }} </th>
                                    <th style="background-color:  #fdfdfd;"> {{ formatRupiah($totals['ending']) }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($combined as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="text-start">{{ $row['nama'] }}</td>
                                        <td>{{ $row['kode'] }}</td>
                                        <td>{{ $row['kategori'] }}</td>
                                        <td>{{ $row['satuan'] }}</td>

                                        <td>{{ $row['starting_jumlah'] }}</td>
                                        <td>{{ number_format($row['starting_harga'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($row['starting_total'], 0, ',', '.') }}</td>

                                        <td>{{ $row['trading_jumlah'] }}</td>
                                        <td>{{ number_format($row['trading_harga'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($row['trading_total'], 0, ',', '.') }}</td>

                                        <td>{{ $row['inbound_jumlah'] }}</td>
                                        <td>{{ number_format($row['inbound_harga'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($row['inbound_total'], 0, ',', '.') }}</td>

                                        <td>{{ $row['used_jumlah'] }}</td>
                                        <td>{{ number_format($row['used_harga'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($row['used_total'], 0, ',', '.') }}</td>

                                        <td>{{ $row['ending_jumlah'] }}</td>
                                        <td>{{ number_format($row['ending_harga'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($row['ending_total'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('js')
    <script>
        flatpickr("#periode", {
            locale: "id",
            plugins: [new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y-m",
                altFormat: "F Y",
                theme: "light"
            })],
            altInput: true,
            defaultDate: "{{ $periode ?? '' }}",
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#stockflowTable').DataTable({
                paging: true,
                searching: true,
                ordering: false,
                scrollX: true,
                scrollY: "480px",
                scrollCollapse: true,
                fixedHeader: {
                    header: true
                },
                "language": {
                    "sProcessing": "Sedang memproses...",
                    "sLengthMenu": "Tampilkan _MENU_ entri",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "sSearch": "Pencarian:",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext": "Selanjutnya",
                        "sLast": "Terakhir"
                    },
                },
            });
        });
    </script>
@endpush
