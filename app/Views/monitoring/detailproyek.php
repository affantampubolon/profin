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
                    <h4>Detail Proyek</h4>
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
                        <span class="f-12 f-w-500 text-uppercase">Unduh Data Proyek:</span>
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
                            <div id="tabel_detail_proyek"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->

<!-- Modal Update Karyawan -->
<div class="modal fade" id="dataDetProyekModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="dataDetProyekModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Data Proyek</h5>   (<h5 id="nowbsheader"></h5>)
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-xl-12 col-md-12 box-col-12">
                    <ul class="nav nav-pills nav-primary" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link m-10 active" id="proyek" data-bs-toggle="pill" href="#proyek1" role="tab" aria-controls="pills-aboutus" aria-selected="true">Proyek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link m-10" id="invoice" data-bs-toggle="pill" href="#invoice1" role="tab" aria-controls="pills-contactus" aria-selected="false">Invoice</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link m-10" id="anggaranbiaya" data-bs-toggle="pill" href="#anggaranbiaya1" role="tab" aria-controls="pills-blog" aria-selected="false">Anggaran & Biaya</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade active show" id="proyek1" role="tabpanel" aria-labelledby="pills-aboutus-tab">
                            <div class="row">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label proyek-label" for=""><b>No. SO</b></label>
                                    <p id="noso" name="noso"></p>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label proyek-label" for=""><b>No. Laporan</b></label>
                                    <p id="reportno" name="reportno"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label proyek-label" for=""><b>Tanggal Mulai Pekerjaan</b></label>
                                    <p id="jobstartdate" name="jobstartdate"></p>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label proyek-label" for=""><b>Tanggal Selesai Pekerjaan</b></label>
                                    <p id="jobenddate" name="jobenddate"></p>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label proyek-label" for=""><b>Total Waktu Pekerjaan (hari)</b></label>
                                    <p id="jobtotaltime" name="jobtotaltime"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label proyek-label" for=""><b>Nama Pekerjaan</b></label>
                                    <p id="jobname" name="jobname"></p>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label proyek-label" for=""><b>Nama Perusahaan</b></label>
                                    <p id="companyname" name="companyname"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-md-12">
                                    <label class="form-label proyek-label" for=""><b>Alamat Perusahaan</b></label>
                                    <p id="companyaddress" name="companyaddress"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label proyek-label" for=""><b>Nama PIC</b></label>
                                    <p id="companypic" name="companypic"></p>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label proyek-label" for=""><b>No. Telp</b></label>
                                    <p id="telpno" name="telpno"></p>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label proyek-label" for=""><b>Email</b></label>
                                    <p id="email" name="email"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-md-12">
                                    <label class="form-label proyek-label" for=""><b>Lokasi Pekerjaan</b></label>
                                    <p id="joblocation" name="joblocation"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label proyek-label" for=""><b>Project Manager(PM)</b></label>
                                    <p id="projectmanager" name="projectmanager"></p>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label proyek-label" for=""><b>Inspektur</b></label>
                                    <p id="inspector" name="inspector"></p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="invoice1" role="tabpanel" aria-labelledby="pills-aboutus-tab">
                            <div class="row">
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label invoice-label" for=""><b>Tanggal Kirim Invoice</b></label>
                                    <p id="invoicesenddate" name="invoicesenddate"></p>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label invoice-label" for=""><b>Tanggal Terima Invoice</b></label>
                                    <p id="invoicereceivedate" name="invoicereceivedate"></p>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label invoice-label" for=""><b>Nama Penerima</b></label>
                                    <p id="invoicereceivename" name="invoicereceivename"></p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="anggaranbiaya1" role="tabpanel" aria-labelledby="pills-aboutus-tab">
                            <div class="row">
                                <p>Rupiah</p>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label anggaranbiaya-label" for=""><b>Saldo Piutang</b></label>
                                    <p id="arbalance" name="arbalance"></p>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label anggaranbiaya-label" for=""><b>Nilai Kontrak</b></label>
                                    <p id="contractamt" name="contractamt"></p>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label anggaranbiaya-label" for=""><b>Nilai Pendapatan</b></label>
                                    <p id="revenueamt" name="revenueamt"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-3 col-md-3">
                                    <label class="form-label anggaranbiaya-label" for=""><b>Nilai Anggaran</b></label>
                                    <p id="budgetamt" name="budgetamt"></p>
                                </div>
                                <div class="col-xl-3 col-md-3">
                                    <label class="form-label anggaranbiaya-label" for=""><b>Nilai Realisasi Biaya</b></label>
                                    <p id="realamt" name="realamt"></p>
                                </div>
                                <div class="col-xl-3 col-md-3">
                                    <label class="form-label anggaranbiaya-label" for=""><b>(%) Biaya</b></label>
                                    <p id="prsachiev" name="prsachiev"></p>
                                </div>
                                <div class="col-xl-3 col-md-3">
                                    <label class="form-label anggaranbiaya-label" for=""><b>Nilai Realisasi Dropping</b></label>
                                    <p id="realdropamt" name="realdropamt"></p>
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