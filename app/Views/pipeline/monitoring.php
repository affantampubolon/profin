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
                        <div class="row g-3">
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Tahun</label
                                >
                                <select id="tahunMonPipeline" class="select2 form-control" name="tahun_mon_pipeline">
                                    <option value="<?= date('Y'); ?>"><?= date('Y'); ?></option>
                                    <option value="<?= date('Y')+ 1; ?>"><?= date('Y')+ 1; ?></option>
                                </select>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Bulan</label
                                >
                                <select id="bulanMonPipeline" class="select2 form-control" name="bulan_mon_pipeline"></select>
                            </div>
                        </div>
                        <div class="row g-1">
                            <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                        >Cabang</label
                                    >
                                    <select id="cabangOps" class="select2 form-control" name="cabang_ops"
                                        <?= ($session->get('branch_id') <> '11') ? 'disabled' : ''; ?>>
                                        <?php if ($session->get('branch_id') <> '11'): ?>
                                            <!-- Jika bukan branch_id = 11 -->
                                            <option value="<?= $session->get('branch_id'); ?>" selected>
                                                <?= $session->get('branch_id'); ?> - <?= $session->get('branch_name'); ?>
                                            </option>
                                        <?php else: ?>
                                            <!-- Jika branch_id = 11, tampilkan opsi dropdown biasa -->
                                            <option value="">Pilih Cabang</option>
                                        <?php endif; ?>
                                    </select>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for="">Sales / Marketing</label>
                                <select id="salesMarketing" class="select2 form-control" name="sales_marketing"
                                    <?= ($session->get('role_id') == '5') ? 'disabled' : ''; ?>>
                                    <?php if ($session->get('role_id') == '5' && $session->get('branch_id') != '11'): ?>
                                        <!-- Jika role_id = 5, set default ke username -->
                                        <option value="<?= $session->get('username'); ?>" selected>
                                            <?= $session->get('name'); ?>
                                        </option>
                                    <?php elseif ($session->get('role_id') != '5' && $session->get('branch_id') != '11'): ?>
                                        <!-- Jika bukan role_id = 5, tampilkan opsi dropdown biasa -->
                                        <option value="">Pilih Sales/Marketing</option>
                                        <?php foreach ($data_salesmarketing as $salesmarketing): ?>
                                            <option value="<?= $salesmarketing->nik; ?>">
                                                <?= $salesmarketing->name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php elseif ($session->get('role_id') != '5' && $session->get('branch_id') == '11'): ?>
                                        <option value="">Pilih Sales/Marketing</option>
                                    <?php endif; ?>
                                </select>
                            </div>
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