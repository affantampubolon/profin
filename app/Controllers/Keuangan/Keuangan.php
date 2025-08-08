<?php

namespace App\Controllers\Keuangan;

use App\Controllers\BaseController;

class Keuangan extends BaseController
{
  // Parent Construct
  public function __construct() {}

  public function anggaranindex()
  {
    $data = [
      'title' => "Rencana Anggaran",
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('keuangan/anggaran', $data);
  }

  public function insertAnggaran()
  {
      try {
          $proyekId = $this->request->getPost('proyekId');
          $data = json_decode($this->request->getPost('data'), true);
          $username = $this->session->get('username');

          if (!$proyekId || empty($data)) {
              return $this->response->setJSON([
                  'status' => 'error',
                  'message' => 'Data tidak lengkap.',
              ]);
          }

          // Ambil sequence untuk no_doc dari mst_seq
          $db = \Config\Database::connect();
          $v_year = date('Y');
          $v_month = $this->romanMonth(date('n')); // Fungsi untuk konversi bulan ke Romawi

          $builder = $db->table('mst_seq');
          $builder->where('year', $v_year);
          $builder->where('init', 'ANG');
          $seq = $builder->select('seq')->get()->getRow();

          if ($seq) {
              $v_seq = $seq->seq + 1;
              $builder->update(['seq' => $v_seq]);
          } else {
              $v_seq = 1;
              $builder->insert(['init' => 'ANG', 'year' => $v_year, 'month' => $v_month, 'seq' => $v_seq]);
          }

          $no_doc = 'ANG-' . $v_year . '-' . $v_month . '-' . str_pad($v_seq, 4, '0', STR_PAD_LEFT);

          // Siapkan data untuk insert ke trn_cost_plan
          $insertData = [];
          foreach ($data as $row) {
              $insertData[] = [
                  'id_ref' => $proyekId,
                  'coa' => $row['coa'],
                  'description' => $row['description'],
                  'budget_amt' => str_replace([',', '.'], ['', ''], $row['budget_amt']), // Hilangkan format uang
                  'diff_amt' => str_replace([',', '.'], ['', ''], $row['budget_amt']),
                  'net_plan_amt' => str_replace([',', '.'], ['', ''], $row['budget_amt']),
                  'no_doc' => $no_doc, // Gunakan no_doc yang sama untuk semua baris
                  'user_create' => $username,   
                  'create_date' => date('Y-m-d H:i:s'),
              ];
          }

          // Insert batch ke tabel
          $this->anggaranModel->insertAnggaran($insertData);

          return $this->response->setJSON([
              'status' => 'success',
              'message' => 'Data anggaran berhasil disimpan.',
          ]);
      } catch (\Exception $e) {
          log_message('error', 'Error menyimpan anggaran: ' . $e->getMessage());
          return $this->response->setJSON([
              'status' => 'error',
              'message' => 'Terjadi kesalahan saat menyimpan data.',
          ]);
      }
  }

  // Fungsi untuk konversi bulan ke notasi Romawi
  private function romanMonth($month)
  {
      $roman = [
          1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
          7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
      ];
      return $roman[$month] ?? 'ERROR';
  }

  public function realisasiindex()
  {
    $data = [
      'title' => "Realisasi Biaya",
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('keuangan/realisasi', $data);
  }

  public function insertRealisasi()
  {
      try {
          $data = json_decode($this->request->getPost('data'), true);
          $username = $this->session->get('username');

          // Ambil sequence untuk no_doc dari mst_seq
          $db = \Config\Database::connect();
          $v_year = date('Y');
          $v_month = $this->romanMonth(date('n')); // Fungsi untuk konversi bulan ke Romawi

          $builder = $db->table('mst_seq');
          $builder->where('year', $v_year);
          $builder->where('init', 'BIY');
          $seq = $builder->select('seq')->get()->getRow();

          if ($seq) {
              $v_seq = $seq->seq + 1;
              $builder->update(['seq' => $v_seq]);
          } else {
              $v_seq = 1;
              $builder->insert(['init' => 'BIY', 'year' => $v_year, 'month' => $v_month, 'seq' => $v_seq]);
          }

          $no_doc = 'BIY-' . $v_year . '-' . $v_month . '-' . str_pad($v_seq, 4, '0', STR_PAD_LEFT);

          // Siapkan data untuk insert ke trn_cost_real
          $insertData = [];
          foreach ($data as $row) {
              $insertData[] = [
                  'id_ref' => $row['id_ref'],
                  'coa' => $row['coa'],
                  'description' => $row['description'],
                  'real_amt' => str_replace([',', '.'], ['', ''], $row['real_amt']), // Hilangkan format uang
                  'no_doc' => $no_doc, // Gunakan no_doc yang sama untuk semua baris
                  'user_create' => $username,
                  'create_date' => date('Y-m-d H:i:s'),
              ];
          }

          // Insert batch ke tabel
          $this->realisasiModel->insertRealisasi($insertData);

          return $this->response->setJSON([
              'status' => 'success',
              'message' => 'Data realisasi biaya berhasil disimpan.',
          ]);
      } catch (\Throwable $e) {
        // Log error di server
        log_message('error', $e->getMessage());

        // Ambil pesan kesalahan pertama saja
        $fullMsg   = $e->getMessage();
        $lines     = explode("\n", $fullMsg);
        $firstLine = $lines[0] ?? 'Terjadi kesalahan saat menyimpan.';
        
        // Jika pesan mengandung 'ERROR:' dari PostgreSQL, ekstrak setelahnya
        if (preg_match('/ERROR:\s*(.*)/', $firstLine, $m)) {
            $firstLine = trim($m[1]);
        }

        // Kembalikan respons JSON error dengan status 400
        return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $firstLine,
                    ]);
    } 
  }

  public function droppingindex()
  {
    $data = [
      'title' => "Biaya Dropping",
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('keuangan/dropping', $data);
  }

  public function insertDropping()
  {
      try {
          $data = json_decode($this->request->getPost('data'), true);
          $username = $this->session->get('username');

          // Ambil sequence untuk no_doc dari mst_seq
          $db = \Config\Database::connect();
          $v_year = date('Y');
          $v_month = $this->romanMonth(date('n')); // Fungsi untuk konversi bulan ke Romawi

          $builder = $db->table('mst_seq');
          $builder->where('year', $v_year);
          $builder->where('init', 'DRP');
          $seq = $builder->select('seq')->get()->getRow();

          if ($seq) {
              $v_seq = $seq->seq + 1;
              $builder->update(['seq' => $v_seq]);
          } else {
              $v_seq = 1;
              $builder->insert(['init' => 'DRP', 'year' => $v_year, 'month' => $v_month, 'seq' => $v_seq]);
          }

          $no_doc = 'DRP-' . $v_year . '-' . $v_month . '-' . str_pad($v_seq, 4, '0', STR_PAD_LEFT);

          // Siapkan data untuk insert ke trn_cost_dropping
          $insertData = [];
          foreach ($data as $row) {
              $insertData[] = [
                  'id_ref' => $row['id_ref'],
                  'coa' => $row['coa'],
                  'description' => $row['description'],
                  'real_drop_amt' => str_replace([',', '.'], ['', ''], $row['real_drop_amt']), // Hilangkan format uang
                  'no_doc' => $no_doc, // Gunakan no_doc yang sama untuk semua baris
                  'user_create' => $username,
                  'create_date' => date('Y-m-d H:i:s'),
              ];
          }

          // Insert batch ke tabel
          $this->droppingModel->insertDropping($insertData);

          return $this->response->setJSON([
              'status' => 'success',
              'message' => 'Data biaya dropping berhasil disimpan.',
          ]);
      } catch (\Throwable $e) {
        // Log error di server
        log_message('error', $e->getMessage());

        // Ambil pesan kesalahan pertama saja
        $fullMsg   = $e->getMessage();
        $lines     = explode("\n", $fullMsg);
        $firstLine = $lines[0] ?? 'Terjadi kesalahan saat menyimpan.';
        
        // Jika pesan mengandung 'ERROR:' dari PostgreSQL, ekstrak setelahnya
        if (preg_match('/ERROR:\s*(.*)/', $firstLine, $m)) {
            $firstLine = trim($m[1]);
        }

        // Kembalikan respons JSON error dengan status 400
        return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $firstLine,
                    ]);
    } 
  }

  public function pembayaranindex()
  {
    $data = [
      'title' => "Pembayaran Invoice",
      'breadcrumb' => $this->breadcrumb,
      'session' => $this->session
    ];
    return view('keuangan/pembayaran', $data);
  }

  public function unggahInvoicePembayaran()
  {
      $file = $this->request->getFile('fileInvoice');
      if ($file && $file->isValid() && !$file->hasMoved()) {
          $fileType = $file->getClientMimeType();
          $fileSize = $file->getSize();

          // Validasi format dan ukuran
          if ($fileType !== 'application/pdf') {
              return $this->response->setJSON(['status' => 'error', 'message' => 'File yang diunggah bukan dalam format .pdf']);
          }

          if ($fileSize > 2.5 * 1024 * 1024) { // 2,5 MB
              return $this->response->setJSON(['status' => 'error', 'message' => 'File melebihi kapasitas 2,5 MB']);
          }

          // Tentukan lokasi penyimpanan
          $uploadPath = WRITEPATH . 'uploads/invoice';
          if (!is_dir($uploadPath)) {
              mkdir($uploadPath, 0775, true); // Buat folder jika belum ada
          }

          // Simpan file dengan nama acak
          $fileName = $file->getRandomName();
          if ($file->move($uploadPath, $fileName)) {
              return $this->response->setJSON(['status' => 'success', 'fileName' => $fileName]);
          } else {
              return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan file: ' . $file->getErrorString()]);
          }
      } else {
          return $this->response->setJSON(['status' => 'error', 'message' => 'File tidak valid atau tidak ditemukan']);
      }
  }

  public function insertPembayaran()
  {
      try {
          $data = json_decode($this->request->getPost('data'), true);
          $username = $this->session->get('username');

          // Ambil sequence untuk no_doc dari mst_seq
          $db = \Config\Database::connect();
          $v_year = date('Y');
          $v_month = $this->romanMonth(date('n')); // Fungsi untuk konversi bulan ke Romawi

          $builder = $db->table('mst_seq');
          $builder->where('year', $v_year);
          $builder->where('init', 'PBY');
          $seq = $builder->select('seq')->get()->getRow();

          if ($seq) {
              $v_seq = $seq->seq + 1;
              $builder->update(['seq' => $v_seq]);
          } else {
              $v_seq = 1;
              $builder->insert(['init' => 'PBY', 'year' => $v_year, 'month' => $v_month, 'seq' => $v_seq]);
          }

          $no_doc = 'PBY-' . $v_year . '-' . $v_month . '-' . str_pad($v_seq, 4, '0', STR_PAD_LEFT);

          // Siapkan data untuk insert ke trn_payment
          $insertData = [];
          foreach ($data as $row) {
              // Konversi string tanggal ke objek DateTime untuk perhitungan
              $invoiceDate = new \DateTime($row['invoice_date']);
              $paymentDate = new \DateTime($row['payment_date']);

              // Hitung selisih hari
              $timeDiff = $paymentDate->diff($invoiceDate);
              $dayDiff = $timeDiff->days;
              if ($dayDiff === 0) {
                  $dayDiff = 0; // Jika sama, set ke 0 hari
              } else {
                  $dayDiff += 1; // Tambah 1 hari untuk hari terakhir
              }

              $insertData[] = [
                  'id_ref' => $row['id_ref'],
                  'no_doc' => $no_doc,
                  'invoice_date' => $row['invoice_date'],
                  'payment_date' => $row['payment_date'],
                  'period_payment' => $dayDiff,
                  'payment_amt' => str_replace([',', '.'], ['', ''], $row['payment_amt']),
                  'description' => $row['description'] ?? null,
                  'reason' => $row['reason'] ?? null,
                  'file_invoice' => $row['file_invoice'] ?? null, // Tambahkan nama file
                  'user_create' => $username,
                  'create_date' => date('Y-m-d H:i:s'),
              ];
          }

          // Insert batch ke tabel
          $this->pembayaranModel->insertPembayaran($insertData);

          return $this->response->setJSON([
              'status' => 'success',
              'message' => 'Data pembayaran berhasil disimpan.',
          ]);
      } catch (\Throwable $e) {
          log_message('error', $e->getMessage());
          $fullMsg = $e->getMessage();
          $lines = explode("\n", $fullMsg);
          $firstLine = $lines[0] ?? 'Terjadi kesalahan saat menyimpan.';
          if (preg_match('/ERROR:\s*(.*)/', $firstLine, $m)) {
              $firstLine = trim($m[1]);
          }

          return $this->response
              ->setStatusCode(400)
              ->setJSON([
                  'status' => 'error',
                  'message' => $firstLine,
              ]);
      }
  }
}
