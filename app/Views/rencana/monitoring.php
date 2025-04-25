<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Rencana Kunjungan</h4>
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
                    <h4>Monitoring Rencana Kunjungan</h4>
                    <div class="row g-3">
                        <div class="row g-3">
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Tanggal Rencana</label
                                >
                                <div class="form-group">
                                  <input
                                    class="form-control"
                                    name="rentang_tgl_mon"
                                    id="rentangTanggalMon"
                                    type="text"
                                    value="<?php echo date("Y-m-d"); ?>"
                                  />
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Cabang</label
                                >
                                <select id="cabangRencanaOps" class="select2 form-control" name="cabang_rencanaops"
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
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Sales / Marketing  </label
                                >
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
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label" for=""
                                >Grup Barang</label
                            >
                            <select id="grupBarang" class="select2 form-control" name="grup_barang" disabled>
                                <option value="">Pilih Grup</option>
                                <option value="<?= $session->get('group_id'); ?>" selected>
                                    <?= $session->get('group_id'); ?> - <?= $session->get('group_name'); ?>
                                </option>
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label" for=""
                                >Subgrup Barang</label
                            >
                            <select id="subgrupBarang" class="select2 form-control" name="subgrup_barang">
                                <?php foreach ($subgroup_barang as $subgroupbarang): ?>
                                    <option value="<?= $subgroupbarang['subgroup_id']; ?>"> 
                                        <?= $subgroupbarang['subgroup_id'] ?> - <?= $subgroupbarang['subgroup_name'] ?>
                                    </option>
                                <?php endforeach; ?>
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
                            <p><b>Keterangan:</b> <i class='fa fa-check' style='color:#03A791'></i> Ya <i class='fa fa-times' style='color:#FF5677'></i> Tidak</p>
                             <div id="tabel_monitoring_rencana_kunjungan"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan detail -->
    <div
        class="modal fade"
        id="detailModalMonitoring"
        data-bs-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="detailModalMonitoringLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-toggle-wrapper text-start dark-sign-up">
                    <div class="modal-header justify-content-center border-0">
                        <div class="row">
                            <h5 class="justify-content-center border-0">
                                Detail Rencana Kunjungan
                            </h5>
                        </div>
                        <!-- <div class="row">
                            <p class="justify-content-center border-0" id="kode_pelanggan"></p> <p>-</p> <p class="justify-content-center border-0" id="nama_pelanggan"></p>
                        </div> -->
                    </div>
                    <div class="modal-body">
                        <div class="col-xl-12 col-md-12 box-col-12">
                             <div id="tabel_det_monitoring_rencana_kunjungan"></div>
                             <!-- Elemen table untuk kondisi data kosong -->
                             <table id="detailTable" class="table table-bordered" style="display: none;">
                              <tbody></tbody>
                             </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Container-fluid Ends-->
<?= $this->endSection(); ?>
<!-- END : End Main Content-->