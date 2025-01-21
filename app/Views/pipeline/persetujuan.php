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
                            <select id="salesAccPipeline" class="select2 form-control" name="sales_acc_pipeline"></select>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label" for=""
                                >Grup Barang</label
                            >
                            <select id="grupBarang" class="select2 form-control" name="grup_barang">
                                <option value="">Pilih Grup</option>
                                <?php foreach ($group_barang as $group): ?>
                                    <option value="<?= htmlspecialchars($group['group_id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($group['group_id'], ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($group['group_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
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
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div id="tabel_persetujuan_pipeline"></div>
                        </div>
                        <div class="col-xl-12 col-md-12">
                            <button class="btn btn-primary" type="submit">
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
<?= $this->endSection(); ?>
<!-- END : End Main Content-->