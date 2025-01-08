<?php

namespace App\Models\PipelineModel;

use CodeIgniter\Model;

class PipelineModel extends Model
{
    protected $table      = 'trn_pipeline';
    protected $allowedFields = ['nik', 'group_id', 'subgroup_id', 'class_id', 'month', 'year'];    

    // ... (method-method lainnya jika diperlukan)

    public function insertBatchReturning(array $dataBatch)
    {
        $builder = $this->db->table($this->table);

        // Gunakan INSERT ... RETURNING untuk mendapatkan ID dan data yang disisipkan
        $builder->insertBatch($dataBatch);

        $query = $this->db->query('SELECT id, nik, group_id, subgroup_id, class_id, month, year FROM ' . $this->table . ' WHERE id >= LASTVAL()');
        return $query->getResultArray(); // Mengembalikan ID yang disisipkan bersama data
    }

    public function getExistingPipelines(array $pipelineData)
    {
        $builder = $this->db->table($this->table);

        // Bangun query untuk mencocokkan semua kombinasi unik
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