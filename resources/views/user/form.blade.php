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
                            <label for="name" class="col-md-2 col-md-offset-1 control-label">Username</label>
                            <div class="col-md 6">
                                <input type="text" name="name" id="name" class="form-control" required
                                    oninvalid="this.setCustomValidity('Username harus diisi')"
                                    oninput="this.setCustomValidity('')" autofocus>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-md-offset-1 control-label">Email</label>
                            <div class="col-md 6">
                                <input type="email" name="email" id="email" class="form-control" required
                                    oninvalid="this.setCustomValidity('Email harus diisi')"
                                    oninput="this.setCustomValidity('')" autofocus>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-2 col-md-offset-1 control-label">Password</label>
                            <div class="col-md 6">
                                <input type="password" name="password" id="password" class="form-control" required
                                    oninvalid="this.setCustomValidity('Password harus diisi')"
                                    oninput="this.setCustomValidity('')" autofocus>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-md-2 col-md-offset-1 control-label">Role</label>
                            <div class="col-md 6">
                                <select name="role" id="role" class="form-control" required
                                    oninvalid="this.setCustomValidity('Role belum dipilih')"
                                    oninput="this.setCustomValidity('')" autofocus>
                                    <option value="" selected disabled>Pilih role user</option>
                                    <option value="master">Master</option>
                                    <option value="admin">Admin</option>
                                    <option value="viewer">Viewer</option>
                                </select>
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
