<?php

namespace App\Controllers\Izin;

use App\Controllers\BaseController;

class Izin extends BaseController
{
  // Parent Construct
  public function __construct() {}

  public function verifikasi()
  {
    $data = [
      'title' => "Verifikasi Izin",
      'breadcrumb' => $this->breadcrumb
    ];
    return view('izin/verifikasi', $data);
  }
}
