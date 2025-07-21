<?php

namespace App\Models\KeuanganModel;

use CodeIgniter\Model;

class AnggaranModel extends Model
{

    protected $tableAnggaran = 'trn_cost_plan';

    protected $allowedFields = ['id','no_doc','id_ref','coa','description','budget_amt','real_amt','diff_amt','flg_used','user_create','create_date','user_update','update_date'];

    public function insertAnggaran($data)
    {
        return $this->db->table($this->tableAnggaran)->insertBatch($data);
    }
    
}
