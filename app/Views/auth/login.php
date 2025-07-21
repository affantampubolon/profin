<?= $this->extend('layout/templateAuth'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- login page start-->
<div class="container-fluid p-0">
  <div class="row m-0">
    <div class="col-12 p-0">
      <div class="login-card">
        <div>
          <div>
            <a class="logo"><img
                class="img-fluid for-dark"
                src="<?= base_url(''); ?>riho/assets/images/logo/PROFIN-upt1.png"
                alt="looginpage" /><img
                class="img-fluid for-light"
                src="<?= base_url(''); ?>riho/assets/images/logo/PROFIN-upt1.png"
                style="width: 10%;"
                alt="looginpage" /></a>
          </div>
          <div class="login-main">
            <?= form_open('login', ['class' => 'theme-form']); ?>
            <center>
              <h3>SELAMAT DATANG</h3>
            </center>
            <div class="form-group">
              <label class="col-form-label">ID Pengguna</label>
              <input
                class="form-control"
                type="text"
                required="true"
                name="username"
                </div>
              <div class="form-group">
                <label class="col-form-label">Kata Sandi</label>
                <div class="form-input position-relative">
                  <input
                    class="form-control"
                    type="password"
                    name="password"
                    required="true" />
                  <div class="show-hide"><span class="show"></span></div>
                </div>
              </div>
              <div class="form-group mb-1">
                <a class="checkbox p-0" href="<?= base_url(); ?>forget-password.html">Lupa Kata Sandi?</a>
              </div>
              <?php if (session()->getFlashdata('msg')) : ?>
                <div class="form-group mb-1">
                  <div class="alert alert-danger"> <i class="fa fa-times-circle mr-2"></i><?= session()->getFlashdata('msg') ?></div>
                </div>
              <?php elseif (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger">
                  <i class="fa fa-times-circle mr-2"></i><?= session()->getFlashdata('error') ?>
                </div>
              <?php elseif (session()->getFlashdata('logout')) : ?>
                <div class="form-group mb-1">
                  <div class="alert alert-success"> <i class="fa fa-user mr-2"></i><?= session()->getFlashdata('logout') ?></div>
                </div>
              <?php endif; ?>
              <div class="form-group mb-0">
                <div class="text-end mt-3">
                  <button
                    class="btn btn-primary w-100 f-w-700"
                    type="submit">
                    MASUK
                  </button>
                </div>
              </div>
              <?= form_close(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?= $this->endSection(); ?>
    <!-- END : End Main Content-->