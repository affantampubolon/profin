<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Kunjungan Marketing</h4>
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
                    <h4>Monitoring Kunjungan Marketing</h4>
                </div>
                <div class="card-body pt-0">
                    <ul
                      class="nav nav-pills nav-primary"
                      id="pills-tab"
                      role="tablist"
                    >
                      <li class="nav-item">
                        <a
                          class="nav-link m-10"
                          id="mon_penggunaan"
                          data-bs-toggle="pill"
                          href="#mon_penggunaan1"
                          role="tab"
                          aria-controls="pills-aboutus"
                          aria-selected="true"
                          >Penggunaan</a
                        >
                      </li>
                      <li class="nav-item">
                        <a
                          class="nav-link m-10"
                          id="mon_pelanggan"
                          data-bs-toggle="pill"
                          href="#mon_pelanggan1"
                          role="tab"
                          aria-controls="pills-contactus"
                          aria-selected="false"
                          >Pelanggan</a
                        >
                      </li>
                      <li class="nav-item">
                        <a
                          class="nav-link m-10"
                          id="mon_user"
                          data-bs-toggle="pill"
                          href="#mon_user1"
                          role="tab"
                          aria-controls="pills-blog"
                          aria-selected="false"
                          >User
                        </a>
                      </li>
                      <li class="nav-item">
                        <a
                          class="nav-link m-10"
                          id="mon_user_cat"
                          data-bs-toggle="pill"
                          href="#mon_user_cat1"
                          role="tab"
                          aria-controls="pills-blog"
                          aria-selected="false"
                          >Marketing
                        </a>
                      </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div
                        class="tab-pane fade"
                        id="mon_penggunaan1"
                        role="tabpanel"
                        aria-labelledby="pills-aboutus-tab"
                        >
                        <div class="row m-10">
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Cabang</label
                                >
                                <select id="cabangkunjmarketing" class="select2 form-control" name="cabang_kunjungan_marketing"
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
                        <div class="row m-10">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <div id="table_kunj_marketing_guna"></div>
                            </div>
                        </div>
                      </div>
                      <div
                        class="tab-pane fade"
                        id="mon_pelanggan1"
                        role="tabpanel"
                        aria-labelledby="pills-aboutus-tab"
                      >
                        <div class="row m-10">
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Grup Barang</label
                                >
                                <select id="grupBarang" class="select2 form-control" name="grup_barang" disabled>
                                        <option value="02"> 
                                            02 - Marketing
                                        </option>
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Subgrup Barang</label
                                >
                                <select id="subgrupBarang" class="select2 form-control" name="subgrup_barang">
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Kelas Barang</label
                                >
                                <select id="kelasBarang" class="select2 form-control" name="kelas_barang">
                                </select>
                            </div>
                        </div>
                        <div class="row m-10">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <div id="table_kunj_marketing_pelanggan"></div>
                            </div>
                        </div>
                      </div>
                      <div
                        class="tab-pane fade"
                        id="mon_user1"
                        role="tabpanel"
                        aria-labelledby="pills-aboutus-tab"
                        >
                        <div class="row m-10">
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Cabang</label
                                >
                                <select id="cabangkunjmarketinguser" class="select2 form-control" name="cabang_kunjungan_marketing"
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
                                <label class="form-label" for=""
                                    >Marketing</label
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
                                        <option value="">Pilih Marketing</option>
                                        <?php foreach ($data_salesmarketing as $salesmarketing): ?>
                                            <option value="<?= $salesmarketing->nik; ?>">
                                                <?= $salesmarketing->name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php elseif ($session->get('role_id') != '5' && $session->get('branch_id') == '11'): ?>
                                        <option value="">Pilih Marketing</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row m-10">
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Grup Barang</label
                                >
                                <select id="grupBarang1" class="select2 form-control" name="grup_barang" disabled>
                                        <option value="02"> 
                                            02 - Marketing
                                        </option>
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Subgrup Barang</label
                                >
                                <select id="subgrupBarang1" class="select2 form-control" name="subgrup_barang">
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Kelas Barang</label
                                >
                                <select id="kelasBarang1" class="select2 form-control" name="kelas_barang">
                                </select>
                            </div>
                        </div>
                        <div class="row m-10">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <div id="table_kunj_marketing_user"></div>
                            </div>
                        </div>
                      </div>
                      <div
                        class="tab-pane fade"
                        id="mon_user_cat1"
                        role="tabpanel"
                        aria-labelledby="pills-aboutus-tab"
                        >
                        <div class="row m-10">
                            <div class="col-xl-6 col-md-6">
                                <label class="form-label" for=""
                                    >Cabang</label
                                >
                                <select id="cabangkunjmarketingusercat" class="select2 form-control" name="cabang_kunjungan_marketing"
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
                        <div class="row m-10">
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Grup Barang</label
                                >
                                <select id="grupBarang2" class="select2 form-control" name="grup_barang" disabled>
                                        <option value="02"> 
                                            02 - Marketing
                                        </option>
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Subgrup Barang</label
                                >
                                <select id="subgrupBarang2" class="select2 form-control" name="subgrup_barang">
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <label class="form-label" for=""
                                    >Kelas Barang</label
                                >
                                <select id="kelasBarang2" class="select2 form-control" name="kelas_barang">
                                </select>
                            </div>
                        </div>
                        <div class="row m-10">
                            <div class="col-xl-12 col-md-12 box-col-12">
                                <div id="table_kunj_marketing_user_cat"></div>
                            </div>
                        </div>
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