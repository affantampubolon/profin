<?php

namespace App\Controllers\Monitoring;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Monitoring extends BaseController
{
  // Parent Construct
  public function __construct() {}

  //DETAIL PROYEK
  public function detproyekindex()
  {
    $data = [
      'title' => "Detail Proyek",
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('monitoring/detailproyek', $data);
  }

  // Data detail proyek untuk download Excel
  public function dataUnduhDetProyek()
  {
      $tahun = $this->request->getGet('tahun'); // Mengambil parameter dari GET

      try {
          if (empty($tahun)) {
              $tahun = date('Y'); // Gunakan tahun berjalan jika kosong
          }

          $data = $this->monitoringModel->getDetProyek($tahun);

          // Buat objek Spreadsheet
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();

          // Definisikan header kolom yang user-friendly
          $headers = [
              'ID', 'Tahun', 'Bulan', 'No. SPI', 'No. WBS', 'No. SO', 'Nama Pekerjaan', 'ID Perusahaan', 'Nama Perusahaan',
              'Alamat Perusahaan', 'Nama PIC', 'No. HP', 'Email', 'Lokasi Pekerjaan', 'Project Manager',
              'Inspektur', 'No. Laporan', 'Tgl Kirim Inv', 'Tgl Terima Inv',
              'Nama Penerima Inv', 'Tgl Mulai Pekerjaan', 'Tgl Selesai Pekerjaan', 'Total Waktu Pekerjaan', 'Nilai Kontrak',
              'Nilai Pendapatan', 'Nilai Rencana Biaya', 'Nilai Anggaran', 'Nilai Realisasi Biaya', '(%) Biaya', 'Saldo Piutang', 'Nilai Pembayaran', 'Jml Pembayaran Terakhir', 'Tgl Pembayaran Terakhir', '(%) Pembayaran', 'Status Pembayaran', 'Kendala Pembayaran',
              'Progres (%)', 'Status', 'User Pembuat Proyek',
              'Tgl Pembuatan Proyek', 'User Update Proyek', 'Tgl Update Proyek', 'Nilai Permintaan Dropping', 'Nilai Realisasi Dropping', 'Nilai Sisa Anggaran',
              'User Update Anggaran', 'Tgl Update Anggaran', 'User Update Realisasi', 'Tgl Update Realisasi',
              'User Update Dropping', 'Tgl Update Dropping'
          ];

          // Isi header
          $sheet->fromArray($headers, NULL, 'A1');

          // Isi data
          $rowData = [];
          foreach ($data as $row) {
              $rowData[] = [
                  $row->id, $row->year, $row->month, $row->no_spi, $row->wbs_no, $row->so_no, $row->job_name,
                  $row->company_id, $row->company_name, $row->company_address, $row->company_pic, $row->hp_no,
                  $row->email, $row->job_location, $row->pm_name,
                  $row->insp_name, $row->report_no, $row->invoice_send_date,
                  $row->invoice_receive_date, $row->invoice_receive_name, $row->job_start_date,
                  $row->job_finish_date, $row->job_tot_time, $row->contract_amt, $row->revenue_amt,
                  $row->cost_plan_amt, $row->budget_amt, $row->real_amt, $row->prs_achiev, 
                  $row->ar_balance, $row->payment_amt, $row->last_payment_amt, $row->last_payment_date, $row->prs_payment, $row->status_payment, $row->reason,
                  $row->progress, $row->progress_name,
                  $row->user_create_project, $row->create_date_project, $row->user_update_project,
                  $row->update_date_project,
                  $row->req_drop_amt, $row->real_drop_amt, $row->net_plan_amt,
                  $row->emp_name_budget, $row->create_date_budget, $row->emp_name_real,
                  $row->create_date_real, $row->emp_name_drop, $row->create_date_drop
              ];
          }
          $sheet->fromArray($rowData, NULL, 'A2');

          // Set header respons
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment; filename="det_proyek_' . $tahun . '.xlsx"');
          header('Cache-Control: max-age=0');

          // Tulis file ke output
          $writer = new Xlsx($spreadsheet);
          $writer->save('php://output');
          exit; // Hentikan eksekusi setelah mengirim file
      } catch (\Exception $e) {
          log_message('error', 'Error generating Excel: ' . $e->getMessage());
          return $this->response->setStatusCode(500)->setJSON([
              'status' => 'error',
              'message' => 'Gagal menghasilkan file Excel: ' . $e->getMessage()
          ]);
      }
  }

  public function getSpkFile($fileName)
  {
      $filePath = WRITEPATH . 'uploads/spk/' . $fileName;

      if (file_exists($filePath) && is_file($filePath)) {
          // Set header untuk file PDF
          return $this->response
              ->setHeader('Content-Type', 'application/pdf')
              ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
              ->setHeader('Content-Length', filesize($filePath))
              ->setBody(file_get_contents($filePath));
      } else {
          return $this->response
              ->setStatusCode(404)
              ->setJSON(['status' => 'error', 'message' => 'File tidak ditemukan']);
      }
  }

  public function getLaporanFile($fileName)
  {
      $filePath = WRITEPATH . 'uploads/laporan/' . $fileName;

      if (file_exists($filePath) && is_file($filePath)) {
          // Set header untuk file PDF
          return $this->response
              ->setHeader('Content-Type', 'application/pdf')
              ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
              ->setHeader('Content-Length', filesize($filePath))
              ->setBody(file_get_contents($filePath));
      } else {
          return $this->response
              ->setStatusCode(404)
              ->setJSON(['status' => 'error', 'message' => 'File tidak ditemukan']);
      }
  }

  // Data tabel detail proyek
  public function dataDetProyek()
  {
      $tahun = $this->request->getPost('tahun');

      $data = $this->monitoringModel->getDetProyek($tahun);
      echo json_encode($data);
  }

  // data detail proyek dengan id
  public function dataDetProyekId($id)
  {
      $data = $this->monitoringModel->getDetProyekkId($id);
      return $this->response->setJSON($data);
  }

  //ANGGARAN DAN REALISASI BIAYA
  public function anggaranbiayaindex()
  {
    $data = [
      'title' => "Anggaran dan Realisasi Biaya",
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('monitoring/anggaranbiaya', $data);
  }

  // Data anggaran biaya untuk download Excel
  public function dataUnduhAnggaranBiaya()
  {
      $nowbs = $this->request->getGet('nowbs'); // Mengambil parameter dari GET

      try {
          $data = $this->monitoringModel->getDataAnggaranBiaya($nowbs);

          // Buat objek Spreadsheet
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();

          // Definisikan header kolom yang user-friendly
          $headers = [
              'No. Dokumen', 'ID ref', 'No. Wbs', 'Kode COA', 'COA', 'Nilai Anggaran', 'Nilai Realisasi Biaya', 'Nilai Realisasi Dropping', 'Nilai Sisa Anggaran'
          ];

          // Isi header
          $sheet->fromArray($headers, NULL, 'A1');

          // Isi data
          $rowData = [];
          foreach ($data as $row) {
              $rowData[] = [
                  $row->no_doc, $row->id_ref, $row->wbs_no, $row->coa, $row->coa_name, $row->budget_amt, $row->real_amt,
                  $row->real_drop_amt, $row->net_plan_amt
              ];
          }
          $sheet->fromArray($rowData, NULL, 'A2');

          // Set header respons
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment; filename="biaya_anggaran' . $nowbs . '.xlsx"');
          header('Cache-Control: max-age=0');

          // Tulis file ke output
          $writer = new Xlsx($spreadsheet);
          $writer->save('php://output');
          exit; // Hentikan eksekusi setelah mengirim file
      } catch (\Exception $e) {
          log_message('error', 'Error generating Excel: ' . $e->getMessage());
          return $this->response->setStatusCode(500)->setJSON([
              'status' => 'error',
              'message' => 'Gagal menghasilkan file Excel: ' . $e->getMessage()
          ]);
      }
  }

  // Data tabel anggaran biaya proyek
  public function dataAnggaranBiayaProyek()
  {
      $nowbs = $this->request->getPost('nowbs');

      $data = $this->monitoringModel->getDataAnggaranBiaya($nowbs);
      echo json_encode($data);
  }

  // Data tabel detail realisasi
  public function dataDetRealisasi()
  {
      $nowbs = $this->request->getPost('id_ref');
      $coa = $this->request->getPost('coa');

      $data = $this->monitoringModel-> getDataDetRealisasi($nowbs, $coa);
      echo json_encode($data);
  }

  // Data tabel detail dropping
  public function dataDetDropping()
  {
      $nowbs = $this->request->getPost('id_ref');
      $coa = $this->request->getPost('coa');

      $data = $this->monitoringModel-> getDataDetDropping($nowbs, $coa);
      echo json_encode($data);
  }

  //PEMBAYARAN PIUTANG
  public function pembayaranpiutangindex()
  {
    $data = [
      'title' => "Pembayaran Invoice",
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('monitoring/pembayaranpiutang', $data);
  }

  // Data pembayaran piutang untuk download Excel
  public function dataUnduhPembayaranPiutang()
  {
      $tahun = $this->request->getGet('tahun'); // Mengambil parameter dari GET

      try {
          if (empty($tahun)) {
              $tahun = date('Y'); // Gunakan tahun berjalan jika kosong
          }

          $data = $this->monitoringModel->getDataPembayaranPiutangDet($tahun);

          // Buat objek Spreadsheet
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();

          // Definisikan header kolom yang user-friendly
          $headers = [
              'tahun', 'bulan', 'No. WBS', 'No. SO', 'Perusahaan', 'Nama Pekerjaan', 'No. Pembayaran', 'Tgl Terbit Invoice', 'Tgl Pembayaran',
              'periode (hari)', 'uraian', 'kendala', 'nilai pembayaran', 'pembuat'
          ];

          // Isi header
          $sheet->fromArray($headers, NULL, 'A1');

          // Isi data
          $rowData = [];
          foreach ($data as $row) {
              $rowData[] = [
                  $row->year, $row->month, $row->wbs_no, $row->so_no, $row->company_name, $row->job_name, $row->no_doc,
                  $row->invoice_date, $row->payment_date, $row->period_payment, $row->description, $row->reason,
                  $row->payment_amt, $row->emp_name
              ];
          }
          $sheet->fromArray($rowData, NULL, 'A2');

          // Set header respons
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment; filename="pembayaran_piutang' . $tahun . '.xlsx"');
          header('Cache-Control: max-age=0');

          // Tulis file ke output
          $writer = new Xlsx($spreadsheet);
          $writer->save('php://output');
          exit; // Hentikan eksekusi setelah mengirim file
      } catch (\Exception $e) {
          log_message('error', 'Error generating Excel: ' . $e->getMessage());
          return $this->response->setStatusCode(500)->setJSON([
              'status' => 'error',
              'message' => 'Gagal menghasilkan file Excel: ' . $e->getMessage()
          ]);
      }
  }

  public function getInvoiceFile($fileName)
  {
      $filePath = WRITEPATH . 'uploads/invoice/' . $fileName;

      if (file_exists($filePath) && is_file($filePath)) {
          // Set header untuk file PDF
          return $this->response
              ->setHeader('Content-Type', 'application/pdf')
              ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
              ->setHeader('Content-Length', filesize($filePath))
              ->setBody(file_get_contents($filePath));
      } else {
          return $this->response
              ->setStatusCode(404)
              ->setJSON(['status' => 'error', 'message' => 'File tidak ditemukan']);
      }
  }

  // Data tabel pembayaran piutang
  public function dataPembayaranPiutang()
  {
      $tahun = $this->request->getPost('tahun');

      $data = $this->monitoringModel->getDataPembayaranPiutang($tahun);
      echo json_encode($data);
  }

  // Data tabel detail pembayaran piutang
  public function dataDetPembayaranPiutang()
  {
      $idref = $this->request->getPost('id_ref');

      $data = $this->monitoringModel->getDataDetPembayaranPiutang($idref);
      echo json_encode($data);
  }
}
