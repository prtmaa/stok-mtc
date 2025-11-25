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
                                <input type="text" id="item_nama" class="form-control" placeholder="Nama item"
                                    readonly disabled>
                                <select name="item_id" id="item_id" class="form-control select2bs4">
                                    <option value="" disabled selected>Pilih item...</option>

                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 control-label">Stok Akhir Sekarang</label>
                            <div class="col-md-8">
                                <input type="text" id="stokakhir-input" class="form-control" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="jumlah" class="col-md-4 control-label">Jumlah</label>
                            <div class="col-md-5">
                                <input type="number" min="1" name="jumlah" id="jumlah" class="form-control"
                                    required>
                            </div>
                            <label id="satuan-text" class="col-md-3 control-label text-left"></label>
                        </div>

                        <div class="form-group row">
                            <label for="divisi_id" class="col-md-4 col-md-offset-1 control-label">Divisi</label>
                            <div class="col-md-8">
                                <select name="divisi_id" id="divisi_id" class="form-control" required
                                    oninvalid="this.setCustomValidity('Silahkan pilih Item')"
                                    oninput="this.setCustomValidity('')">
                                    <option value="" disabled selected>Pilih. . . </option>
                                    @foreach ($divisi as $itm)
                                        <option value="{{ $itm->id }}">{{ $itm->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="note" class="col-md-4 col-md-offset-1 control-label">Note</label>
                            <div class="col-md-8">
                                <textarea name="note" id="note" class="form-control" rows="3" placeholder="Masukkan catatan..."></textarea>
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
