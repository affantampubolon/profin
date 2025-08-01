<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>User</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">
                            <svg class="stroke-icon">
                                <use href="assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg>
                        </a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row size-column">
        <div class="col-xl-12 col-md-12 box-col-12">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="col-xl-12 col-md-12 box-col-12">
                        <div class="row">
                            <div class="p-2">
                                <a href="/master/user/index"><i class="fa fa-arrow-circle-o-left"></i> <b>Kembali</b></a>
                            </div>
                        </div>
                    </div>
                    <h4>Formulir Registrasi User</h4>
                    <div class="col-xl-6 col-md-6">
                        <label class="form-label" for="">Karyawan</label>
                        <select id="masterkaryawan" class="select2 form-control" name="master_karyawan"></select>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form class="row g-3" id="formUser" action="/master/user/insertdatauser" method="POST">
                        <input type="hidden" id="idRefUser" name="idRefUser">
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Username <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="usernameKaryawan" name="usernameKaryawan" type="text"/>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Password<span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="passwordKaryawan" name="passwordKaryawan" type="text" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->

<?= $this->endSection(); ?>