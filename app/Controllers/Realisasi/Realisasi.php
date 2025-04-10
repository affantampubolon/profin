<?php

namespace App\Controllers\Realisasi;

use App\Controllers\BaseController;

class Realisasi extends BaseController
{
  // Parent Construct
  public function __construct() {}

  // Function mengambil branch_id berdasarkan session user
  public function getUserBranchSession()
  {
      $session = session();
      return $this->response->setJSON(['branch_id' => $session->get('branch_id')]);
  }

  public function verifikasi()
  {
    // Ambil username dari session
    $username = session()->get('username');
    $grp_prod = session()->get('group_id');

    $data = [
      'title' => "Verifikasi Realisasi Kunjungan",
      'subgroup_barang' => $this->kelasProdModel->getSubGrupBarang($grp_prod),
      'validation' => $this->validation,
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('realisasi/verifikasi', $data);
  }

  // Data verifikasi realisasi kunjungan
  public function dataVerifRealisasi()
  {
      //filter data verifikasi realisasi kunjungan
      $tanggal = $this->request->getPost('tanggal');
      $cabang = $this->request->getPost('cabang');
      $grp_id = $this->request->getPost('grp_prod');
      $subgrp_id = $this->request->getPost('subgrp_prod');
      $clsgrp_id = $this->request->getPost('klsgrp_prod');

      $data = $this->realisasiKunjModel->getDataVerifikasiRealisasi($tanggal, $cabang, $grp_id, $subgrp_id, $clsgrp_id);
      echo json_encode($data);
  }

  public function updateVerifikasi()
  {
      // Log request untuk debugging
      log_message('debug', 'Request Data: ' . json_encode($this->request->getPost()));

      $ids = $this->request->getPost('ids');
      $flg_verify = $this->request->getPost('flg_verify');
      $feedback = $this->request->getPost('feedback') ?? '';

      // Validasi input
      if (empty($ids) || !is_array($ids)) {
          return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak valid atau kosong']);
      }

      $username = $this->session->get('username');
      if (!$username) {
          return $this->response->setJSON(['status' => 'error', 'message' => 'Session username tidak ditemukan']);
      }

      // Sesuaikan flg_verify dengan tipe data di database (misalnya 't' untuk PostgreSQL)
      $data = [
          'flg_verify' => $flg_verify, // Sesuaikan dengan tipe data Anda
          'feedback' => $feedback,
          'user_update' => $username,
          'update_date' => date('Y-m-d H:i:s'),
          'user_verify' => $username,
          'date_verify' => date('Y-m-d H:i:s')
      ];

      $successCount = 0;
      foreach ($ids as $id) {
          try {
              $update = $this->realisasiKunjModel->updateRealisasi($id, $data);
              if ($update) {
                  $successCount++;
              }
          } catch (\Exception $e) {
              log_message('error', 'Update Error for ID ' . $id . ': ' . $e->getMessage());
          }
      }

      if ($successCount === count($ids)) {
          return $this->response->setJSON(['status' => 'success', 'message' => 'Semua data kunjungan berhasil diverifikasi']);
      } else {
          return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui beberapa data']);
      }
  }

  public function monitoring()
  {
    // Ambil username dari session
    $username = session()->get('username');
    $grp_prod = session()->get('group_id');

    $data = [
      'title' => "Monitoring Realisasi Kunjungan",
      'data_salesmarketing' => $this->salesMarketingModel->getSalesMarketingCab($username),
      'subgroup_barang' => $this->kelasProdModel->getSubGrupBarang($grp_prod),
      'validation' => $this->validation,
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('realisasi/monitoring', $data);
  }

  // Data monitoring realisasi kunjungan
  public function dataMonitoringRealisasi()
  {
      //filter data monitoring realisasi kunjungan
      $nik = $this->request->getPost('sales_marketing');
      $tanggal_1 = $this->request->getPost('tanggal_1');
      $tanggal_2 = $this->request->getPost('tanggal_2');
      $grp_id = $this->request->getPost('grp_prod');
      $subgrp_id = $this->request->getPost('subgrp_prod');
      $clsgrp_id = $this->request->getPost('klsgrp_prod');

      $data = $this->realisasiKunjModel->getDataMonitoringRealisasi($nik, $tanggal_1, $tanggal_2, $grp_id, $subgrp_id, $clsgrp_id);
      echo json_encode($data);
  }
}
