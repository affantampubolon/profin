<?php

namespace App\Models\PipelineModel;

use CodeIgniter\Model;

class PipelineModel extends Model
{
    protected $table = 'trn_pipeline';
    protected $allowedFields = ['nik', 'group_id', 'subgroup_id', 'class_id', 'month', 'year'];

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
}