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
                    <h4>Formulir Registrasi Pelanggan</h4>
                </div>
                <div class="card-body pt-0">
                    <form class="row g-3">
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <p class="text-uppercase"><b>Identitas Pelanggan</b></p>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Nama Pelanggan</label
                                    >
                                    <input
                                    class="form-control"
                                    id="namaPelanggan"
                                    type="text"
                                    placeholder="nama pelanggan"
                                    />
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Nama PIC <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <input
                                    class="form-control"
                                    id="namaPic"
                                    type="text"
                                    placeholder="nama pic"
                                    />
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Email</label
                                    >
                                    <input
                                    class="form-control"
                                    id="emailPelanggan"
                                    type="email"
                                    placeholder="email"
                                    />
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >No. Telepon <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <input
                                    class="form-control"
                                    id="notelpPelanggan"
                                    type="text"
                                    placeholder="no telepon"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <p class="text-uppercase"><b>Lokasi Pelanggan</b></p>
                            <div class="row p-2">
                                <div class="col-xl-12 col-md-12">
                                    <label class="form-label" for=""
                                    >Alamat <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <textarea
                                        class="form-control"
                                        id="alamatPelanggan"
                                        required
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <p class="text-uppercase"><b>Informasi Pajak</b></p>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >NPWP</label
                                    >
                                    <input
                                    class="form-control"
                                    id="npwpPelanggan"
                                    type="text"
                                    placeholder="npwp"
                                    />
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Nama</label
                                    >
                                    <input
                                    class="form-control"
                                    id="namanpwpPelanggan"
                                    type="text"
                                    placeholder="nama"
                                    />
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-12 col-md-12">
                                    <label class="form-label" for=""
                                    >Alamat</label
                                    >
                                    <textarea
                                        class="form-control"
                                        id="alamatnpwpPelanggan"
                                    ></textarea>
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
    <div class="row size-column">
        <div class="col-xl-12 col-md-12 box-col-12">
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
<?= $this->endSection(); ?>
<!-- END : End Main Content-->