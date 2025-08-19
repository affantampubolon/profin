<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $tableEmployee = 'mst_employee';
    protected $tableParamEmp = 'mst_param_emp';
    protected $tableUser = 'mst_user';

    public function getDataKaryawan($cabang)
    {
        $builder = $this->db->table('mst_param_emp')
            ->select('id, nik, f_tv_employee_name(nik) AS emp_name, branch_id, f_tv_branch_name(branch_id) AS branch_name,
                    position_id, f_position_name(position_id) AS position_name, department_id, f_dept_name(department_id) AS department_name, role_id, f_tv_role_name(role_id) AS role_name, flg_used')
            ->where('flg_used', 't')
            ->orderBy('nik');

            // Kondisi untuk branch_id (jika tidak kosong, tambahkan filter)
            if (!empty($cabang)) {
                $builder->where('branch_id', $cabang);
            }

            return $builder->get()->getResult();
    }

    public function getKaryawanById($id)
    {
        return $this->db->table('mst_param_emp')
            ->select('id, nik, f_tv_employee_name(nik) AS emp_name, branch_id, department_id, position_id, role_id')
            ->where('id', $id)
            ->get()
            ->getRow();
    }

    public function updateKaryawan($id, $data)
    {
        return $this->db->table($this->tableParamEmp)
            ->where('id', $id)
            ->update($data);
    }

    public function insertKaryawan($data)
    {
        return $this->db->table($this->tableEmployee)->insert($data);
    }

    public function insertParamKaryawan($data)
    {
        return $this->db->table($this->tableParamEmp)->insert($data);
    }

    public function getDataUser()
    {
        $builder = $this->db->table('mst_user')
            ->select('id, username, flg_used')
            ->where('flg_used', 't')
            ->orderBy('id');

            return $builder->get()->getResult();
    }

    public function updateUser($id, $data)
    {
        return $this->db->table($this->tableUser)
            ->where('id', $id)
            ->update($data);
    }

    public function insertUser($data)
    {
        return $this->db->table($this->tableUser)->insert($data);
    }

    public function getFilterDataKaryawan()
    {
        $builder = $this->db->table('mst_param_emp')
            ->select('id, nik, f_tv_employee_name(nik) AS emp_name, branch_id, f_tv_branch_name(branch_id) AS branch_name,
                    position_id, f_position_name(position_id) AS position_name, department_id, f_dept_name(department_id) AS department_name, role_id, f_tv_role_name(role_id) AS role_name, flg_used')
            ->where('flg_used', 't')
            ->orderBy('nik');

            return $builder->get()->getResult();
    }

    public function updatePassword($username, $data)
    {
        return $this->db->table('mst_user')
            ->where('username', $username)
            ->update($data);
    }
}
