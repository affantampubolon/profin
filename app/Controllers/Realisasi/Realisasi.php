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

    $data = [
      'title' => "Verifikasi Realisasi Kunjungan",
      'data_salesmarketing' => $this->salesMarketingModel->getSalesMarketingCab($username),
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
      $nik = $this->request->getPost('sales_marketing');
      $tanggal = $this->request->getPost('tanggal');

      $data = $this->realisasiKunjModel->getDataVerifikasiRealisasi($nik, $tanggal);
      echo json_encode($data);
  }

  // Data detail verifikasi realisasi kunjungan
  public function dataVerifRealisasiDet()
  {
      //filter data detail verifikasi realisasi kunjungan
      $nik = $this->request->getPost('sales_marketing');
      $tanggal = $this->request->getPost('tanggal');
      $pelanggan = $this->request->getPost('cust_id');

      $data = $this->realisasiKunjModel->getDataVerifikasiRealisasiDet($nik, $tanggal, $pelanggan);
      echo json_encode($data);
  }

  public function updateVerifikasi()
  {
      // Log request untuk debugging
    log_message('debug', 'Request Data: ' . json_encode($this->request->getPost()));

    $date = $this->request->getPost('date');
    $nik = $this->request->getPost('nik');
    $cust_id = $this->request->getPost('cust_id');
    $flg_verify = $this->request->getPost('flg_verify');
    $feedback = $this->request->getPost('feedback') ?? '';

    // Validasi input
    if (empty($date) || empty($nik) || empty($cust_id)) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter date, nik, atau cust_id kosong']);
    }

    $username = $this->session->get('username');
    if (!$username) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Session username tidak ditemukan']);
    }

    // Data yang akan diupdate
    $data = [
        'flg_verify' => $flg_verify,
        'feedback' => $feedback,
        'user_update' => $username,
        'update_date' => date('Y-m-d H:i:s'),
        'user_verify' => $username,
        'date_verify' => date('Y-m-d H:i:s')
    ];

    // Panggil model untuk update berdasarkan kombinasi date, nik, cust_id
    $update = $this->realisasiKunjModel->updateRealisasi($date, $nik, $cust_id, $data);

    if ($update) {
        return $this->response->setJSON(['status' => 'success', 'message' => 'Data kunjungan berhasil diverifikasi']);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data']);
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

      $data = $this->realisasiKunjModel->getDataMonitoringRealisasi($nik, $tanggal_1, $tanggal_2);
      echo json_encode($data);
  }

  // Data detail monitoring realisasi kunjungan
  public function dataMonitoringRealisasiDet()
  {
      //filter data detail monitoring realisasi kunjungan
      $nik = $this->request->getPost('sales_marketing');
      $tanggal = $this->request->getPost('tanggal');
      $pelanggan = $this->request->getPost('cust_id');

      $data = $this->realisasiKunjModel->getDataMonitoringRealisasiDet($nik, $tanggal, $pelanggan);
      echo json_encode($data);
  }

  public function bukaVerifikasi()
  {
    // Ambil username dari session
    $username = session()->get('username');

    $data = [
      'title' => "Buka Verifikasi Realisasi Kunjungan",
      'data_salesmarketing' => $this->salesMarketingModel->getSalesMarketingCab($username),
      'validation' => $this->validation,
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('realisasi/buka_verifikasi', $data);
  }

  // Data buka verifikasi realisasi kunjungan
  public function dataBukaVerifRealisasi()
  {
      //filter data verifikasi realisasi kunjungan
      $nik = $this->request->getPost('sales_marketing');
      $tanggal = $this->request->getPost('tanggal');

      $data = $this->realisasiKunjModel->getDataBukaVerifikasiRealisasi($nik, $tanggal);
      echo json_encode($data);
  }

  // Data detail buka verifikasi realisasi kunjungan
  public function dataBukaVerifRealisasiDet()
  {
      //filter data detail verifikasi realisasi kunjungan
      $nik = $this->request->getPost('sales_marketing');
      $tanggal = $this->request->getPost('tanggal');
      $pelanggan = $this->request->getPost('cust_id');

      $data = $this->realisasiKunjModel->getDataBukaVerifikasiRealisasiDet($nik, $tanggal, $pelanggan);
      echo json_encode($data);
  }

  public function updateBukaVerifikasi()
  {
    $cust_id = $this->request->getPost('cust_id');
    $nik = $this->request->getPost('nik');
    $date = $this->request->getPost('date');
    $status = $this->request->getPost('status');

    // Ambil username dari session
    $username = $this->session->get('username');

    // Data yang akan diupdate
    $data = [
        'status' => $status,
        'user_update' => $username,
        'update_date' => date('Y-m-d H:i:s')
    ];

    // Panggil model untuk update berdasarkan kombinasi date, nik, cust_id
    $update = $this->realisasiKunjModel->updateBukaVerifRealisasi($cust_id, $nik, $date, $data);

    if ($update) {
        return $this->response->setJSON(['status' => 'success', 'message' => $message]);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data']);
    }
  }

  // Update verifikasi untuk multiple kombinasi
  public function updateBukaVerifikasiAll()
  {
      $combinations = json_decode($this->request->getPost('combinations'), true);
      $status = $this->request->getPost('status');

      // Ambil username dari session
      $username = $this->session->get('username');

      $data = [
          'status' => $status,
          'user_update' => $username,
          'update_date' => date('Y-m-d H:i:s'),
      ];

      $success = true;
      foreach ($combinations as $combination) {
          $cust_id = $combination['cust_id'];
          $nik = $combination['nik'];
          $date = $combination['date'];
          $update = $this->realisasiKunjModel->updateBukaVerifRealisasi($cust_id, $nik, $date, $data);
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
}
