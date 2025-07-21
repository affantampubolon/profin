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

  // Data grafik pembayaran invoice (persentase pembayaran)
  public function dataGraphicPayment()
  {
      $year = $this->request->getPost('tahun');
      $project_manager = $this->request->getPost('pmfilter');

      $data = $this->berandaModel->getGraphicPayment($year, $project_manager);
      echo json_encode($data);
  }

  // Data grafik proyek (pendapatan dan pembayaran)
  public function dataGraphicProject()
  {
      $year = $this->request->getPost('tahun');
      $project_manager = $this->request->getPost('pmfilter');

      $data = $this->berandaModel->getGraphicProject($year, $project_manager);
      echo json_encode($data);
  }

  // Data grafik proyek (anggaran dan realisasi)
  public function dataGraphicBudgetProject()
  {
      $year = $this->request->getPost('tahun');
      $project_manager = $this->request->getPost('pmfilter');

      $data = $this->berandaModel->getGraphicBudgetProject($year, $project_manager);
      echo json_encode($data);
  }
    
}
