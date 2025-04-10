<?php

namespace App\Controllers\Rencana;

use App\Controllers\BaseController;
use Config\Session;

class Rencana extends BaseController
{
  // Parent Construct
  public function __construct() {}

  public function verifikasi()
  {
    // Ambil username dari session
    $username = session()->get('username');
    $grp_prod = session()->get('group_id');

    $data = [
      'title' => "Verifikasi Rencana Kunjungan",
      'data_salesmarketing' => $this->salesMarketingModel->getSalesMarketingCab($username),
      'subgroup_barang' => $this->kelasProdModel->getSubGrupBarang($grp_prod),
      'validation' => $this->validation,
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('rencana/verifikasi', $data);
  }

  // Data verifikasi rencana kunjungan
  public function dataVerifRencana()
  {
      //filter data verifikasi rencana kunjungan
      $nik = $this->request->getPost('sales_marketing');
      $tanggal = $this->request->getPost('tanggal');
      $grp_id = $this->request->getPost('grp_prod');
      $subgrp_id = $this->request->getPost('subgrp_prod');
      $clsgrp_id = $this->request->getPost('klsgrp_prod');

      $data = $this->rencanaKunjModel->getDataVerifikasiRencana($nik, $tanggal, $grp_id, $subgrp_id, $clsgrp_id);
      echo json_encode($data);
  }

  public function updateVerifikasi()
  {
      $id = $this->request->getPost('id');
      $flg_approve = $this->request->getPost('flg_approve');
      $reason_reject = $this->request->getPost('reason_reject') ?? '';

      // Ambil username dari session
      $username = $this->session->get('username');

      $data = [
      'flg_approve' => $flg_approve,
          'reason_reject' => $reason_reject,
          'user_update' => $username,
          'update_date' => date('Y-m-d H:i:s'), // Format timestamp
          'user_approve' => $username,
          'date_approve' => date('Y-m-d H:i:s') // Format timestamp
      ];

      $update = $this->rencanaKunjModel->updateRencana($id, $data);

      if ($update) {
          $message = $flg_approve
              ? "Rencana kunjungan ID $id berhasil disetujui."
              : "Rencana kunjungan ID $id ditolak dengan alasan: $reason_reject.";

          return $this->response->setJSON(['status' => 'success', 'message' => $message]);
      } else {
          return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data.']);
      }
  }

  public function monitoring()
  {
    // Ambil username dari session
    $username = session()->get('username');
    $grp_prod = session()->get('group_id');

    $data = [
      'title' => "Monitoring Rencana Kunjungan",
      'data_salesmarketing' => $this->salesMarketingModel->getSalesMarketingCab($username),
      'subgroup_barang' => $this->kelasProdModel->getSubGrupBarang($grp_prod),
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('rencana/monitoring', $data);
  }

  // Data monitoring rencana kunjungan
  public function dataMonitoringRencana()
  {
      //filter data monitoring rencana kunjungan
      $nik = $this->request->getPost('sales_marketing');
      $tanggal_1 = $this->request->getPost('tanggal_1');
      $tanggal_2 = $this->request->getPost('tanggal_2');
      $grp_id = $this->request->getPost('grp_prod');
      $subgrp_id = $this->request->getPost('subgrp_prod');
      $clsgrp_id = $this->request->getPost('klsgrp_prod');

      $data = $this->rencanaKunjModel->getDataMonitoringRencana($nik, $tanggal_1, $tanggal_2, $grp_id, $subgrp_id, $clsgrp_id);
      echo json_encode($data);
  }
}
