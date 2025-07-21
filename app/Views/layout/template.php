<!DOCTYPE html>
<html lang="en">

<!-- BEGIN : Head-->
<?= $this->include('layout/header'); ?>
<!-- END : Head-->

<!-- BEGIN : Body-->

<body>
  <!-- loader starts-->
  <div class="loader-wrapper">
    <div class="loader">
      <div class="loader4"></div>
    </div>
  </div>
  <!-- loader ends-->
  <!-- tap on top starts-->
  <div class="tap-top"><i data-feather="chevrons-up"></i></div>
  <!-- tap on tap ends-->
  <!-- page-wrapper Start-->
  <div class="page-wrapper compact-wrapper" id="pageWrapper">

    <!-- BEGIN : Navbar-->
    <?= $this->include('layout/navbar'); ?>
    <!-- END : Navbar-->

    <!-- Page Body Start-->
    <div class="page-body-wrapper">

      <!-- BEGIN : Sidebar-->
      <?= $this->include('layout/sidebar'); ?>
      <!-- END : Sidebar-->

      <div class="page-body">

        <!-- BEGIN : Main Content-->
        <?= $this->renderSection('content'); ?>
        <!-- END : Main Content-->

      </div>

      <!-- BEGIN : Footer-->
      <?= $this->include('layout/footer'); ?>
      <!-- END : Footer-->

</body>

</html>