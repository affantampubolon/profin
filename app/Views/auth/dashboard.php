<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>

<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-6">
        <h4>Beranda</h4>
      </div>
      <div class="col-6">
        <ol class="breadcrumb">
          <?php
          $path_parts = explode(">", $breadcrumb['path_name']);
          $current_path = '';
          foreach ($path_parts as $part):
            $current_path .= $part . '>';
            if ($part != end($path_parts)): ?>
              <li class="breadcrumb-item"><?= $part ?></li>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">/ <?= $part ?></li>
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
    <div class="row g-3">
      <div class="col-xl-4 col-md-12 box-col-12">
        <div class="card" style="height: 350px">
          <div class="card-header sales-chart card-no-border">
            <h4>Pencapaian</h4>
          </div>
          <div class="card-body pt-0">
            <canvas id="pencapaian_cab" height="100"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="row g-3">
      <div class="col-xl-6 col-md-6 box-col-12">
          <div class="card" style="height: 350px">
              <div class="card-header card-no-border">
                <div class="row g-3">
                  <h4><i class="fa fa-warning" style='color:#FFAE1A'></i> Daftar Belum Diverifikasi</h4>
                </div>
              </div>
              <div class="card-body pt-0">
                  <div class="row g-3">
                      <div class="col-xl-12 col-md-12 box-col-12">
                          <?php if ($session->get('role_id') == '5'): ?>
                            <div class="table-order table-responsive table-striped">
                              <table class="w-100">
                                  <thead>
                                      <tr>
                                          <th>Nama</th>
                                          <th>Pipeline</th>
                                          <th>Rencana</th>
                                          <th>Realisasi</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php foreach ($data_daftar_tunda_verif as $row) : ?>
                                          <tr>
                                              <td><?= $row->emp_name ?></td>
                                              <td><?= $row->tot_job_pipeline ?></td>
                                              <td><?= $row->tot_job_plan ?></td>
                                              <td><?= $row->tot_job_real ?></td>
                                          </tr>
                                      <?php endforeach; ?>
                                  </tbody>
                              </table>
                            </div>
                          <?php else: ?>
                            <div id="tabel_daftar_verifikasi_tertunda"></div>
                          <?php endif; ?> 
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
<!-- END : Main Content-->