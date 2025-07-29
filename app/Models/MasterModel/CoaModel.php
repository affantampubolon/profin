<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class CoaModel extends Model
{

    protected $tableCoa = 'mst_coa';

    protected $allowedFields = ['id','coa_code','coa_name','flg_used','user_create','create_date','user_update','update_date'];

    public function getCoaFilter()
    {
        $db = \Config\Database::connect();

        $query = $db->table('mst_coa')
            ->select('id, coa_code, coa_name')
            ->where('flg_used', 't')
            ->orderBy('coa_code')
            ->get();

        return $query->getResultArray();
    }

    // Mendapatkan nilai COA
    public function getCoaVal($coa, $id_ref)
    {
        $builder = $this->db->table('trn_cost_plan')
            ->select('diff_amt, net_plan_amt')
            ->where('coa', $coa)
            ->where('id_ref', $id_ref);

            return $builder->get()->getResult();
    }
}
