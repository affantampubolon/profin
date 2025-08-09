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
  <!-- filter grafik -->
  <div class="row size-column">
    <div class="row g-1">
      <div class="col-xl-12 col-md-12 box-col-12">
        <div class="card">
          <div class="card-header sales-chart card-no-border">
          </div>
          <div class="card-body pt-0">
              <div class="row m-10">
                   <div class="col-xl-6 col-md-6">
                      <label class="form-label f-w-600" for=""
                          >Tahun</label
                      >
                      <select id="tahunfilter" name="tahunfilter" class="select2 form-control">
                          <option value="">Pilih Tahun</option>
                              <?php
                                  $currentYear = date('Y'); // Tahun saat ini (2025)
                                  for ($i = 0; $i <= 3; $i++) {
                                      $year = $currentYear - $i;
                                      $selected = ($i === 0) ? 'selected' : ''; // Pilih tahun saat ini secara default
                                      echo "<option value='$year' $selected>$year</option>";
                                  }
                              ?>
                      </select>
                  </div>
                  <div class="col-xl-6 col-md-6">
                      <label class="form-label f-w-600" for=""
                          >Project Manager</label
                      >
                      <select id="pmfilter" name="pmfilter" class="select2 form-control">
                      </select>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- <div class="row size-column">
    <p><b>Rupiah (Rp)</b></p>
  </div> -->
  <!-- jumlah detail Proyek dan pembayaran Invoice-->
  <div class="row size-column">
    <div class="row g-1">
      <div class="col-xl-4 col-md-4 box-col-4">
        <div class="card alert-light-info" style="height: 150px;">
          <div class="card-header">
            <h5 class="f-w-700"><i class="fa fa-pencil"></i>  Nilai Proyek</h5>
          </div>
          <div class="card-body">
            <h4 class="f-w-900" id="totalcontract"></h4>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-md-4 box-col-4">
        <div class="card alert-light-success" style="height: 150px;">
          <div class="card-header">
            <h5 class="f-w-700"><i class="fa fa-thumbs-o-up"></i>  Realisasi Pendapatan</h5>
          </div>
          <div class="card-body">
            <h4 class="f-w-900" id="totalrevenue"></h4>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-md-4 box-col-4">
        <div class="card alert-light-primary" style="height: 150px;">
          <div class="card-header">
            <h5 class="f-w-700"><i class="fa fa-anchor"></i>  Total Pekerjaan</h5>
          </div>
          <div class="card-body">
            <h4 class="f-w-900" id="totalproject"></h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row size-column">
    <div class="row g-1">
      <div class="col-xl-6 col-md-6 box-col-6">
        <div class="card alert-light-primary" style="height: 150px;">
          <div class="card-header">
            <h5 class="f-w-700"><i class="fa fa-suitcase"></i>  Nilai Anggaran</h5>
          </div>
          <div class="card-body">
            <h4 class="f-w-900" id="totalbudget"></h4>
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-md-6 box-col-6">
        <div class="card alert-light-danger" style="height: 150px;">
          <div class="card-header">
            <h5 class="f-w-700"><i class="fa fa-money"></i>  Realisasi Biaya</h5>
          </div>
          <div class="card-body">
            <h4 class="f-w-900" id="totalrealbudget"></h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row size-column">
    <div class="row g-1 justify-content-between">
      <div class="col-xl-12 col-md-12 box-col-12">
        <div class="card">
          <div class="card-header sales-chart card-no-border">
            <h5 class="f-w-700"><i class="fa fa-bell-o"></i> Pembayaran Invoice</h5>
          </div>
          <div class="card-body pt-0">
              <div class="row m-2">
                <div class="col-xl-12 col-md-12">
                  <canvas id="prcPaymentChart" height="100" style="height: 225px !important;"></canvas>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- jumlah proyek dan % Realisasi Proyek-->
  <div class="row size-column">
    <div class="row g-1">
      <div class="col-xl-6 col-md-6 box-col-6">
        <div class="card">
          <div class="card-header sales-chart card-no-border">
            <h5 class="f-w-700">Jumlah Proyek</h5>
          </div>
          <div class="card-body pt-0">
              <div class="row m-2">
                <div class="col-xl-12 col-md-12">
                  <canvas id="countProjectChart" height="100" style="height: 200px !important;"></canvas>
                </div>
              </div>
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-md-6 box-col-6">
        <div class="card">
          <div class="card-header sales-chart card-no-border">
            <h5 class="f-w-700">% Realisasi Biaya</h5>
          </div>
          <div class="card-body pt-0">
              <div class="row m-2">
                <div class="col-xl-12 col-md-12">
                  <canvas id="prsRealAmtChart" height="100" style="height: 200px !important;"></canvas>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- pendapatan & pembayaran proyek -->
  <div class="row size-column">
    <div class="row g-1">
      <div class="col-xl-12 col-md-12 box-col-12">
        <div class="card">
          <div class="card-header sales-chart card-no-border">
            <h5 class="f-w-700">Pendapatan & Pembayaran Proyek</h5>
          </div>
          <div class="card-body pt-0">
              <div class="row m-10">
                <div class="col-xl-12 col-md-12">
                  <canvas id="revenueChart" height="350" style="height: 350px !important;"></canvas>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- pendapatan & pembayaran proyek -->
  <div class="row size-column">
    <div class="row g-1">
      <div class="col-xl-12 col-md-12 box-col-12">
        <div class="card">
          <div class="card-header sales-chart card-no-border">
            <h5 class="f-w-700">Anggaran & Biaya Proyek</h5>
          </div>
          <div class="card-body pt-0">
              <div class="row m-10">
                <div class="col-xl-12 col-md-12">
                  <canvas id="budgetChart" height="350" style="height: 350px !important;"></canvas>
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