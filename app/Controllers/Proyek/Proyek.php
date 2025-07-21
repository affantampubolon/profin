<?php

namespace App\Controllers\Proyek;

use App\Controllers\BaseController;

class Proyek extends BaseController
{
  // Parent Construct
  public function __construct() {}

  public function registrasiindex()
  {
    $data = [
      'title' => "Registrasi Proyek",
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('proyek/registrasi', $data);
  }

  // Data proyek filter utama
  public function dataFilterProyek()
  {

      $data = $this->proyekModel->getProyekFilter();
      echo json_encode($data);
  }

  // Data proyek
  public function dataProyekPembayaranFilter()
  {   
      $term = $this->request->getGet('term'); // Ambil parameter 'term' dari query string
      $builder = $this->db->table('trn_job_project')
          ->select('id AS value, wbs_no AS label')->where('flg_used', 't'); // Sesuaikan dengan struktur tabel Anda
      if ($term) {
          $builder->like('wbs_no', $term); // Filter berdasarkan wbs_no
      }
      $builder->where('flg_used', 't');
      $data = $builder->get()->getResultArray();

      return $this->response->setJSON($data); // Mengembalikan data dalam format JSON
  }

  // Data proyek
  public function dataProyekAnggaranFilter()
  {

      $data = $this->proyekModel->getProyekAnggaranFilter();
      echo json_encode($data);
  }

  // Data proyek
  public function dataProyekRealisasiFilter()
  {   
      $term = $this->request->getGet('term'); // Ambil parameter 'term' dari query string
      $builder = $this->db->table('trn_job_project')
          ->select('id AS value, wbs_no AS label')->where('flg_used', 't')
          ->whereIn('id', function ($builder) {
              $builder->select('id_ref')
                      ->from('trn_cost_plan')
                      ->where('id_ref IS NOT NULL');
          }); // Sesuaikan dengan struktur tabel Anda
      if ($term) {
          $builder->like('wbs_no', $term); // Filter berdasarkan wbs_no
      }
      $builder->where('flg_used', 't');
      $data = $builder->get()->getResultArray();

      return $this->response->setJSON($data); // Mengembalikan data dalam format JSON
  }

  public function insertProyek()
  { 
      // Ambil data dari form
      $nowbs = $this->request->getPost('nowbs');
      $noso = $this->request->getPost('noso');
      $jobname = $this->request->getPost('jobname');
      $companyname = $this->request->getPost('companyname');
      $companyaddress = $this->request->getPost('companyaddress');
      $companypic = $this->request->getPost('companypic');
      $telpno = $this->request->getPost('telpno');
      $email = $this->request->getPost('email');
      $joblocation = $this->request->getPost('joblocation');
      $projectmanager = $this->request->getPost('projectmanager');
      $inspector = $this->request->getPost('inspector');
      $reportno = $this->request->getPost('reportno');
      $contractamt = $this->request->getPost('contractamt');
      $revenueamt = $this->request->getPost('revenueamt');
      $costplanamt = $this->request->getPost('costplanamt');

      // Ambil username dari session
      $username = $this->session->get('username');

      // Aturan validasi
      $validationRules = [
          'nowbs' => 'required',
          'noso' => 'required',
          'jobname' => 'required',
          'companyname' => 'required',
          'companyaddress' => 'required',
          'companypic' => 'required',
          'telpno' => 'required|numeric',
        //   'email' => 'required|valid_email',
          'joblocation' => 'required',
          'projectmanager' => 'required',
          'inspector' => 'required',
          'reportno' => 'required',
          'contractamt' => 'required|numeric',
          'revenueamt' => 'numeric',
          'costplanamt' => 'required|numeric',
      ];

      // Validasi input
      if (!$this->validate($validationRules)) {
          // Jika validasi gagal, kembalikan ke form dengan pesan error
          return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
      }

      // Data untuk tabel trn_job_project
      $dataProyek = [
          'wbs_no'  => $nowbs,
          'so_no'  => $noso,
          'job_name'    => $jobname,
          'company_id'    => $companyname,
          'company_address'    => $companyaddress,
          'company_pic'    => $companypic,
          'hp_no'    => $telpno,
          'email'    => $email,
          'job_location'    => $joblocation,
          'project_manager'    => $projectmanager,
          'inspector'    => $inspector,
          'report_no'    => $reportno,
          'contract_amt'    => $contractamt,
          'ar_balance'    => $revenueamt,
          'revenue_amt'    => $revenueamt,
          'cost_plan_amt'    => $costplanamt,
          'user_create' => $username,
          'create_date' => date('Y-m-d H:i:s')
      ];

      // Insert ke tabel trn_job_project
      $this->proyekModel->insertProyek($dataProyek);

      // Redirect dengan pesan sukses
      return redirect()->to('/proyek/registrasi/index')->with('success', 'Data proyek berhasil disimpan');
  }

  public function pembaruandataindex()
  {
    $data = [
      'title' => "Pembaruan Data Proyek",
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('proyek/pembaruan_data', $data);
  }

  // Data proyek
  public function dataProyek()
  {

      $data = $this->proyekModel->getDataProyek();
      echo json_encode($data);
  }

  // data proyek dengan id
  public function dataProyekId($id)
  {
      $data = $this->proyekModel->getDataProyekId($id);
      return $this->response->setJSON($data);
  }

  public function updateDataProyek()
  {
      $id = $this->request->getPost('id');
      $jobstartdate = $this->request->getPost('jobstartdate');
      $jobenddate = $this->request->getPost('jobenddate');
      $jobtotaltime = $this->request->getPost('jobtotaltime');
      $invoicesenddate = $this->request->getPost('invoicesenddate');
      $invoicereceivedate = $this->request->getPost('invoicereceivedate');
      $invoicereceivename = $this->request->getPost('invoicereceivename');
      $progressjob = $this->request->getPost('progressjob');
      $revenueamt = $this->request->getPost('revenueamt');

      $username = $this->session->get('username');

      $data = [
          'job_start_date' => $jobstartdate,
          'job_finish_date' => $jobenddate,
          'job_tot_time' => $jobtotaltime,
          'invoice_send_date'    => $invoicesenddate,
          'invoice_receive_date'    => $invoicereceivedate,
          'invoice_receive_name'    => $invoicereceivename,
          'progress' => $progressjob,
          'ar_balance'    => $revenueamt,
          'revenue_amt' => $revenueamt,
          'user_update' => $username,
          'update_date' => date('Y-m-d H:i:s')
      ];

      $result = $this->proyekModel->updateProyek($id, $data);

      if ($result) {
          return $this->response->setJSON(['status' => 'success', 'message' => 'Data proyek berhasil diperbarui']);
      } else {
          return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data proyek']);
      }
  }

}
