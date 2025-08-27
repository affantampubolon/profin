<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Proyek</h4>
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
                    <h4>Pembaruan Data Proyek</h4>
                </div>
                <div class="card-body pt-0">
                    <div class="row m-10">
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div id="tabel_daftar_proyek"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->

<!-- Modal Update Karyawan -->
<div class="modal fade" id="updateProyekModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="updateProyekModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProyekModalLabel">Update Data Proyek</h5>
                <button
                    class="btn-close py-0"
                    type="button"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" id="formUpdateProyek" action="/proyek/pembaruandata/updatedataproyek" method="POST" enctype="multipart/form-data">
                    <div class="col-xl-12 col-md-12 box-col-12">
                        <div class="row p-2">
                        <p class="text-uppercase"><b>.Registrasi Proyek</b></p>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for="">No. WBS </label>
                                <input class="form-control" id="nowbs" name="nowbs" type="text" />
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for="">No. SO </label>
                                <input class="form-control" id="noso" name="noso" type="text" />
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for="">No. Laporan </label>
                                <input class="form-control" id="reportno" name="reportno" type="text" />
                            </div>
                        </div>
                        <div class="row p-2">
                        <p class="text-uppercase"><b>.Periode Kontrak atau Proyek</b></p>
                            <?php
                            // Gunakan $session yang diteruskan dari controller
                            $role_id = $session->get('role_id');
                            ?>
                            <!-- Logika berdasarkan role -->
                            <?php if ($role_id == 5): ?>
                                <div class="row p-2">
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for="">Termin </label>
                                        <input class="form-control" id="termintime" name="termintime" type="text" />
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for="">Durasi Kontrak (hari) </label>
                                        <input class="form-control" id="contracttottime" name="contracttottime" type="text" />
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="row p-2">
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for="">Termin (hari) </label>
                                        <input class="form-control" id="termintime" name="termintime" type="text" disabled/>
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for="">Durasi Kontrak (hari) </label>
                                        <input class="form-control" id="contracttottime" name="contracttottime" type="text" disabled/>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for="">Tanggal Mulai Pekerjaan </label>
                                <div class="input-group flatpicker-calender">
                                    <input
                                        class="form-control"
                                        id="jobstartdate"
                                        name="jobstartdate"
                                        type="date"
                                        value=""
                                    />
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for="">Tanggal Selesai Pekerjaan </label>
                                <div class="input-group flatpicker-calender">
                                    <input
                                        class="form-control"
                                        id="jobenddate"
                                        name="jobenddate"
                                        type="date"
                                        value=""
                                    />
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for="">Total Waktu Pekerjaan (hari) </label>
                                <input class="form-control" id="jobtotaltime" name="jobtotaltime" type="text" />
                            </div>
                        </div>
                        <div class="row p-2">
                        <p class="text-uppercase"><b>.Invoice</b></p>

                            <?php
                            // Gunakan $session yang diteruskan dari controller
                            $role_id = $session->get('role_id');
                            ?>

                            <!-- Logika berdasarkan role -->
                            <?php if ($role_id == 5): ?>
                                <div class="row p-2">
                                    <div class="col-xl-6 col-md-12">
                                        <p class="form-label" for="">Unggah File Invoice </p>
                                        <input
                                            class="form-control"
                                            name="fileInvoice" id="fileInvoice"
                                            type="file"
                                            aria-describedby="inputGroupFileAddon03"
                                            aria-label="Upload"
                                        />
                                        <p class="mb-2">
                                            <em>Silahkan unggah file dalam format <strong>.pdf</strong>, dengan kapasitas file maks. <strong>2,5 MB</strong></em>
                                        </p>
                                    </div>
                                    <div class="col-xl-6 col-md-12">
                                        <p class="form-label" for="">Unggah File Faktur Pajak </p>
                                        <input
                                            class="form-control"
                                            name="fileFakturPajak" id="fileFakturPajak"
                                            type="file"
                                            aria-describedby="inputGroupFileAddon03"
                                            aria-label="Upload"
                                        />
                                        <p class="mb-2">
                                            <em>Silahkan unggah file dalam format <strong>.pdf</strong>, dengan kapasitas file maks. <strong>2,5 MB</strong></em>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="invoicesenddate">Tanggal Kirim Invoice</label>
                                    <div class="input-group flatpicker-calender">
                                        <input
                                            class="form-control"
                                            id="invoicesenddate"
                                            name="invoicesenddate"
                                            type="date"
                                            value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="invoicereceivedate">Tanggal Terima Invoice</label>
                                    <div class="input-group flatpicker-calender">
                                        <input
                                            class="form-control"
                                            id="invoicereceivedate"
                                            name="invoicereceivedate"
                                            type="date"
                                            value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="invoicereceivename">Nama Penerima</label>
                                    <input class="form-control" id="invoicereceivename" name="invoicereceivename" type="text" />
                                </div>
                            <?php else: ?>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="invoicesenddate">Tanggal Kirim Invoice</label>
                                    <div class="input-group flatpicker-calender">
                                        <input
                                            class="form-control"
                                            id="invoicesenddate"
                                            name="invoicesenddate"
                                            type="date"
                                            value=""
                                            disabled
                                        />
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="invoicereceivedate">Tanggal Terima Invoice</label>
                                    <div class="input-group flatpicker-calender">
                                        <input
                                            class="form-control"
                                            id="invoicereceivedate"
                                            name="invoicereceivedate"
                                            type="date"
                                            value=""
                                            disabled
                                        />
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="invoicereceivename">Nama Penerima</label>
                                    <input class="form-control" id="invoicereceivename" name="invoicereceivename" type="text" disabled/>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row p-2">
                        <p class="text-uppercase"><b>.Detail Proyek</b></p>
                            <div class="col-xl-12 col-md-12">
                                <label class="form-label" for="">Progres Pekerjaan % </label>
                                <select id="progressjob" name="progressjob" class="select2 form-control">
                                    <option value="0">0 %</option>
                                    <option value="10">10 %</option>
                                    <option value="20">20 %</option>
                                    <option value="30">30 %</option>
                                    <option value="40">40 %</option>
                                    <option value="50">50 %</option>
                                    <option value="60">60 %</option>
                                    <option value="70">70 %</option>
                                    <option value="80">80 %</option>
                                    <option value="90">90 %</option>
                                    <option value="100">100 %</option>
                                </select>
                            </div>
                        </div>
                        <div class="row p-2">
                            <div class="col-xl-12 col-md-12">
                                <label class="form-label" for="">Nilai Pendapatan </label>
                                <input class="form-control" id="revenueamt" name="revenueamt" type="text" />
                            </div>
                        </div>
                        <div class="row p-2">
                            <div class="col-xl-6 col-md-12">
                                <p class="form-label" for="">Unggah File SPK </p>
                                <input
                                    class="form-control"
                                    name="fileSpk" id="fileSpk"
                                    type="file"
                                    aria-describedby="inputGroupFileAddon03"
                                    aria-label="Upload"
                                />
                                <p class="mb-2">
                                    <em>Silahkan unggah file dalam format <strong>.pdf</strong>, dengan kapasitas file maks. <strong>2,5 MB</strong></em>
                                </p>
                            </div>
                        </div>
                        <div class="row p-2">
                            <div class="col-xl-6 col-md-12">
                                <p class="form-label" for="">Unggah File Laporan </p>
                                <input
                                    class="form-control"
                                    name="fileLaporan" id="fileLaporan"
                                    type="file"
                                    aria-describedby="inputGroupFileAddon03"
                                    aria-label="Upload"
                                />
                                <p class="mb-2">
                                    <em>Silahkan unggah file dalam format <strong>.pdf</strong>, dengan kapasitas file maks. <strong>2,5 MB</strong></em>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"> Simpan</i> 
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<!-- END : End Main Content-->