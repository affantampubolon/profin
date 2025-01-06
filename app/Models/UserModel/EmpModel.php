<?php

namespace App\Models\UserModel;

use CodeIgniter\Model;

class EmpModel extends Model
{
  protected $table = 'mst_employee';
  protected $useTimestamps = true;
  protected $allowedFields = ['id', 'nik', 'name', 'flg_used', 'user_create', 'create_date', 'user_update', 'update_date'];

  public function getEmployeeByNik($nik)
  {
    return $this->where('nik', $nik)->where('flg_used', 'T')->first();
  }
}
