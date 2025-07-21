<!DOCTYPE html>
<html lang="en">

<!-- BEGIN : Head-->
<?= $this->include('layout/headerAuth'); ?>
<!-- END : Head-->

<!-- BEGIN : Body-->

<body>
  <!-- BEGIN : Main Content-->
  <?= $this->renderSection('content'); ?>
  <!-- END : Main Content-->


  <!-- BEGIN : Footer-->
  <?= $this->include('layout/footerAuth'); ?>
  <!-- END : Footer-->

</body>

</html>