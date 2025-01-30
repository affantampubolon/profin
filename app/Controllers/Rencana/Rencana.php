<?php

namespace App\Controllers\Rencana;

use App\Controllers\BaseController;

class Rencana extends BaseController
{
  // Parent Construct
  public function __construct() {}

  public function verifikasi()
  {
    $data = [
      'title' => "Verifikasi Rencana Kunjungan",
      'breadcrumb' => $this->breadcrumb
    ];
    return view('rencana/verifikasi', $data);
  }

  public function monitoring()
  {
    $data = [
      'title' => "Monitoring Rencana Kunjungan",
      'breadcrumb' => $this->breadcrumb
    ];
    return view('rencana/monitoring', $data);
  }
}
