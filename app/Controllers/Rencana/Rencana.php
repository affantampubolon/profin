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

  // Data detail verifikasi rencana kunjungan
  public function dataVerifRencanaDet()
  {
      //filter data detail verifikasi rencana kunjungan
      $nik = $this->request->getPost('sales_marketing');
      $tanggal = $this->request->getPost('tanggal');
      $pelanggan = $this->request->getPost('cust_id');

      $data = $this->rencanaKunjModel->getDataVerifikasiRencanaDet($nik, $tanggal, $pelanggan);
      echo json_encode($data);
  }

  public function updateVerifikasi()
  {
    $cust_id = $this->request->getPost('cust_id');
    $nik = $this->request->getPost('nik');
    $date = $this->request->getPost('date');
    $flg_approve = $this->request->getPost('flg_approve');
    $reason_reject = $this->request->getPost('reason_reject') ?? '';

    // Ambil username dari session
    $username = $this->session->get('username');

    $data = [
        'flg_approve' => $flg_approve,
        'reason_reject' => $reason_reject,
        'user_update' => $username,
        'update_date' => date('Y-m-d H:i:s'),
        'user_approve' => $username,
        'date_approve' => date('Y-m-d H:i:s')
    ];

    $update = $this->rencanaKunjModel->updateRencana($cust_id, $nik, $date, $data);

    if ($update) {
        $message = $flg_approve
            ? "Rencana kunjungan untuk cust_id: $cust_id, nik: $nik, date: $date berhasil disetujui."
            : "Rencana kunjungan untuk cust_id: $cust_id, nik: $nik, date: $date ditolak dengan alasan: $reason_reject.";

        return $this->response->setJSON(['status' => 'success', 'message' => $message]);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data.']);
    }
  }

  // Update verifikasi untuk multiple kombinasi
  public function updateVerifikasiAll()
  {
      $combinations = json_decode($this->request->getPost('combinations'), true);
      $flg_approve = $this->request->getPost('flg_approve');
      $reason_reject = $this->request->getPost('reason_reject') ?? '';

      // Ambil username dari session
      $username = $this->session->get('username');

      $data = [
          'flg_approve' => $flg_approve,
          'reason_reject' => $reason_reject,
          'user_update' => $username,
          'update_date' => date('Y-m-d H:i:s'),
          'user_approve' => $username,
          'date_approve' => date('Y-m-d H:i:s')
      ];

      $success = true;
      foreach ($combinations as $combination) {
          $cust_id = $combination['cust_id'];
          $nik = $combination['nik'];
          $date = $combination['date'];
          $update = $this->rencanaKunjModel->updateRencana($cust_id, $nik, $date, $data);
          if (!$update) {
              $success = false;
          }
      }

      if ($success) {
          return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil diperbarui.']);
      } else {
          return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui sebagian atau seluruh data.']);
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

  // Data detail monitoring rencana kunjungan
  public function dataMonitoringRencanaDet()
  {
      //filter data detail monitoring rencana kunjungan
      $nik = $this->request->getPost('sales_marketing');
      $tanggal = $this->request->getPost('tanggal');
      $pelanggan = $this->request->getPost('cust_id');

      $data = $this->rencanaKunjModel->getDataMonitoringRencanaDet($nik, $tanggal, $pelanggan);
      echo json_encode($data);
  }
}
