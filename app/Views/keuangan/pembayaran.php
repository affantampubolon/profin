<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Keuangan</h4>
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
                    <h4>Pembayaran Invoice</h4>
                </div>
                <div class="card-body pt-0">
                        <div class="row m-10">
                            <div class="col-xl-12 col-md-12">
                                <button id="tambahbaris" class="btn btn-sm btn-pill btn-outline btn-primary">
                                    <i class="fa fa-plus"></i> Tambah Baris
                                </button>
                            </div>
                        </div>
                        <div class="row m-10">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <div id="tabel_pembayaran"></div>
                            </div>
                        </div>
                        <div class="row m-10">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <button class="btn btn-success" type="submit">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
<!-- Modal Unggah Invoice -->
<div class="modal fade" id="unggahInvoiceModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="unggahInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unggah Dokumen Invoice</h5>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-xl-12 col-md-12">
                    <div class="row">
                        <div class="col-xl-12 col-md-12">
                            <form id="uploadForm" enctype="multipart/form-data">
                                <div class="form-group">
                                    <p class="form-label" for="">Unggah File Invoice <span class="txt-danger f-w-600">*</span></p>
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="uploadButton">Unggah</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<!-- END : End Main Content-->