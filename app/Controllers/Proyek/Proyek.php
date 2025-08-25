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
        $jobcategory = $this->request->getPost('jobcategory');
        $jobname = $this->request->getPost('jobname');
        $companyname = $this->request->getPost('companyname');
        $companyaddress = $this->request->getPost('companyaddress');
        $companypic = $this->request->getPost('companypic');
        $telpno = $this->request->getPost('telpno');
        $email = $this->request->getPost('email');
        $joblocation = $this->request->getPost('joblocation');
        $projectmanager = $this->request->getPost('projectmanager');
        $inspector = $this->request->getPost('inspector'); // Array dalam bentuk JSON string
        $contractamt = $this->request->getPost('contractamt');
        $revenueamt = $this->request->getPost('revenueamt');
        $costplanamt = $this->request->getPost('costplanamt');

        // Ambil username dari session
        $username = $this->session->get('username');

        // Aturan validasi
        $validationRules = [
            'jobcategory' => 'required',
            'jobname' => 'required',
            'companyname' => 'required',
            'companyaddress' => 'required',
            'companypic' => 'required',
            'telpno' => 'permit_empty|numeric', // Ubah menjadi permit_empty jika null diperbolehkan
            'joblocation' => 'required',
            'projectmanager' => 'required',
            'inspector' => 'required', // Validasi bahwa ada setidaknya satu inspector
            'contractamt' => 'required|numeric',
            'revenueamt' => 'numeric',
            'costplanamt' => 'required|numeric',
        ];

        // Validasi input
        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal: ' . json_encode($this->validator->getErrors())
            ]);
        }

        // Konversi inspector dari JSON string ke array (jika perlu), lalu simpan sebagai string
        $inspectorString = is_string($inspector) ? $inspector : json_encode($inspector);

        // Data untuk tabel trn_job_project
        $dataProyek = [
            'wbs_no' => $nowbs ?: '0', // Jika $nowbs kosong (null atau ''), ganti dengan '0'
            'so_no' => $noso ?: '0',   // Jika $noso kosong (null atau ''), ganti dengan '0'
            'job_category' => $jobcategory,
            'job_name' => $jobname,
            'company_id' => $companyname,
            'company_address' => $companyaddress,
            'company_pic' => $companypic,
            'hp_no' => $telpno ?: null, // Konversi kosong menjadi NULL
            'email' => $email ?: null,
            'job_location' => $joblocation,
            'project_manager' => $projectmanager,
            'inspector' => $inspectorString, // Simpan sebagai string JSON
            'report_no' => '', // Tambahkan logika jika report_no diperlukan
            'contract_amt' => $contractamt,
            'ar_balance' => $revenueamt,
            'revenue_amt' => $revenueamt,
            'cost_plan_amt' => $costplanamt,
            'user_create' => $username,
            'create_date' => date('Y-m-d H:i:s')
        ];

        try {
            // Insert ke tabel trn_job_project
            $result = $this->proyekModel->insertProyek($dataProyek);

            if ($result) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Data proyek berhasil disimpan']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data proyek']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Insert Proyek Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ]);
        }
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
    $nowbs = $this->request->getPost('nowbs');
    $noso = $this->request->getPost('noso');
    $reportno = $this->request->getPost('reportno');
    $jobstartdate = $this->request->getPost('jobstartdate');
    $jobenddate = $this->request->getPost('jobenddate');
    $jobtotaltime = $this->request->getPost('jobtotaltime');
    $invoicesenddate = $this->request->getPost('invoicesenddate');
    $invoicereceivedate = $this->request->getPost('invoicereceivedate');
    $invoicereceivename = $this->request->getPost('invoicereceivename');
    $progressjob = $this->request->getPost('progressjob');
    $revenueamt = $this->request->getPost('revenueamt');

    $username = $this->session->get('username');

    // Ambil data lama dari database
    $oldData = $this->proyekModel->getDataProyekId($id);
    if (!$oldData) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Data proyek tidak ditemukan']);
    }

    // Siapkan data untuk update, gunakan nilai lama jika tidak ada pembaruan
    $data = [
        'wbs_no' => $nowbs ?: $oldData->wbs_no,
        'so_no' => $noso ?: $oldData->so_no,
        'report_no' => $reportno ?: $oldData->report_no,
        'job_start_date' => ($jobstartdate === null || $jobstartdate === '') ? $oldData->job_start_date : $jobstartdate,
        'job_finish_date' => ($jobenddate === null || $jobenddate === '') ? $oldData->job_finish_date : $jobenddate,
        'job_tot_time' => $jobtotaltime ?: $oldData->job_tot_time,
        'invoice_send_date' => ($invoicesenddate === null || $invoicesenddate === '') ? $oldData->invoice_send_date : $invoicesenddate,
        'invoice_receive_date' => ($invoicereceivedate === null || $invoicereceivedate === '') ? $oldData->invoice_receive_date : $invoicereceivedate,
        'invoice_receive_name' => $invoicereceivename ?: $oldData->invoice_receive_name,
        'progress' => $progressjob ?: ($oldData->progress ?? 0),
        'ar_balance' => $revenueamt ?: $oldData->ar_balance,
        'revenue_amt' => $revenueamt ?: $oldData->revenue_amt,
        'user_update' => $username ?: $oldData->user_update,
        'update_date' => date('Y-m-d H:i:s')
    ];

    // Penanganan unggahan file PDF
    //File SPK
    $fileSpk = $this->request->getFile('fileSpk');
    log_message('debug', 'File detected: ' . ($fileSpk ? 'Yes' : 'No') . ', Is Valid: ' . ($fileSpk ? ($fileSpk->isValid() ? 'Yes' : 'No') : 'N/A'));
    if ($fileSpk && $fileSpk->isValid() && !$fileSpk->hasMoved()) {
        // Validasi format dan ukuran file
        $fileType = $fileSpk->getClientMimeType();
        $fileSize = $fileSpk->getSize();

        log_message('debug', 'File Type: ' . $fileType . ', File Size: ' . $fileSize . ' bytes');
        if ($fileType !== 'application/pdf') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File yang diunggah bukan dalam format .pdf']);
        }

        if ($fileSize > 2.5 * 1024 * 1024) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File melebihi kapasitas 2,5 MB']);
        }

        // Pastikan direktori ada
        $uploadPath = WRITEPATH . 'uploads/spk';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        // Simpan file
        $fileName = $fileSpk->getRandomName();
        $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
        if ($fileSpk->move($uploadPath, $fileName)) {
            $data['file_spk'] = $fileName; // Simpan nama file ke database
            log_message('debug', 'File saved successfully: ' . $fullPath);
        } else {
            log_message('error', 'Failed to move file: ' . $fileSpk->getErrorString());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan file: ' . $fileSpk->getErrorString()]);
        }
    } elseif (isset($oldData->file_spk)) {
        $data['file_spk'] = $oldData->file_spk; // Pertahankan file lama jika tidak ada unggahan baru
    }

    //File Laporan
    // Penanganan unggahan file PDF
    $fileLaporan = $this->request->getFile('fileLaporan');
    log_message('debug', 'File detected: ' . ($fileLaporan ? 'Yes' : 'No') . ', Is Valid: ' . ($fileLaporan ? ($fileLaporan->isValid() ? 'Yes' : 'No') : 'N/A'));
    if ($fileLaporan && $fileLaporan->isValid() && !$fileLaporan->hasMoved()) {
        // Validasi format dan ukuran file
        $fileType = $fileLaporan->getClientMimeType();
        $fileSize = $fileLaporan->getSize();

        log_message('debug', 'File Type: ' . $fileType . ', File Size: ' . $fileSize . ' bytes');
        if ($fileType !== 'application/pdf') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File yang diunggah bukan dalam format .pdf']);
        }

        if ($fileSize > 2.5 * 1024 * 1024) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File melebihi kapasitas 2,5 MB']);
        }

        // Pastikan direktori ada
        $uploadPath = WRITEPATH . 'uploads/laporan';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        // Simpan file
        $fileName = $fileLaporan->getRandomName();
        $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
        if ($fileLaporan->move($uploadPath, $fileName)) {
            $data['file_spk'] = $fileName; // Simpan nama file ke database
            log_message('debug', 'File saved successfully: ' . $fullPath);
        } else {
            log_message('error', 'Failed to move file: ' . $fileLaporan->getErrorString());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan file: ' . $fileLaporan->getErrorString()]);
        }
    } elseif (isset($oldData->file_laporan)) {
        $data['file_laporan'] = $oldData->file_laporan; // Pertahankan file lama jika tidak ada unggahan baru
    }


    // Validasi data sebelum update
    if (!$id) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak ditemukan']);
    }

    $result = $this->proyekModel->updateProyek($id, $data);

    if ($result) {
        return $this->response->setJSON(['status' => 'success', 'message' => 'Data proyek berhasil diperbarui']);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data proyek']);
    }
}

}
