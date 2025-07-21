<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
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
                            </svg></a>
                    </li>
                    <?php
                    $path_parts = explode(">", $breadcrumb['path_name']);
                    $current_path = '';
                    foreach ($path_parts as $part):
                        $current_path .= $part . '>';
                        if ($part != end($path_parts)): ?>
                            <li class="breadcrumb-item"><?= $part ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page"><?= $part ?></li>
                    <?php endif;
                    endforeach;
                    ?>
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
                                <a href="/master/karyawan/formulir"><i class="fa fa-plus-circle"></i> <b>Tambah data Karyawan</b></a>
                            </div>
                        </div>
                    </div>
                    <h4>Master Karyawan</h4>
                </div>
                <div class="card-body pt-0">
                    <div class="row m-10">
                        <div class="col-xl-6 col-md-6">
                            <label class="form-label" for="">Cabang</label>
                            <select id="cabang" class="select2 form-control" name="cabang"
                                <?= ($session->get('branch_id') !== '11') ? 'disabled' : ''; ?>>
                                <?php if ($session->get('branch_id') !== '11'): ?>
                                    <option value="<?= $session->get('branch_id'); ?>" selected>
                                        <?= $session->get('branch_id'); ?> - <?= $session->get('branch_name'); ?>
                                    </option>
                                <?php else: ?>
                                    <option value="">Pilih Cabang</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row m-10">
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div id="table_master_karyawan"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->

<!-- Modal Update Karyawan -->
<div class="modal fade" id="updateKaryawanModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="updateKaryawanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateKaryawanModalLabel">Update Data Karyawan</h5>
                <button
                    class="btn-close py-0"
                    type="button"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" id="formUpdateKaryawan" action="/master/karyawan/updatedatakaryawan" method="POST">
                    <div class="col-xl-12 col-md-12 box-col-12">
                        <div class="row p-2">
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for="">NIK Karyawan <span class="txt-danger f-w-600">*</span></label>
                                <input class="form-control" id="nikKaryawan" name="nikKaryawan" type="text" readonly />
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for="">Nama Karyawan <span class="txt-danger f-w-600">*</span></label>
                                <input class="form-control" id="namaKaryawan" name="namaKaryawan" type="text" readonly />
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
<?= $this->endSection(); ?>
<!-- END : End Main Content-->