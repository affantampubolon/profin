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
                    <h4>Pembayaran Invoice</h4>
                </div>
                <div class="card-body pt-0">
                    <div class="row m-10">
                        <div class="col-xl-6 col-md-6">
                            <label class="form-label" for=""
                                >Tahun</label
                            >
                            <select id="tahunfilter" name="tahunfilter" class="select2 form-control">
                                <option value="">Pilih Tahun</option>
                                    <?php
                                        $currentYear = date('Y'); // Tahun saat ini (2025)
                                        for ($i = 0; $i <= 3; $i++) {
                                            $year = $currentYear - $i;
                                            $selected = ($i === 0) ? 'selected' : ''; // Pilih tahun saat ini secara default
                                            echo "<option value='$year' $selected>$year</option>";
                                        }
                                    ?>
                            </select>
                        </div>
                    </div>
                    <div class="row m-10">
                        <span class="f-12 f-w-500 text-uppercase">Unduh Pembayaran Piutang:</span>
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
                            <div id="tabel_pembayaran_piutang"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->

<!-- Modal Detail Pembayaran Piutang -->
<div class="modal fade" id="dataPembayaranPiutangModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="dataPembayaranPiutangLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pembayaran Invoice</h5>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-xl-12 col-md-12">
                    <div class="row">
                        <div class="col-xl-12 col-md-12">
                            <div id="tabel_detail_pembayaran_piutang"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<!-- END : End Main Content-->