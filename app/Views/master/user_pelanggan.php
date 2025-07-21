<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>User Pelanggan</h4>
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
                    <h4>Master User Pelanggan</h4>
                    <div class="row g-3">
                        <div class="row g-3">
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Cabang</label
                                >
                                <select id="cabanguserpelanggan" class="select2 form-control" name="cabang_user_pelanggan"
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
                                <div id="tabel_master_user_pelanggan"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
<!-- Modal untuk update data user pelanggan -->
    <div
        class="modal fade"
        id="updateUserPelangganModal"
        data-bs-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="updateUserPelangganModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-toggle-wrapper text-start dark-sign-up">
                    <div class="modal-header">
                        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-wrapper-toggle">
                            <div class="row g-3 text-center">
                                <h5 class="display-8"><b>Pembaruan User Pelanggan</b></h5>
                                <p class="" id="pelanggan"></p>
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <div class="row g-3">
                                    <div class="col-xl-12 col-md-12">
                                        <label class="form-label" for="">Nama</label>
                                        <input
                                            class="form-control"
                                            id="namaUser"
                                            type="text"
                                        />
                                    </div>
                                    <div class="col-xl-12 col-md-12">
                                        <label class="form-label" for="">Jabatan / Posisi</label>
                                        <select id="posisiUserPelanggan" class="select2 form-control" name="posisi_user_pelanggan">
                                        </select>
                                    </div>
                                    <div class="col-xl-12 col-md-12">
                                        <label class="form-label" for="">No. Telepon</label>
                                        <input
                                            class="form-control"
                                            id="noTelp"
                                            type="text"
                                        />
                                    </div>
                                    <div class="col-xl-12 col-md-12">
                                        <label class="form-label" for=""
                                        >Status</label
                                        >
                                        <select id="statusUser" class="select2 form-control" name="tipe_bangunan">
                                            <option value=true>Aktif</option>
                                            <option value=false>Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="saveUpdate">Simpan</button>
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