@extends('layouts.master')

@section('tittle')
    Data Out Used
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"> <a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Stok</li>
    <li class="breadcrumb-item active">Out Used</li>
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
                                            <button onclick="addForm('{{ route('out.store') }}')"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-plus-circle"></i> Tambah Data
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Form Export Excel -->
                                    <form action="{{ route('out.export') }}" method="GET"
                                        class="d-flex align-items-center gap-2">

                                        <div class="input-group input-group-sm" style="max-width: 170px;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            <input type="text" id="periode" name="bulan"
                                                class="form-control form-control-sm" placeholder="Pilih Bulan"
                                                autocomplete="off" required>
                                        </div>

                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa fa-file-excel"></i> Excel
                                        </button>
                                    </form>


                                </div>
                                <!-- Tombol Tambah Data -->

                            </div>

                            <div class="card-body table-responsive">
                                <form action="" class="form-produk" method="post">
                                    @csrf
                                    <table class="table text-center table-bordered">
                                        <thead>
                                            <th style="width: 20px;">No</th>
                                            <th>Tanggal</th>
                                            <th>Id</th>
                                            <th>Divisi</th>
                                            <th>Item id</th>
                                            <th>Item</th>
                                            <th>Jumlah</th>
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

        @include('out.form')
    @endsection

    @push('js')
        <script>
            let table;
            $(function() {
                table = $('.table').DataTable({
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    autoWidth: false,
                    responsive: true,
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
                    ajax: {
                        url: '{{ route('out.data') }}',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false
                        },
                        {
                            data: 'tanggal'
                        },
                        {
                            data: 'code'
                        },
                        {
                            data: 'divisi_id'
                        },
                        {
                            data: 'item_id'
                        },
                        {
                            data: 'item'
                        },
                        {
                            data: 'jumlah'
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
                                    icon: 'error',
                                    title: 'Oops...',
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

                // tampilkan select2, sembunyikan input readonly
                $('#item_nama').hide();
                $('#item_id').show();

                // inisialisasi select2
                $('#item_id').select2({
                    theme: 'bootstrap4',
                    dropdownParent: $('#modal-form'),
                    placeholder: 'Pilih Item...',
                    allowClear: true,
                    ajax: {
                        url: '{{ route('get.items') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term // kirim kata kunci ke controller
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(item => ({
                                    id: item.id,
                                    text: item.code + ' - ' + item.nama
                                }))
                            };
                        }
                    },
                    language: {
                        noResults: function() {
                            return "Tidak ada item tersedia";
                        }
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                }).val(null).trigger('change');

                $(document).ready(function() {
                    // Saat pilihan berubah
                    $('#item_id').on('change', function() {
                        const itemId = $(this).val();

                        if (itemId) {
                            $.ajax({
                                url: '/get-satuan/' + itemId,
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    console.log('Hasil dari server:', data);
                                    // Update label satuan
                                    $('#satuan-text').text(data.satuan ?? '');
                                    $('#stokakhir-input').val(data.stok_akhir ?? 0);
                                },
                                error: function(xhr) {
                                    console.log('Error ambil satuan:', xhr.responseText);
                                }
                            });
                        } else {
                            $('#satuan-text').text('');
                            $('#stokakhir-input').val('');
                        }
                    });
                });

                $.fn.select2.defaults.set("language", {
                    noResults: function() {
                        return "Tidak ada hasil ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    },
                    inputTooShort: function() {
                        return "Ketikkan minimal 1 huruf";
                    }
                });
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

                // sembunyikan select2, tampilkan input readonly
                $('#item_id').hide();
                $('#item_id').next('.select2-container').hide();
                $('#item_nama').show();

                $.get(url)
                    .done(response => {
                        // isi data form
                        $('#modal-form [name=tanggal]').val(response.tanggal);
                        $('#modal-form [name=divisi_id]').val(response.divisi_id);
                        $('#modal-form [name=jumlah]').val(response.jumlah);
                        $('#modal-form [name=note]').val(response.note);

                        // isi item readonly
                        $('#item_id').val(response.item_id);
                        $('#item_nama').val(response.item.code + ' - ' + response.item.nama);
                        $('#satuan-text').text(response.item?.satuan?.nama ?? '');
                        $('#stokakhir-input').val(response.item.stok_akhir ?? '');
                    })
                    .fail(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Data gagal ditampilkan',
                        });
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
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data gagal dihapus',
                                })
                            });
                    }
                })
            }

            flatpickr(".tanggal", {
                dateFormat: "Y-m-d",
                defaultDate: "today",
                locale: "id",
                onReady: function(selectedDates, dateStr, instance) {
                    instance.input.style.backgroundColor = "#fff";
                    instance.input.style.color = "#000";
                    instance.input.style.border = "1px solid #ced4da";
                }
            });

            const bs = document.getElementById('harga');

            bs.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, ''); // Hanya angka
                if (value) {
                    // Format angka dengan titik ribuan
                    value = new Intl.NumberFormat('id-ID').format(value);
                    this.value = value;
                } else {
                    this.value = '';
                }
            });

            $('#modal-form').on('hidden.bs.modal', function() {
                // Reset semua input biasa
                $(this).find('form')[0].reset();

                // Reset select2 dengan benar tanpa menghapus option
                const $select = $('#item_id');
                $select.val(null).trigger('change'); // kosongkan pilihan
            });
        </script>

        <script>
            $(document).ready(function() {

                // Saat pilihan berubah
                $('#item_id').on('change', function() {
                    const itemId = $(this).val();

                    if (itemId) {
                        $.ajax({
                            url: '/get-satuan/' + itemId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                console.log('Hasil dari server:', data);
                                // Update label satuan
                                $('#satuan-text').text(data.satuan ?? '');
                            },
                            error: function(xhr) {
                                console.log('Error ambil satuan:', xhr.responseText);
                            }
                        });
                    } else {
                        $('#satuan-text').text('');
                    }
                });
            });

            $.fn.select2.defaults.set("language", {
                noResults: function() {
                    return "Tidak ada hasil ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                },
                inputTooShort: function() {
                    return "Ketikkan minimal 1 huruf";
                }
            });
        </script>
        <script>
            flatpickr("#periode", {
                locale: "id",
                plugins: [new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "Y-m",
                    altFormat: "F Y",
                    theme: "light"
                })],

            });
        </script>
    @endpush
