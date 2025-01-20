<?php

namespace App\Controllers\Realisasi;

use App\Controllers\BaseController;

class Realisasi extends BaseController
{
  // Parent Construct
  public function __construct() {}

  public function verifikasi()
  {
    $data = [
      'title' => "Verifikasi Realisasi Kunjungan",
      'breadcrumb' => $this->breadcrumb
    ];
    return view('realisasi/verifikasi', $data);
  }

  public function monitoring()
  {
    $data = [
      'title' => "Monitoring Realisasi Kunjungan",
      'breadcrumb' => $this->breadcrumb
    ];
    return view('realisasi/monitoring', $data);
  }
}
