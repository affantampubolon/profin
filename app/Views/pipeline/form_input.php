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
                            href="assets/svg/icon-sprite.svg#stroke-home"
                            ></use></svg
                        ></a>
                    </li>
                    <!--  -->
                    </ol>
                </div>
                <!-- <div id="toast-container" class="toast-top-right"></div> -->
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row size-column">
            <div class="col-xl-12 col-md-12 box-col-12">
                <div class="card">
                    <div class="card-header card-no-border">
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row">
                                <div class="p-2">
                                    <a href="/pipeline/pembuatan"><i class="fa fa-arrow-circle-o-left"></i> <b>Kembali</b></a>
                                </div>
                            </div>
                        </div>
                        <h4>Formulir Pembuatan Pipeline</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <form class="row g-3" action="/pipeline/insertForm" method="POST" id="formPipeline">
                                    <div class="col-xl-6 col-md-6 box-col-6">
                                        <label class="form-label" for=""
                                            >Tahun</label
                                        >
                                        <select id="tahunPipeline" class="select2 form-control" name="tahun_pipeline">
                                            <option value="<?= date('Y'); ?>"><?= date('Y'); ?></option>
                                            <option value="<?= date('Y')+ 1; ?>"><?= date('Y')+ 1; ?></option>
                                        </select>
                                    </div>
                                    <div class="col-xl-6 col-md-6 box-col-6">
                                        <label class="form-label" for=""
                                            >Bulan</label
                                        >
                                        <select id="bulanPipeline" class="select2 form-control" name="bulan_pipeline"></select>
                                    </div>
                                    <div class="col-xl-4 col-md-4">
                                        <label class="form-label" for=""
                                            >Grup Barang</label
                                        >
                                        <select id="grupBarang" class="select2 form-control" name="grup_barang">
                                            <option value="">Pilih Grup</option>
                                            <?php foreach ($group_barang as $group): ?>
                                                <option value="<?= $group->group_id; ?>"> <?= $group->group_id ?> - <?= $group->group_name; ?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-xl-4 col-md-4">
                                        <label class="form-label" for=""
                                            >Subgrup Barang</label
                                        >
                                        <select id="subgrupBarang" class="select2 form-control" name="subgrup_barang">
                                            <option value="">Pilih Subgrup</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-4 col-md-4">
                                        <label class="form-label" for=""
                                            >Kelas Barang</label
                                        >
                                        <select id="kelasBarang" class="select2 form-control" name="kelas_barang">
                                            <option value="">Pilih Kelas</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-12 col-md-12">
                                        <!--tombol modal mengisi detail pipeline-->
                                        <button
                                            class="btn btn-pill btn-air-* btn-primary"
                                            type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#formdetailpipeline"
                                            data-whatever="@getbootstrap"
                                        >
                                            <i class="fa fa-plus"></i> Pelanggan
                                        </button>
                                    </div>
                                    <div class="col-xl-12 col-md-12">
                                        <label class="form-label" for=""
                                            >Daftar Pelanggan (Detail Pipeline)</label
                                        >
                                        <div id="tabel_detail_pipeline"></div>
                                    </div>
                                    <div class="col-xl-12 col-md-12">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-save"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="modal fade"
        id="formdetailpipeline"
        data-bs-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="formdetailpipeline"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div
                class="modal-toggle-wrapper text-start dark-sign-up"
                >
                    <h3
                        class="modal-header justify-content-center border-0"
                    >
                        Detail Pipeline
                    </h3>
                    <div class="modal-body">
                        <form class="row g-3">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <label class="form-label" for=""
                                    >Pelanggan <span class="txt-danger f-w-600">*</span></label
                                >
                                <select id="masterpelanggan" class="select2 form-control" name="master_pelanggan">
                                </select>
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <label class="form-label" for=""
                                    >Frekuensi Kunjungan <span class="txt-danger f-w-600">*</span></label
                                >
                                <input class="form-control" id="freqVisit" type="text" placeholder="0">
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <label class="form-label" for=""
                                    >Target Nilai (Rp) <span class="txt-danger f-w-600">*</span></label
                                >
                                <input class="form-control" id="targetNilai" type="text" placeholder="0">
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <label class="form-label" for=""
                                    >Target Probabilitas <span class="txt-danger f-w-600">*</span></label
                                >
                                <select id="targetProbabilitas" class="select2 form-control" name="tahun_pipeline">
                                    <option value="">Probabilitas</option>
                                    <?php foreach ($probabilitas as $probabilitas): ?>
                                        <option value="<?= htmlspecialchars($probabilitas['scale'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($probabilitas['scale'], ENT_QUOTES, 'UTF-8') ?>% - <?= htmlspecialchars($probabilitas['description'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <p class="f-w-600"><span class="txt-danger">*</span>) Wajib Diisi</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" id="tambahDataDetPipeline"><i class="fa fa-save"></i> Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection(); ?>
<!-- END : End Main Content-->