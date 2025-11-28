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
                            <label for="nama" class="col-md-4 col-md-offset-1 control-label">Item</label>
                            <div class="col-md 6">
                                <input type="text" name="nama" id="nama" class="form-control" required
                                    autofocus oninvalid="this.setCustomValidity('Item harus diisi')"
                                    oninput="this.setCustomValidity('')">
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="kategori_id" class="col-md-4 col-md-offset-1 control-label">Kategori</label>
                            <div class="col-md 6">
                                <select name="kategori_id" id="kategori_id" class="form-control" required
                                    oninvalid="this.setCustomValidity('Silahkan pilih kategori')"
                                    oninput="this.setCustomValidity('')">
                                    <option value="" disabled selected>Pilih. . . </option>
                                    @foreach ($kategori as $itm)
                                        <option value="{{ $itm->id }}">{{ $itm->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="satuan_id" class="col-md-4 col-md-offset-1 control-label">Satuan</label>
                            <div class="col-md 6">
                                <select name="satuan_id" id="satuan_id" class="form-control" required
                                    oninvalid="this.setCustomValidity('Silahkan pilih satuan')"
                                    oninput="this.setCustomValidity('')">
                                    <option value="" disabled selected>Pilih. . . </option>
                                    @foreach ($satuan as $itm)
                                        <option value="{{ $itm->id }}">{{ $itm->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="min" class="col-md-4 col-md-offset-1 control-label">Min Stok</label>
                            <div class="col-md 6">
                                <input type="number" name="min" id="min" class="form-control" value="0"
                                    required autofocus oninvalid="this.setCustomValidity('Item harus diisi')"
                                    oninput="this.setCustomValidity('')">
                                <span class="help-block with-errors"></span>
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
