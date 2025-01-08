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
        ];
        return view('pipeline/pembuatan', $data);
    }

   public function uploadpipeline()
{
    try {
        // Ambil data dari session
        $username = $this->session->get('username');
        if (!$username) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Session login tidak valid. Silakan login kembali.',
            ]);
        }

        // Validasi file yang diunggah
        if (!$this->validate([
            'file' => 'uploaded[file]|mime_in[file,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel]|max_size[file,1024]'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File tidak valid. Pastikan format dan ukuran sesuai.',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $file = $this->request->getFile('file');
        $newName = $file->getRandomName();
        $file->move('uploads', $newName);

        // Baca file Excel
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load('uploads/' . $newName);
        $worksheet = $spreadsheet->getActiveSheet();

        // Inisialisasi array untuk menyimpan data
        $uniquePipeline = [];
        $dataTabelPipelineDet = [];

        // Ambil semua data dari worksheet dan abaikan header
        $rows = $worksheet->toArray(null, true, true, true); // Tetap baca nilai kosong
        array_shift($rows); // Hapus baris pertama (header)

        foreach ($rows as $rowIndex => $rowArray) {
            // Validasi jumlah kolom
            if (count($rowArray) < 10) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Baris ke-" . ($rowIndex + 2) . " tidak memiliki jumlah kolom yang cukup. Pastikan file Excel sesuai format.",
                ]);
            }

            // Validasi kolom wajib
            if (empty($rowArray['A']) || empty($rowArray['B']) || empty($rowArray['C']) || empty($rowArray['D']) || empty($rowArray['E']) || empty($rowArray['F'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Baris ke-" . ($rowIndex + 2) . " memiliki data yang tidak lengkap. Pastikan semua kolom wajib terisi.",
                ]);
            }

            // Buat key unik untuk data pipeline
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

            // Data detail untuk setiap baris
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

        // Cek data pipeline yang sudah ada di database
        $existingPipelines = $this->pipelineModel->getExistingPipelines(array_values($uniquePipeline));

        $idMapping = [];
        foreach ($existingPipelines as $pipeline) {
            $key = $pipeline['nik'] . '|' . $pipeline['group_id'] . '|' . $pipeline['subgroup_id'] . '|' . $pipeline['class_id'] . '|' . $pipeline['month'] . '|' . $pipeline['year'];
            $idMapping[$key] = $pipeline['id'];
        }

        // Sisipkan data pipeline baru
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

        // Mapping id_ref untuk data detail
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

        // Sisipkan data detail
        $this->pipelineDetModel->insertBatch($dataTabelPipelineDet);

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
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Terjadi kesalahan selama proses unggahan.',
            'error_details' => $e->getMessage(),
        ]);
    }
}



}