<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'mst_department';
    protected $allowedFields = ['id', 'name', 'flg_kp', 'flg_used', 'user_create', 'create_date', 'user_update', 'update_date', 'group_id'];

    public function getGroupByDeptID($dept_id)
    {
        return $this->where('id', $dept_id)->where('flg_used', TRUE)->first();
    }
}
