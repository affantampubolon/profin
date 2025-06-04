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
                    <h4>Persetujuan Rencana Kunjungan</h4>
                    <div class="row g-3">
                        <div class="row g-3">
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Tanggal Kunjungan</label
                                >
                                <div class="input-group flatpicker-calender">
                                  <input
                                    class="form-control"
                                    id="tanggalAccKunjungan"
                                    type="date"
                                    value=""
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
                        <div class="col-xl-12 col-md-12">
                            <button id="saveApproveAll" class="btn btn-pill btn-outline btn-success">
                                <i class="fa fa-check-circle-o"></i> Simpan Data
                            </button>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                             <div id="tabel_verifikasi_rencana_kunjungan"></div>
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

<!-- Modal untuk menampilkan detail -->
    <div
        class="modal fade"
        id="detailModal"
        data-bs-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="detailModalLabel"
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
                             <div id="tabel_det_verifikasi_rencana_kunjungan"></div>
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
<?= $this->endSection(); ?>
<!-- END : End Main Content-->