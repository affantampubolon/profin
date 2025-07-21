<?= $this->extend('layout/template'); ?>

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
                    <h4>Formulir Registrasi Proyek</h4>
                </div>
                <div class="card-body pt-0">
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                    <form class="row g-3" id="formProyek" action="/proyek/registrasi/insertdataproyek" method="POST">
                        <div class="col-xl-12 col-md-12 box-col-12">
                        <p class="text-uppercase"><b>.Detail Perusahaan dan Pekerjaan</b></p>
                            <div class="row p-2">
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="">No. WBS <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="nowbs" name="nowbs" type="text" />
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="">No. SO <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="noso" name="noso" type="text" />
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="">No. Laporan <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="reportno" name="reportno" type="text" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Nama Pekerjaan <span class="txt-danger f-w-600">*</span></label>
                                    <textarea class="form-control" id="jobname" name="jobname" type="text"> </textarea>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Nama Perusahaan <span class="txt-danger f-w-600">*</span></label>
                                    <select id="companyname" name="companyname" class="select2 form-control">
                                        <option value="">Pilih Perusahaan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row p-2">
                                <div class="col-xl-12 col-md-12">
                                    <label class="form-label" for="">Alamat Perusahaan <span class="txt-danger f-w-600">*</span></label>
                                    <textarea class="form-control" id="companyaddress" name="companyaddress" type="text"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row p-2">
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="">Nama PIC <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="companypic" name="companypic" type="text" />
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="">No. Telp <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="telpno" name="telpno" type="text" />
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="">Email <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="email" name="email" type="text" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row p-2">
                                <div class="col-xl-12 col-md-12">
                                    <label class="form-label" for="">Lokasi Pekerjaan <span class="txt-danger f-w-600">*</span></label>
                                    <textarea class="form-control" id="joblocation" name="joblocation" type="text"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Project Manager(PM) <span class="txt-danger f-w-600">*</span></label>
                                    <select id="projectmanager" name="projectmanager" class="select2 form-control">
                                        <option value="">Pilih Project Manager(PM)</option>
                                    </select>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for="">Inspector <span class="txt-danger f-w-600">*</span></label>
                                    <select id="inspector" name="inspector" class="select2 form-control">
                                        <option value="">Pilih Inspector</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                        <p class="text-uppercase"><b>.Rincian Kontrak, Biaya & Pendapatan</b></p>
                            <div class="row p-2">
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="">Nilai Kontrak <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="contractamt" name="contractamt" type="text" />
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="">Nilai Pendapatan</label>
                                    <input class="form-control" id="revenueamt" name="revenueamt" type="text" />
                                </div>
                                <div class="col-xl-4 col-md-4">
                                    <label class="form-label" for="">Rencana Biaya <span class="txt-danger f-w-600">*</span></label>
                                    <input class="form-control" id="costplanamt" name="costplanamt" type="text" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <button class="btn btn-success" type="submit">
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