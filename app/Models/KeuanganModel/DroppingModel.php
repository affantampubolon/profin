<?php

namespace App\Models\KeuanganModel;

use CodeIgniter\Model;

class DroppingModel extends Model
{

    protected $tableDropping = 'trn_cost_dropping';

    protected $allowedFields = ['id','no_doc','id_ref','coa','description','real_drop_amt','flg_used','user_create','create_date','user_update','update_date'];

    public function insertDropping($data)
    {
        return $this->db->table($this->tableDropping)->insertBatch($data);
    }
    
}
