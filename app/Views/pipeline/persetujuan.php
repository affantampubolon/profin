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
                    <h4>Persetujuan Pipeline</h4>
                    <div class="row g-3">
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label" for=""
                                >Tahun</label
                            >
                            <select id="tahunAccPipeline" class="select2 form-control" name="tahun_acc_pipeline">
                                <option value="<?= date('Y'); ?>"><?= date('Y'); ?></option>
                                <option value="<?= date('Y')+ 1; ?>"><?= date('Y')+ 1; ?></option>
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label" for=""
                                >Bulan</label
                            >
                            <select id="bulanAccPipeline" class="select2 form-control" name="bulan_acc_pipeline"></select>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label" for=""
                                >Sales / Marketing</label
                            >
                            <select id="salesMarketing" class="select2 form-control" name="sales_marketing">
                                <option value="">Pilih Sales/Marketing</option>
                                <?php foreach ($data_salesmarketing as $salesmarketing): ?>
                                    <option value="<?= $salesmarketing->nik; ?>"> <?= $salesmarketing->name ?></option>
                                <?php endforeach; ?>
                            </select>
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
                            <button id="selectAll" class="btn btn-pill btn-outline btn-success">
                                <i class="fa fa-check-circle-o"></i> Pilih Semua
                            </button>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                             <div id="tabel_verifikasi_pipeline"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
<!-- Modal Input Alasan Penolakan -->
<div
        class="modal fade"
        id="rejectModal"
        data-bs-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="rejectModal"
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
                        Alasan Penolakan
                    </h3>
                    <div class="modal-body">
                        <form class="row g-3">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <label for="reject_reason">Alasan Penolakan:</label>
                                <textarea id="reject_reason" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <p class="f-w-600"><span class="txt-danger">*</span>) Wajib Diisi</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" id="saveReject"><i class="fa fa-save"></i> Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>
<!-- END : End Main Content-->