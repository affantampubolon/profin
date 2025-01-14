<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Pipeline</h4>
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
                    <h4>Pembuatan Pipeline</h4>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="revenuegrowth-details">
                                <div class="growth-details">
                                    <span class="f-12 f-w-500 text-uppercase">Unduh Format Dokumen:</span>
                                    <div class="input-group mb-2">
                                        <button
                                            class="btn btn-info mb-2"
                                            type="button"
                                            data-bs-toggle="tooltip"
                                            title="btn btn-info">
                                            Unduh
                                        </button>
                                    </div>
                                </div>
                                <div class="growth-details">
                                    <span class="f-12 f-w-500 text-uppercase">Unggah Dokumen:
                                    </span>
                                    <div class="input-group mb-2">
                                        <input
                                            class="form-control"
                                            id="inputGroupFile04"
                                            type="file"
                                            aria-describedby="inputGroupFileAddon04"
                                            aria-label="Upload" />
                                        <button
                                            class="btn btn-outline-success"
                                            id="inputGroupFileAddon04"
                                            type="button">
                                            Unggah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 col-md-4 box-col-12">
                            <div class="select-box">
                                <div class="options-container">
                                    <div class="selection-option">
                                        <input
                                            class="radio"
                                            id="year"
                                            name="year"
                                            value="<?= date('Y'); ?>" />
                                        <label class="mb-0" for="year"><?= date('Y'); ?></label>
                                    </div>
                                    <div class="selection-option">
                                        <input
                                            class="radio"
                                            id="year"
                                            name="year"
                                            value="<?= date('Y') + 1; ?>" />
                                        <label class="mb-0" for="year"><?= date('Y') + 1; ?></label>
                                    </div>
                                </div>
                                <div class="selected-box">Tahun</div>
                                <div class="search-box">
                                    <input type="text" placeholder="Mulai Mengetik..." />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row size-column">
        <div class="col-xl-12 col-md-12 box-col-12">
            <div class="card">
                <div class="card-header card-no-border">
                    <!-- <h4>Pembuatan Pipeline</h4> -->
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div id="tabel_pembuatan_pipeline"></div>
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