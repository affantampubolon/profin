<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Distribusi Produk</h4>
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
                    <h4>Monitoring Distribusi Produk</h4>
                </div>
                <div class="card-body pt-0">
                        <div class="row g-3">
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Tanggal</label
                                >
                                <div class="form-group">
                                  <input
                                    class="form-control"
                                    name="rentang_tgl_distribusi"
                                    id="rentangTanggalDistribusi"
                                    type="text"
                                    value="<?php echo date("Y-m-d"); ?>"
                                  />
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Cabang</label
                                >
                                <select id="cabangdistprod" class="select2 form-control" name="cabang_dist_prod"
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
                        </div>
                        <div class="row g-3">
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Grup Barang</label
                                >
                                <select id="grupBarang" class="select2 form-control" name="grup_barang">
                                    <?php foreach ($group_barang as $groupbarang): ?>
                                        <option value="<?= $groupbarang['group_id']; ?>"> 
                                            <?= $groupbarang['group_id'] ?> - <?= $groupbarang['group_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Subgrup Barang</label
                                >
                                <select id="subgrupBarang" class="select2 form-control" name="subgrup_barang">
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Kelas Barang</label
                                >
                                <select id="kelasBarang" class="select2 form-control" name="kelas_barang">
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <p style="margin: 5px;"><b>Pelanggan:</b> <i class='fa fa-circle' style='color:#483AA0'></i> Existing <i class='fa fa-circle' style='color:#129990'></i> NOO</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <div id="map" style="z-index: 1;"></div>
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