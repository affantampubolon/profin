<?php

namespace App\Controllers\Pipeline;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Pipeline extends BaseController
{
    // Parent Construct
    public function __construct() {}

    // Function mengambil group_id berdasarkan session user
    public function getUserGroupSession()
    {
        $session = session();
        return $this->response->setJSON(['group_id' => $session->get('group_id')]);
    }

    // Function Index -> halaman PIPELINE
    public function index()
    {
        // Ambil username dari session
        $username = session()->get('username');

        $data = [
            'title' => "Pembuatan Pipeline",
            'group_barang' => $this->kelasProdModel->getGrupBarang($username),
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
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
                    'freq_visit' => $rowArray['G'] ?? 0,
                    'target_value' => $rowArray['H'] ?? 0,
                    'probability' => $rowArray['I'] ?? 0,
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
        // Ambil username dari session
        $username = session()->get('username');
        
        $data = [
            'title' => "Formulir Pipeline",
            'group_barang' => $this->kelasProdModel->getGrupBarang($username),
            'probabilitas' => $this->probabilitasModel->getSkalaProbabilitas($username),
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('pipeline/form_input', $data);
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
        if (empty($data['cust_id']) || empty($data['target_value'])) {
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

        // Validasi duplikasi
        foreach ($temporaryData[$nik] as $existingData) {
            if ($existingData['cust_id'] === $data['cust_id']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID pelanggan tidak boleh sama.'
                ]);
            }
        }

        $temporaryData[$nik][] = $data;

        // Simpan kembali ke session
        $session->set('temporary_data', $temporaryData);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil disimpan ke sesi.',
        ]);
    }

    public function deleteTemporerDetailPipeline()
    {
        $session = \Config\Services::session();
        $requestData = $this->request->getJSON(true);
    
        $cust_id = $requestData['cust_id'] ?? null;
        $nik = $requestData['nik'] ?? 'default_nik';
    
        $temporaryData = $session->get('temporary_data') ?? [];

        if (!isset($temporaryData[$nik])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan untuk NIK ini.'
            ]);
        }

        if ($cust_id) {
            // Hapus hanya data dengan cust_id tertentu
            $temporaryData[$nik] = array_filter($temporaryData[$nik], function ($data) use ($cust_id) {
                return $data['cust_id'] !== $cust_id;
            });
        } else {
            // Hapus semua data terkait NIK
            unset($temporaryData[$nik]);
        }

        $session->set('temporary_data', $temporaryData);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil dihapus.'
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

            // Menyimpan data pipeline
            $pipelineData = [
                'year' => $this->request->getPost('tahun_pipeline'),
                'month' => $this->request->getPost('bulan_pipeline'),
                'group_id' => $this->request->getPost('grup_barang'),
                'subgroup_id' => $this->request->getPost('subgrup_barang'),
                'class_id' => $this->request->getPost('kelas_barang'),
                'nik' => $nik,
                'user_create' => $nik,
            ];

            $insertedPipelines = $this->pipelineModel->insertBatchReturning([$pipelineData]);

            if (!$insertedPipelines) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data pipeline.',
                ]);
            }

            $pipelineId = $insertedPipelines[0]['id'];

            // Menyimpan detail pipeline
            $detailPipeline = [];
            $groupId = $this->session->get('group_id');

            foreach ($detailData as $detail) {
                if (!isset($detail['cust_id'], $detail['target_value'])) {
                    continue; // Lewati jika data tidak lengkap
                }

                $pipelineDetail = [
                    'id_ref' => $pipelineId,
                    'cust_id' => $detail['cust_id'],
                    'target_value' => $detail['target_value'],
                    'user_create' => $nik,
                ];

                // Jika group_id adalah '01' atau '03', tambahkan freq_visit
                if (in_array($groupId, ['01', '03'])) {
                    $pipelineDetail['freq_visit'] = $detail['freq_visit'] ?? null;
                }

                // Jika group_id adalah '02' atau '05', tambahkan probability
                if (in_array($groupId, ['02', '05'])) {
                    $pipelineDetail['probability'] = $detail['probability'] ?? null;
                }

                $detailPipeline[] = $pipelineDetail;
            }

            // Debugging log untuk memeriksa data sebelum insert
            log_message('debug', 'Detail pipeline yang akan disimpan: ' . print_r($detailPipeline, true));

            if (!empty($detailPipeline)) {
                $this->pipelineDetModel->insertBatch($detailPipeline);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data detail pipeline tidak valid.',
                ]);
            }

            // Mengirimkan response sukses
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

    // Data draft pipeline
    public function dataDraftPipeline()
    {
        // Ambil username dari session
        $username = session()->get('username');
        //filter data draft pipeline
        $tahun = $this->request->getPost('thn');
        $bulan = $this->request->getPost('bln');
        $group_id = $this->request->getPost('grp_prod');
        $subgroup_id = $this->request->getPost('subgrp_prod');
        $class_id = $this->request->getPost('clsgrp_prod');

        $data = $this->pipelineDetModel->getDataPipelineDet($username, $tahun, $bulan, $group_id, $subgroup_id, $class_id);
        echo json_encode($data);
    }

    // Update data draft pipeline
    public function updateDraftPipeline()
    {
        $request = $this->request->getJSON();
        $id = $request->id;

        // Ambil username dari session
        $username = $this->session->get('username');

        $data = [
            'freq_visit' => $request->freq_visit,
            'target_value' => $request->target_value,
            'probability' => $request->probability,
            'user_update' => $username,
            'update_date' => date('Y-m-d H:i:s') // Format timestamp
        ];

        $update = $this->pipelineDetModel->updatePipelineDet($id, $data);

        if ($update) {
            die(json_encode(['status' => 'success', 'message' => 'Berhasil memperbarui data']));
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data']);
        }
    }

    // Hapus data draft pipeline
    public function deleteDraftPipeline()
    {
        $request = $this->request->getJSON();
        $id = $request->id;

        $delete = $this->pipelineDetModel->deletePipelineDet($id);

        if ($delete) {
            return $this->response->setJSON(['status' => 'success' , 'message' => 'Berhasil menghapus data']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }

    // Function Index -> halaman Persetujuan Pipeline
    public function indexPersetujuan()
    {
        // Ambil username dari session
        $username = session()->get('username');

        $data = [
            'title' => "Persetujuan Pipeline",
            'data_salesmarketing' => $this->salesMarketingModel->getSalesMarketingCab($username),
            'group_barang' => $this->kelasProdModel->getGrupBarang($username),
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('pipeline/persetujuan', $data);
    }

    // Data verifikasi pipeline
    public function dataVerifPipeline()
    {
        //filter data verifikasi pipeline
        $username = $this->request->getPost('sales_marketing');
        $tahun = $this->request->getPost('thn');
        $bulan = $this->request->getPost('bln');
        $group_id = $this->request->getPost('grp_prod');
        $subgroup_id = $this->request->getPost('subgrp_prod');
        $class_id = $this->request->getPost('klsgrp_prod');

        $data = $this->pipelineDetModel->getDataPipelineDet($username, $tahun, $bulan, $group_id, $subgroup_id, $class_id);
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

        $update = $this->pipelineDetModel->updatePipelineDet($id, $data);

        if ($update) {
            $message = $flg_approve
                ? "Pipeline ID $id berhasil disetujui."
                : "Pipeline ID $id ditolak dengan alasan: $reason_reject.";

            return $this->response->setJSON(['status' => 'success', 'message' => $message]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data.']);
        }
    }

    // Function Index -> halaman Monitoring Pipeline
    public function indexMonitoring()
    {
        // Ambil username dari session
        $username = session()->get('username');

        $data = [
            'title' => "Monitoring Pipeline",
            'data_salesmarketing' => $this->salesMarketingModel->getSalesMarketingCab($username),
            'group_barang' => $this->kelasProdModel->getGrupBarang($username),
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('pipeline/monitoring', $data);
    }
}
