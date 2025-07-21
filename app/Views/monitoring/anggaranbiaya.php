<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Monitoring</h4>
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
    <!-- kartu pertama -->
    <div class="row size-column">
        <div class="col-xl-12 col-md-12 box-col-12">
            <div class="card">
                <div class="card-header card-no-border">
                    <h4>Anggaran dan Realisasi Biaya</h4>
                </div>
                <div class="card-body pt-0">
                    <div class="row m-10">
                        <div class="col-xl-6 col-md-6">
                            <label class="form-label" for=""
                                >No. WBS</label
                            >
                            <select id="nowbsfilter" name="nowbsfilter" class="select2 form-control">
                                <option value="">Pilih No. Wbs</option>
                            </select>
                        </div>
                    </div>
                    <div class="row m-10">
                        <span class="f-12 f-w-500 text-uppercase">Unduh Data Anggaran dan Biaya:</span>
                        <div class="row m-10">
                            <div class="col-xl-12 col-md-12">
                                <button id="unduhdataexcel" class="btn btn-sm btn-outline-primary btn-pill">
                                    <i class="fa fa-file-excel-o"></i> Unduh Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- kartu kedua -->
    <div class="row size-column">
        <div class="col-xl-12 col-md-12 box-col-12">
            <div class="card">
                <div class="card-header card-no-border">
                </div>
                <div class="card-body pt-0">
                    <div class="row m-10">
                        <div class="col-xl-12 col-md-12">
                            <div id="tabel_anggaran_biaya"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->

<!-- Modal Detail Realisasi dan Dropping -->
<div class="modal fade" id="dataDetRealisasiModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="dataDetRealisasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Realisasi Biaya dan Dropping</h5>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-xl-12 col-md-12 box-col-12">
                    <ul class="nav nav-pills nav-primary" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link m-10 active" id="realisasibiaya" data-bs-toggle="pill" href="#realisasibiaya1" role="tab" aria-controls="pills-aboutus" aria-selected="true">Realisasi Biaya</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link m-10" id="dropping" data-bs-toggle="pill" href="#dropping1" role="tab" aria-controls="pills-contactus" aria-selected="false">Dropping</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade active show" id="realisasibiaya1" role="tabpanel" aria-labelledby="pills-aboutus-tab">
                            <div class="row">
                                <div class="col-xl-12 col-md-12">
                                    <div id="tabel_detail_realisasi"></div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dropping1" role="tabpanel" aria-labelledby="pills-aboutus-tab">
                            <div class="row">
                            <div class="col-xl-12 col-md-12">
                                    <div id="tabel_detail_dropping"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<!-- END : End Main Content-->