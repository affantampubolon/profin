<?php

namespace App\Controllers\Beranda;

use App\Controllers\BaseController;

class Beranda extends BaseController
{
  // Parent Construct
  public function __construct() {}

  // Function mengambil branch_id berdasarkan session user
  public function getUserBranchSession()
  {
      $session = session();
      return $this->response->setJSON(['branch_id' => $session->get('branch_id')]);
  }

  // Data untuk gauge chart pencapaian cab
  public function dataPencapaianCab()
  {
      $cabang = session()->get('branch_id');

      $data = $this->berandaModel->getDataPencapaianCab($cabang);
      echo json_encode($data);
  }

  // Data daftar verifikasi tertunda per cabang
  public function dataVerifTertundaCab()
  {
      $cabang = session()->get('branch_id');

      $data = $this->berandaModel->getDataVerifikasiTertundaCab($cabang);
      echo json_encode($data);
  }
    
}
