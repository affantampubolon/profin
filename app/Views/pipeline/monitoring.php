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
                    <h4>Monitoring Pipeline</h4>
                    <div class="row g-3">
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label" for=""
                                >Tahun</label
                            >
                            <select id="tahunMonPipeline" class="select2 form-control" name="tahun_mon_pipeline">
                                <option value="<?= date('Y'); ?>"><?= date('Y'); ?></option>
                                <option value="<?= date('Y')+ 1; ?>"><?= date('Y')+ 1; ?></option>
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label" for=""
                                >Bulan</label
                            >
                            <select id="bulanMonPipeline" class="select2 form-control" name="bulan_mon_pipeline"></select>
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
                        <div class="col-xl-12 col-md-12 box-col-12">
                             <div id="tabel_monitoring_pipeline"></div>
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