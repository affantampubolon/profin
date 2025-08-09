<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Pelanggan</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">
                            <svg class="stroke-icon">
                                <use
                                    href="assets/svg/icon-sprite.svg#stroke-home"></use>
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
                    <h4>Master Pelanggan</h4>
                    <div class="row g-3">
                        <div class="row g-3">
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Cabang</label
                                >
                                <select id="cabangpelanggan" class="select2 form-control" name="cabang_pelanggan"
                                    <?= ($session->get('branch_id') <> '11') ? 'disabled' : ''; ?>>
                                    <?php if ($session->get('branch_id') <> '11'): ?>
                                        <!-- Jika bukan branch_id = 11 -->
                                        <option value="<?= $session->get('branch_id'); ?>" selected>
                                            <?= $session->get('branch_id'); ?> - <?= $session->get('branch_name'); ?>
                                        </option>
                                    <?php else: ?>
                                        <!-- Jika branch_id = 11, tampilkan opsi dropdown biasa -->
                                        <option value="">Pilih Cabang</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <div id="tabel_master_pelanggan"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
<!-- Modal untuk menampilkan detail -->
    <div
        class="modal fade"
        id="detailPelangganModal"
        data-bs-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="detailPelangganModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-toggle-wrapper text-start dark-sign-up">
                    <div class="modal-header">
                        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-wrapper-toggle">
                            <div class="row g-3 text-center">
                                <h5 class="display-8"><b>Detail Pelanggan</b></h5>
                                <p class="" id="pelanggan"></p>
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <p class="text-uppercase"><i class="fa fa-pencil"></i> <b>Identitas Pelanggan</b></p>
                                <div class="row g-3">
                                    <div class="col-xl-4 col-md-4">
                                        <label class="form-label" for=""><b>Nama PIC</b></label>
                                        <p class="" id="nama_pic"></p>
                                    </div>
                                    <div class="col-xl-4 col-md-4">
                                        <label class="form-label" for=""><b>Email</b></label>
                                        <p class="" id="email"></p>
                                    </div>
                                    <div class="col-xl-4 col-md-4">
                                        <label class="form-label" for=""><b>No. Telepon</b></label>
                                        <p class="" id="no_telp"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <p class="text-uppercase"><i class="fa fa-pencil"></i> <b>Lokasi Pelanggan</b></p>
                                <div class="row g-3">
                                    <div class="col-xl-12 col-md-12">
                                        <label class="form-label" for=""><b>Alamat</b></label>
                                        <p class="" id="alamat"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <p class="text-uppercase"><i class="fa fa-pencil"></i> <b>Informasi Pajak</b></p>
                                <div class="row g-3">
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for=""><b>NPWP</b></label>
                                        <p class="" id="npwp"></p>
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for=""><b>Nama</b></label>
                                        <p class="" id="nama_pajak"></p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-xl-12 col-md-12">
                                        <label class="form-label" for=""><b>Alamat</b></label>
                                        <p class="" id="alamat_pajak"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Container-fluid Ends-->
<?= $this->endSection(); ?>
<!-- END : End Main Content-->