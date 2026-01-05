@extends('layouts.master')

@section('tittle')
    Data Item
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"> <a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Item</li>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">

            <section class="col-lg-12 connectedSortable">

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">

                                <div class="d-flex flex-wrap justify-content-between align-items-center">
                                    @if (in_array(auth()->user()->role, ['master', 'admin']))
                                        <div class="btn-group">
                                            <button onclick="addForm('{{ route('item.store') }}')"
                                                class="btn btn-primary btn-sm"><i class="fa fa-plus-circle"></i> Tambah
                                                Data</button>
                                        </div>
                                    @endif

                                    <a href="{{ route('item.exportExcel') }}" class="btn btn-success btn-sm">
                                        <i class="fa fa-file-excel"></i> Excel
                                    </a>

                                </div>

                            </div>

                            <style>
                                .table th {
                                    vertical-align: middle !important;
                                    text-align: center !important;
                                }
                            </style>

                            <div class="card-body table-responsive">
                                <form action="" class="form-produk" method="post">
                                    @csrf
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <th style="width: 20px;">No</th>
                                            <th>Item Id</th>
                                            <th>Item</th>
                                            <th>Kategori</th>
                                            <th>UoM</th>
                                            <th>Stok</th>
                                            <th>Min Stok</th>
                                            <th>Status</th>
                                            <th style="width: 150px;">Aksi</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </form>
                            </div>

                        </div>

                    </div>

                </div>

            </section>
        </div>

        @include('item.form')
    @endsection

    @push('js')
        <script>
            let table;
            $(function() {
                table = $('.table').DataTable({
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    responsive: true,
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
                    ajax: {
                        url: '{{ route('item.data') }}',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false
                        },
                        {
                            data: 'code'
                        },
                        {
                            data: 'nama'
                        },
                        {
                            data: 'kategori_id'
                        },
                        {
                            data: 'satuan_id'
                        },
                        {
                            data: 'stok_akhir'
                        },
                        {
                            data: 'min'
                        },
                        {
                            data: 'status'
                        },

                        {
                            data: 'aksi',
                            "searchable": false,
                            "orderable": false
                        },
                    ]
                });



                $('#modal-form').validator().on('submit', function(e) {
                    if (!e.preventDefault()) {
                        $.ajax({
                                enctype: 'multipart/form-data',
                                url: $('#modal-form form').attr('action'),
                                type: $('#modal-form form').attr('method'),
                                data: new FormData($('#modal-form form')[0]),
                                async: false,
                                processData: false,
                                contentType: false
                            })
                            .done((response) => {
                                $('#modal-form').modal('hide');
                                table.ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data berhasil disimpan',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            })
                            .fail((errors) => {
                                Swal.fire({
                                    icon: 'warning',
                                    confirmButtonColor: '#3085d6',
                                    iconColor: '#dc3545',
                                    title: 'Gagal',
                                    text: 'Data gagal disimpan',
                                })
                            });
                    }
                })

            });

            function addForm(url) {
                $('#modal-form').modal({
                    backdrop: 'static',
                    keyboard: false
                }).modal('show');
                $('#modal-form .modal-title').text('Tambah Data');

                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('post');
                $('#modal-form [name=item]').focus();
            }

            function editForm(url) {
                $('#modal-form').modal({
                    backdrop: 'static',
                    keyboard: false
                }).modal('show');
                $('#modal-form .modal-title').text('Edit Data');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('put');
                $('#modal-form [name=nama]').focus();

                $.get(url)
                    .done((response) => {
                        $('#modal-form [name=nama]').val(response.nama);
                        $('#modal-form [name=kategori_id]').val(response.kategori_id);
                        $('#modal-form [name=satuan_id]').val(response.satuan_id);
                        $('#modal-form [name=min]').val(response.min);
                    })
                    .fail((errors) => {
                        Swal.fire({
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            iconColor: '#dc3545',
                            title: 'Gagal',
                            text: 'Data gagal ditampilkan',
                        })
                    });
            }

            function deleteData(url) {
                Swal.fire({
                    title: 'Yakin?',
                    text: "Data akan dihapus",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                                '_token': $('[name=csrf-token]').attr('content'),
                                '_method': 'delete'
                            })
                            .done((response) => {
                                table.ajax.reload();
                                $('.alertdelete').fadeIn();

                                setTimeout(() => {
                                    $('.alertdelete').fadeOut();
                                }, 3000);
                            })
                            .fail((errors) => {
                                Swal.fire({
                                    icon: 'warning',
                                    confirmButtonColor: '#3085d6',
                                    iconColor: '#dc3545',
                                    title: 'Gagal',
                                    text: 'Data gagal dihapus',
                                })
                            });
                    }
                })
            }
        </script>
    @endpush
