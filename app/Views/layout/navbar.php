<!-- Page Header Start-->
<div class="page-header">
  <div class="header-wrapper row m-0">
    <div class="header-logo-wrapper col-auto p-0">
      <div class="logo-wrapper">
        <a href="index.html"><img
            class="img-fluid for-light"
            src="<?= base_url(''); ?>riho/assets/images/logo/logo_dark.png"
            alt="logo-light" /><img
            class="img-fluid for-dark"
            src="<?= base_url(''); ?>riho/assets/images/logo/logo.png"
            alt="logo-dark" /></a>
      </div>
      <div class="toggle-sidebar">
        <i
          class="status_toggle middle sidebar-toggle"
          data-feather="align-center"></i>
      </div>
    </div>
    <div
      class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
      <div>
        <a class="toggle-sidebar" href="#">
          <i class="iconly-Category icli"> </i></a>
        <div class="d-flex align-items-center gap-2">
          <h4 class="f-w-600">Selamat Datang <?= $session->get('name') ?>,</h4>
          <img class="mt-0" src="<?= base_url(''); ?>riho/assets/images/hand.gif" alt="hand-gif" />
        </div>
      </div>
    </div>
    <div
      class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
      <ul class="nav-menus">
        <li class="profile-nav onhover-dropdown">
          <div class="media profile-media">
            <img
              class="b-r-10"
              src="<?= base_url(''); ?>riho/assets/images/dashboard/profile.png"
              alt="" />
            <div class="media-body d-xxl-block d-none box-col-none">
              <div class="d-flex align-items-center gap-2">
                <span><?= $session->get('name') ?> </span><i class="middle fa fa-angle-down"> </i>
              </div>
              <p class="mb-0 font-roboto"><?= $session->get('role_id') ?></p>
            </div>
          </div>
          <ul class="profile-dropdown onhover-show-div">
            <!-- <li>
              <a href="user-profile.html"><i data-feather="user"></i><span>Profil</span></a>
            </li>
            <li>
              <a href="edit-profile.html">
                <i data-feather="settings"></i><span>Pengaturan</span></a>
            </li> -->
            <li>
              <a
                class="btn btn-pill btn-outline-primary btn-sm"
                href="<?= base_url('logout'); ?>">Keluar</a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
    <script class="result-template" type="text/x-handlebars-template">
      <div class="ProfileCard u-cf">
              <div class="ProfileCard-avatar"><svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="feather feather-airplay m-0"
                ><path
                    d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"
                  ></path><polygon
                    points="12 15 17 21 7 21 12 15"
                  ></polygon></svg></div>
              <div class="ProfileCard-details">
                <div class="ProfileCard-realName">{{name}}</div>
              </div>
            </div>
          </script>
  </div>
</div>
<!-- Page Header Ends -->