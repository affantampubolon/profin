<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Realisasi Kunjungan</h4>
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
                    <h4>Buka Persetujuan Realisasi Kunjungan</h4>
                    <div class="row g-3">
                        <div class="row g-3">
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Tanggal Kunjungan</label
                                >
                                <div class="input-group flatpicker-calender">
                                  <input
                                    class="form-control"
                                    id="tanggalDispenKunjungan"
                                    type="date"
                                    value=""
                                  />
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Cabang</label
                                >
                                <select id="cabangBukaVerifikasi" class="select2 form-control" name="cabang_buka_verifikasi"
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
                             <div id="tabel_buka_verifikasi_realisasi_kunjungan"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
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
                                Detail Realisasi Kunjungan
                            </h5>
                        </div>
                        <!-- <div class="row">
                            <p class="justify-content-center border-0" id="kode_pelanggan"></p> <p>-</p> <p class="justify-content-center border-0" id="nama_pelanggan"></p>
                        </div> -->
                    </div>
                    <div class="modal-body">
                        <div class="col-xl-12 col-md-12 box-col-12">
                             <div id="tabel_det_verifikasi_realisasi_kunjungan"></div>
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
<!-- Modal Peta -->
<div
    class="modal fade"
    id="mapModal"
    data-bs-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="mapModal"
    aria-hidden="true"
>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="kode_pelanggan"></h5>-<h5 id="nama_pelanggan"></h5>
        <button
          class="btn-close py-0"
          type="button"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div id="map" style="height: 400px;"></div>
      </div>
    </div>
  </div>
</div>
<!-- modal data photo kunjungan -->
    <div
        class="modal fade"
        id="fotoKunjModal"
        data-bs-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="fotoKunjModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-toggle-wrapper text-start dark-sign-up">
                    <div class="modal-header justify-content-center border-0">
                        <h5 class="justify-content-center border-0">
                            Foto Realisasi
                        </h5>
                        <div class="row">
                            <p class="justify-content-center border-0" id="kode_pelanggan"></p>
                            <p>-</p>
                            <p class="justify-content-center border-0" id="nama_pelanggan"></p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="col-xl-12 col-md-12 box-col-12 text-center">
                            <!-- Elemen untuk menampilkan foto -->
                            <img id="foto_realisasi" src="" alt="Foto Realisasi" class="img-fluid" style="max-width: 100%; max-height: 400px;" />
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