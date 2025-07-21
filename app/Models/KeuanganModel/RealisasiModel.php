<?php

namespace App\Models\KeuanganModel;

use CodeIgniter\Model;

class RealisasiModel extends Model
{

    protected $tableRealisasi = 'trn_cost_real';

    protected $allowedFields = ['id','no_doc','id_ref','coa','description','real_amt','flg_used','user_create','create_date','user_update','update_date'];

    public function insertRealisasi($data)
    {
        return $this->db->table($this->tableRealisasi)->insertBatch($data);
    }
    
}
