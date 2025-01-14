<?php

namespace App\Controllers\Pipeline;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Pipeline extends BaseController
{
    // Parent Construct
    public function __construct() {}
    // Function Index -> halaman PIPELINE
    public function index()
    {
        $data = [
            'title' => "Pembuatan Pipeline",
            'breadcrumb' => $this->breadcrumb
        ];
        return view('pipeline/pembuatan', $data);
    }

    public function uploadPipeline()
    {
        try {
            $username = $this->session->get('username');
            if (!$username) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Session login tidak valid. Silakan login kembali.',
                ]);
            }

            if (!$this->validate([
                'file' => 'uploaded[file]|mime_in[file,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel]|max_size[file,1024]'
            ])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'File tidak valid.',
                    'errors' => $this->validator->getErrors(),
                ]);
            }

            // Proses unggahan file
            $file = $this->request->getFile('file');
            $newName = $file->getRandomName();
            $file->move('uploads', $newName);

            // Parsing data file menggunakan PhpSpreadsheet
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load('uploads/' . $newName);
            $worksheet = $spreadsheet->getActiveSheet();

            $rows = $worksheet->toArray(null, true, true, true);
            array_shift($rows);

            $uniquePipeline = [];
            $dataTabelPipelineDet = [];

            foreach ($rows as $rowArray) {
                if (empty($rowArray['A']) || empty($rowArray['B']) || empty($rowArray['C']) || empty($rowArray['D']) || empty($rowArray['E'])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Data tidak lengkap. Pastikan semua kolom terisi.',
                    ]);
                }

                $key = $username . '|' . $rowArray['A'] . '|' . $rowArray['B'] . '|' . $rowArray['C'] . '|' . $rowArray['D'] . '|' . $rowArray['E'];
                if (!isset($uniquePipeline[$key])) {
                    $uniquePipeline[$key] = [
                        'nik' => $username,
                        'group_id' => $rowArray['A'],
                        'subgroup_id' => $rowArray['B'],
                        'class_id' => $rowArray['C'],
                        'month' => $rowArray['D'],
                        'year' => $rowArray['E'],
                        'user_create' => $username,
                    ];
                }

                $dataTabelPipelineDet[] = [
                    'cust_id' => $rowArray['F'],
                    'target_call' => $rowArray['G'] ?? 0,
                    'target_ec' => $rowArray['H'] ?? 0,
                    'target_value' => $rowArray['I'] ?? 0,
                    'probability' => $rowArray['J'] ?? 0,
                    'pipeline_key' => $key,
                    'user_create' => $username,
                ];
            }

            $existingPipelines = $this->pipelineModel->getExistingPipelines(array_values($uniquePipeline));
            $idMapping = [];
            foreach ($existingPipelines as $pipeline) {
                $key = $pipeline['nik'] . '|' . $pipeline['group_id'] . '|' . $pipeline['subgroup_id'] . '|' . $pipeline['class_id'] . '|' . $pipeline['month'] . '|' . $pipeline['year'];
                $idMapping[$key] = $pipeline['id'];
            }

            $newPipelines = array_filter($uniquePipeline, function ($key) use ($idMapping) {
                return !isset($idMapping[$key]);
            }, ARRAY_FILTER_USE_KEY);

            if (!empty($newPipelines)) {
                $insertedPipelines = $this->pipelineModel->insertBatchReturning(array_values($newPipelines));
                foreach ($insertedPipelines as $pipeline) {
                    $key = $pipeline['nik'] . '|' . $pipeline['group_id'] . '|' . $pipeline['subgroup_id'] . '|' . $pipeline['class_id'] . '|' . $pipeline['month'] . '|' . $pipeline['year'];
                    $idMapping[$key] = $pipeline['id'];
                }
            }

            foreach ($dataTabelPipelineDet as &$row) {
                if (!isset($idMapping[$row['pipeline_key']])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Key pipeline tidak ditemukan: ' . $row['pipeline_key'],
                    ]);
                }
                $row['id_ref'] = $idMapping[$row['pipeline_key']];
                unset($row['pipeline_key']);
            }

            $this->pipelineDetModel->insertBatch($dataTabelPipelineDet);

            // Jika berhasil
            unlink('uploads/' . $newName);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil diunggah dan diproses.',
                'details' => [
                    'total_pipeline' => count($uniquePipeline),
                    'total_pipeline_det' => count($dataTabelPipelineDet),
                ],
            ]);
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());

            // Filter pesan error PostgreSQL
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'RAISE EXCEPTION') !== false || strpos($errorMessage, 'CONTEXT:') !== false) {
                // Ambil hanya pesan error dari RAISE EXCEPTION
                if (preg_match("/ERROR: (.+?) CONTEXT:/s", $errorMessage, $matches)) {
                    $errorMessage = trim($matches[1]); // Pesan yang relevan
                } else {
                    $errorMessage = "Terjadi kesalahan selama proses unggahan."; // Pesan fallback
                }
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan selama proses unggahan.',
                'error_details' => $errorMessage,
            ]);
        }
    }

    // Function Index -> halaman Formulir Pipeline
    public function indexform()
    {
        $data = [
            'title' => "Formulir Pipeline",
            'group_barang' => $this->kelasProdModel->getGrupBarang(),
            'breadcrumb' => $this->breadcrumb
        ];
        return view('pipeline/form_input', $data);
    }

    // Function untuk mendapatkan Subgrup Barang berdasarkan Grup Barang
    public function getSubGrupBarang()
    {
        $grp_prod = $this->request->getPost('grp_prod');

        // Validasi input
        if (empty($grp_prod)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['message' => 'Group Product tidak boleh kosong']);
        }

        // Ambil data dari model
        $data = $this->kelasProdModel->getSubGrupBarang($grp_prod);

        if (empty($data)) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['message' => 'Sub Grup Barang tidak ditemukan']);
        }

        // Buat opsi untuk dropdown
        $options = '<option selected value="">Pilih Sub Grup Barang</option>';
        foreach ($data as $row) {
            $options .= '<option value="' . $row['subgroup_id'] . '">'
                . $row['subgroup_id'] . ' - ' . $row['subgroup_name']
                . '</option>';
        }

        return $this->response->setBody($options);
    }

    // Function untuk mendapatkan Kelas Barang berdasarkan Grup dan Subgrup Barang
    public function getKelasBarang()
    {
        $grp_prod = $this->request->getPost('grp_prod');
        $subgrp_prod = $this->request->getPost('subgrp_prod');

        // 
        $data = $this->kelasProdModel->getKelasBarang($grp_prod, $subgrp_prod);
        // Buat opsi untuk dropdown
        $options = '<option selected value="">Pilih Kelas Barang</option>';
        foreach ($data as $row) {
            $options .= '<option value="' . $row['class_id'] . '">'
                . $row['class_id'] . ' - ' . $row['class_name']
                . '</option>';
        }

        return $this->response->setBody($options);
    }

    // Function untuk mendapatkan Pelanggan
    public function getMstPelanggan()
    {
        $data = $this->pelangganModel->getMstPelanggan();
        echo json_encode($data);
    }

    // PENYIMPANAN SEMENTARA DETAIL PIPELINE
    public function getTemporerDetailPipeline()
    {
        // Cek apakah ada data di session
        $session = \Config\Services::session();

        // Ambil data temporary berdasarkan NIK
        $nik = $this->request->getGet('nik') ?? 'default_nik'; // Ganti sesuai logika NIK
        $temporaryData = $session->get('temporary_data') ?? [];

        if (isset($temporaryData[$nik])) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $temporaryData[$nik]
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Tidak ada data untuk NIK ini.'
        ]);
    }

    public function saveTemporerDetailPipeline()
    {
        $session = \Config\Services::session();

        // Ambil input dari request
        $data = $this->request->getJSON(true);

        // Validasi data input
        if (empty($data['cust_id']) || empty($data['target_call']) || empty($data['target_ec']) || empty($data['target_value'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua field harus diisi.'
            ]);
        }

        // Ambil data yang sudah ada di session
        $temporaryData = $session->get('temporary_data') ?? [];

        // Tambahkan data baru ke dalam session
        $nik = $this->request->getPost('nik') ?? 'default_nik'; // Ganti sesuai logika nik
        if (!isset($temporaryData[$nik])) {
            $temporaryData[$nik] = [];
        }

        $temporaryData[$nik][] = $data;

        // Simpan kembali ke session
        $session->set('temporary_data', $temporaryData);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil disimpan ke sesi.',
        ]);
    }

    public function insertFormPipeline()
    {
        try {
            $username = $this->session->get('username');
            if (!$username) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Session login tidak valid. Silakan login kembali.',
                ]);
            }

            $nik = $username;

            $detailData = $this->pipelineModel->getTemporaryData($nik);

            log_message('debug', 'Data detail pipeline: ' . print_r($detailData, true));

            if (empty($detailData) || !is_array($detailData)) {
                log_message('error', 'Data detail pipeline kosong atau tidak valid: ' . print_r($detailData, true));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada data detail pipeline untuk disimpan.',
                ]);
            }

            $pipelineData = [
                'year' => $this->request->getPost('tahun_pipeline'),
                'month' => $this->request->getPost('bulan_pipeline'),
                'group_id' => $this->request->getPost('grup_barang'),
                'subgroup_id' => $this->request->getPost('subgrup_barang'),
                'class_id' => $this->request->getPost('kelas_barang'),
                'nik' => $nik,
            ];

            $insertedPipelines = $this->pipelineModel->insertBatchReturning([$pipelineData]);

            if (!$insertedPipelines) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data pipeline.',
                ]);
            }

            $pipelineId = $insertedPipelines[0]['id'];

            $detailPipeline = [];
            foreach ($detailData as $detail) {
                if (isset($detail['cust_id'], $detail['target_call'], $detail['target_ec'], $detail['target_value'], $detail['probability'])) {
                    $detailPipeline[] = [
                        'id_ref' => $pipelineId,
                        'cust_id' => $detail['cust_id'],
                        'target_call' => $detail['target_call'],
                        'target_ec' => $detail['target_ec'],
                        'target_value' => $detail['target_value'],
                        'probability' => $detail['probability'],
                    ];
                }
            }

            log_message('debug', 'Detail pipeline yang akan disimpan: ' . print_r($detailPipeline, true));

            if (!empty($detailPipeline)) {
                $this->pipelineDetModel->insertBatch($detailPipeline);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data detail pipeline tidak valid.',
                ]);
            }

            $this->pipelineModel->clearTemporaryData($nik);
            log_message('debug', 'Data temporary untuk nik ' . $nik . ' telah dibersihkan.');

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data pipeline berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }
}
