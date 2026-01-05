@extends('layouts.master')

@section('tittle')
    Data In Trading
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"> <a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Stok</li>
    <li class="breadcrumb-item active">In Trading</li>
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
                                            <button onclick="addForm('{{ route('in.store') }}')"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-plus-circle"></i> Tambah Data
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Form Export Excel -->
                                    <form action="{{ route('in.export') }}" method="GET"
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
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <th style="width: 20px;">No</th>
                                            <th>Tanggal</th>
                                            <th>Id</th>
                                            <th>Item id</th>
                                            <th>Item</th>
                                            <th>Supplier</th>
                                            <th>Jumlah</th>
                                            <th>Harga</th>
                                            <th>Harga Total</th>
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

        @include('in.form')
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
                        url: '{{ route('in.data') }}',
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
                            data: 'item_id'
                        },
                        {
                            data: 'item'
                        },
                        {
                            data: 'supplier_id'
                        },
                        {
                            data: 'jumlah'
                        },
                        {
                            data: 'harga'
                        },
                        {
                            data: 'total_harga'
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

                $('#item_id').next('.select2-container').find('.select2-selection')
                    .css('background-color', '')
                    .css('pointer-events', '')

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

                $('#item_id').next('.select2-container').find('.select2-selection')
                    .css('background-color', '#e9ecef')
                    .css('pointer-events', 'none')

                $.get(url)
                    .done((response) => {
                        console.log(response);
                        $('#modal-form [name=tanggal]').val(response.tanggal);
                        $('#modal-form [name=item_id]').val(response.item_id).trigger('change');
                        $('#modal-form [name=supplier_id]').val(response.supplier_id).trigger('change');
                        $('#modal-form [name=jumlah]').val(response.jumlah);
                        const formattedharga = formatEdit(response.harga);
                        const formattedtotal = formatEdit(response.total_harga);
                        $('#modal-form [name=harga]').val(formattedharga);
                        $('#modal-form [name=total_harga]').val(formattedtotal);
                        $('#modal-form [name=note]').val(response.note);
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
        </script>

        <script>
            function formatRibuanKoma(el) {
                let value = el.value;

                // hanya izinkan angka dan koma
                value = value.replace(/[^0-9,]/g, '');

                let parts = value.split(',');

                // format bagian ribuan
                let angka = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                if (parts.length > 1) {
                    el.value = angka + ',' + parts[1];
                } else {
                    el.value = angka;
                }
            }

            document.getElementById('harga').addEventListener('input', function() {
                formatRibuanKoma(this);
            });

            function formatEdit(value) {
                if (!value) return "";

                // ubah ke string
                value = value.toString();

                // ubah titik desimal SQL → koma
                value = value.replace('.', ',');

                // pecah angka
                let parts = value.split(',');

                // tambahkan titik ribuan
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                return parts.join(',');
            }

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
                // Aktifkan select2
                $('.select2bs4').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: 'Pilih item...',
                    allowClear: true
                });

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

            function showNote(note) {
                if (!note || note.trim() === "") {
                    note = "Tidak ada catatan.";
                }

                Swal.fire({
                    title: 'Catatan',
                    html: '<p style="text-align:left;">' + note + '</p>',
                    icon: 'info',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#3085d6',
                });
            }
        </script>
    @endpush
