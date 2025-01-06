<?php

namespace App\Models\ParamEmpModel;

use CodeIgniter\Model;

class ParamEmpModel extends Model
{
  protected $table = 'mst_param_emp';
  protected $primaryKey = 'id'; // Kunci utama tabel mst_param_emp
  protected $useTimestamps = true;
  protected $allowedFields = ['id', 'nik', 'branch_id', 'position_id', 'department_id', 'role_id', 'flg_used', 'user_create', 'create_date', 'user_update', 'update_date'];

  public function getParamEmpByIdRef($idRef)
  {
    return $this->where('id', $idRef)->where('flg_used', 'T')->first();
  }
}
