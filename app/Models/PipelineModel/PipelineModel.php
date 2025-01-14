<?php

namespace App\Models\PipelineModel;

use CodeIgniter\Model;

class PipelineModel extends Model
{
    protected $table = 'trn_pipeline';
    protected $allowedFields = ['nik', 'group_id', 'subgroup_id', 'class_id', 'month', 'year'];

    public function getTemporaryData($nik)
    {
        $session = \Config\Services::session();

        // Ambil data sementara dari sesi berdasarkan nik
        $temporaryData = $session->get('temporary_data') ?? [];

        log_message('debug', 'Data temporary di sesi: ' . print_r($temporaryData, true));
        log_message('debug', 'NIK yang digunakan untuk akses sesi: ' . $nik);

        // Gunakan key 'default_nik' jika data tersimpan di sana
        if (isset($temporaryData['default_nik'])) {
            return $temporaryData['default_nik'];
        }

        return []; // Kembalikan array kosong jika tidak ada data
    }

    public function insertBatchReturning(array $dataBatch)
    {
        $builder = $this->db->table($this->table);

        $ids = [];
        foreach ($dataBatch as $data) {
            $builder->insert($data);
            $ids[] = $this->db->insertID(); // Ambil ID terakhir setelah setiap insert
        }

        $query = $this->db->table($this->table)
            ->whereIn('id', $ids)
            ->get();

        return $query->getResultArray(); // Mengembalikan data yang baru disisipkan
    }

    public function getExistingPipelines(array $pipelineData)
    {
        $builder = $this->db->table($this->table);

        foreach ($pipelineData as $data) {
            $builder->orGroupStart()
                ->where('nik', $data['nik'])
                ->where('group_id', $data['group_id'])
                ->where('subgroup_id', $data['subgroup_id'])
                ->where('class_id', $data['class_id'])
                ->where('month', $data['month'])
                ->where('year', $data['year'])
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    public function clearTemporaryData($nik)
{
    $session = \Config\Services::session();

    // Ambil data sementara dari sesi
    $temporaryData = $session->get('temporary_data') ?? [];

    // Hapus data sementara untuk NIK tertentu
    if (isset($temporaryData['default_nik']['nik']) && $temporaryData['default_nik']['nik'] === $nik) {
        unset($temporaryData['default_nik']); // Hapus key default_nik
        $session->set('temporary_data', $temporaryData); // Perbarui sesi
        log_message('debug', 'Data temporary untuk NIK ' . $nik . ' telah dihapus.');
    } else {
        log_message('error', 'Data temporary untuk NIK ' . $nik . ' tidak ditemukan.');
    }
}

}
