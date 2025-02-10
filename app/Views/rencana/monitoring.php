<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>

<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-6">
        <h4>Monitoring</h4>
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
  <div class="row">
    <!-- Zero Configuration  Starts-->
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header pb-0 card-no-border">
          <h4>Monitoring Rencana Kehadiran</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive custom-scrollbar">
            <table class="display" id="tabel-izin">
              <thead>
                <tr>
                  <th>Nik</th>
                  <th>Nama</th>
                  <th>Cabang</th>
                  <th>Divisi</th>
                  <th>Jadwal Kunjungan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Tiger Nixon</td>
                  <td>System Architect</td>
                  <td>Edinburgh</td>
                  <td>61</td>
                  <td>2011/04/25</td>
                  <td>$320,800</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- Zero Configuration  Ends-->
  </div>
</div>
<!-- Container-fluid Ends-->

<?= $this->endSection(); ?>
<!-- END : Main Content-->