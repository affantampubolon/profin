<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Karyawan</h4>
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
                                <a href="/master/karyawan/index"><i class="fa fa-arrow-circle-o-left"></i> <b>Kembali</b></a>
                            </div>
                        </div>
                    </div>
                    <h4>Formulir Registrasi Karyawan</h4>
                </div>
                <div class="card-body pt-0">
                    <form class="row g-3" id="formKaryawan" action="/master/karyawan/insertdatakaryawan" method="POST">
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">NUP Karyawan <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="nikKaryawan" name="nikKaryawan" type="text" />
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Nama Karyawan <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="namaKaryawan" name="namaKaryawan" type="text" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Cabang <span class="txt-danger f-w-600">*</span></label>
                                    <select id="cabKaryawan" class="select2 form-control" name="cabKaryawan">
                                        <?php foreach ($cabang as $cabang): ?>
                                            <option value="<?= $cabang['branch_id']; ?>">
                                                <?= $cabang['branch_id'] ?> - <?= $cabang['branch_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Hak Akses <span class="txt-danger f-w-600">*</span></label>
                                    <select id="roleKaryawan" class="select2 form-control" name="roleKaryawan">
                                        <?php foreach ($role as $role): ?>
                                            <option value="<?= $role['id']; ?>">
                                                <?= $role['id'] ?> - <?= $role['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Departemen / Divisi <span class="txt-danger f-w-600">*</span></label>
                                    <select id="depKaryawan" class="select2 form-control" name="depKaryawan">
                                        <?php foreach ($departemen as $departemen): ?>
                                            <option value="<?= $departemen['id']; ?>">
                                                <?= $departemen['id'] ?> - <?= $departemen['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Jabatan <span class="txt-danger f-w-600">*</span></label>
                                    <select id="jabKaryawan" class="select2 form-control" name="jabKaryawan">
                                        <?php foreach ($jabatan as $jabatan): ?>
                                            <option value="<?= $jabatan['id']; ?>">
                                                <?= $jabatan['id'] ?> - <?= $jabatan['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
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