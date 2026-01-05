    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="modal-form" style="overflow:hidden;" role="dialog"
        aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">

            <form action="" method="post" enctype="multipart/form-data" data-toggle="validator"
                class="form-horizontal">
                @csrf
                @method('post')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group row">
                            <label for="tanggal" class="col-md-4 col-md-offset-1 control-label">Tanggal</label>
                            <div class="col-md-8">
                                <input type="text" name="tanggal" id="tanggal" class="form-control tanggal"
                                    required autofocus oninvalid="this.setCustomValidity('Silahkan pilih tanggal')"
                                    oninput="this.setCustomValidity('')">
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="item_id" class="col-md-4 col-md-offset-1 control-label">Item</label>
                            <div class="col-md-8">
                                <select name="item_id" id="item_id" class="form-control select2bs4">
                                    <option value="" disabled selected>Pilih item...</option>
                                    @foreach ($item as $itm)
                                        <option value="{{ $itm->id }}">{{ $itm->code }} - {{ $itm->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="supplier_id" class="col-md-4 control-label">Supplier</label>
                            <div class="col-md-8">
                                <select name="supplier_id" id="supplier_id" class="form-control select2bs4" required
                                    oninvalid="this.setCustomValidity('Silahkan masukan supplier')"
                                    oninput="this.setCustomValidity('')">
                                    <option value="" disabled selected>Pilih item...</option>
                                    @foreach ($supplier as $itm)
                                        <option value="{{ $itm->id }}">{{ $itm->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="jumlah" class="col-md-4 col-md-offset-1 control-label">Jumlah</label>

                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" min="1" name="jumlah" id="jumlah"
                                        class="form-control" required
                                        oninvalid="this.setCustomValidity('Silahkan masukan jumlah')"
                                        oninput="this.setCustomValidity('')">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="satuan-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="harga" class="col-md-4 col-md-offset-1 control-label">Harga</label>
                            <div class="col-md-8">
                                <input type="text" name="harga" id="harga" class="form-control harga" required
                                    autofocus oninvalid="this.setCustomValidity('Silahkan masukan harga')"
                                    oninput="this.setCustomValidity(''); formatRibuanKoma(this)">
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="total_harga" class="col-md-4 control-label">Total Harga</label>
                            <div class="col-md-8">
                                <input type="text" name="total_harga" id="total_harga" class="form-control" readonly>
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="note" class="col-md-4 col-md-offset-1 control-label">Note</label>
                            <div class="col-md-8">
                                <textarea name="note" id="note" class="form-control" rows="3" placeholder="Masukkan catatan..."></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 control-label">Stok Akhir Sekarang</label>
                            <div class="col-md-8">
                                <input type="text" id="stokakhir-input" class="form-control" disabled>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i
                                class="fa fa-xmark"></i> Batal</button>
                        <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.getElementById('jumlah').addEventListener('input', updateTotal);
        document.getElementById('harga').addEventListener('input', updateTotal);

        function updateTotal() {
            let jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
            let harga = document.getElementById('harga').value.replace(/\./g, '').replace(/,/g, '.') || 0;
            harga = parseFloat(harga);

            let total = jumlah * harga;

            document.getElementById('total_harga').value =
                total.toLocaleString('id-ID');
        }
    </script>
