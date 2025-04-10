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
                    <h4>Persetujuan Realisasi Kunjungan</h4>
                    <div class="row g-3">
                        <div class="row g-3">
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Tanggal</label
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
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Cabang</label
                                >
                                <select id="cabangRealisasiOps" class="select2 form-control" name="cabang_realisasiops"
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
                        <div class="col-xl-12 col-md-12">
                            <button id="feedback_spv" class="btn btn-pill btn-outline btn-success">
                                <i class="fa fa-save"></i> Simpan Data
                            </button>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                              <p><b>Keterangan:</b> <i class='fa fa-circle' style='color:#578FCA'></i> Terkunjungi <i class='fa fa-circle' style='color:#FF5677'></i> Tidak Terkunjungi</p>
                             <div id="tabel_verifikasi_realisasi_kunjungan"></div>
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

<div
    class="modal fade"
    id="feedbackModal"
    data-bs-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="feedbackModal"
    aria-hidden="true"
>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="feedbackModalLabel">Feedback Verifikasi</h5>
        <button
          class="btn-close py-0"
          type="button"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <textarea id="feedbackInput" class="form-control" rows="5" placeholder="Masukkan feedback (minimal 50 karakter, maksimal 250 karakter)"></textarea>
        <p id="charCount" class="text-muted">0/250</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveFeedback">Simpan</button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection(); ?>
<!-- END : End Main Content-->