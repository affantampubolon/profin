<?php

namespace App\Models\RencanaKunjModel;

use CodeIgniter\Model;

class RencanaKunjModel extends Model
{
    protected $table      = 'trn_plan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['branch_id', 'nik', 'group_id', 'subgroup_id', 'class_id', 'date', 'cust_id', 'cust_user_id', 'description', 'user_update', 'update_date', 'flg_approve', 'user_approve', 'date_approve', 'reason_reject'];

    //data verifikasi rencana kunjungan
    public function getDataVerifikasiRencana($nik, $tanggal)
    {
        $builder = $this->db->table('trn_plan')
            ->select("
                DISTINCT ON (cust_id) cust_id,
                id,
                branch_id, 
                f_tv_branch_name(branch_id) AS branch_name, 
                nik,
                f_tv_employee_name(nik) AS emp_name, 
                f_tv_class_name(group_id, subgroup_id, class_id) AS class_name,
                date,
                f_tv_customer_name(cust_id) AS cust_name,
                f_tv_user_cust_name(cust_user_id) AS cust_user_name, 
                description
            ") 
            ->where('nik', $nik)
            ->where('date', $tanggal)
            ->where('flg_approve IS NULL')
            ->whereIn('status', [0, 1, 3, 4])
            ->orderBy('cust_id, date');

            return $builder->get()->getResult();
    }

    //data detail verifikasi rencana kunjungan
    public function getDataVerifikasiRencanaDet($nik, $tanggal, $pelanggan)
    {
        $builder = $this->db->table('trn_plan')
            ->select("
                f_tv_user_cust_name(cust_user_id) AS cust_user_name,
                group_id,
                f_tv_group_name(group_id) AS group_name,
                subgroup_id,
                f_tv_subgroup_name(group_id, subgroup_id) AS subgroup_name,
                class_id, 
                f_tv_class_name(group_id, subgroup_id, class_id) AS class_name,
            ") 
            ->where('nik', $nik)
            ->where('date', $tanggal)
            ->where('cust_id', $pelanggan)
            ->where('flg_approve IS NULL')
            ->whereIn('status', [0, 1, 3, 4])
            ->orderBy('group_id, subgroup_id, class_id');

            return $builder->get()->getResult();
    }

    // Update data rencana kunjungan
    public function updateRencana($cust_id, $nik, $date, $data)
    {
        return $this->db->table($this->table)
            ->where('cust_id', $cust_id)
            ->where('nik', $nik)
            ->where('date', $date)
            ->update($data);
    }

    //data monitoring rencana kunjungan
    public function getDataMonitoringRencana($nik, $tanggal_1, $tanggal_2)
    {
        $builder = $this->db->table('trn_plan')
            ->select("
                DISTINCT ON (cust_id) cust_id,
                id,
                branch_id, 
                f_tv_branch_name(branch_id) AS branch_name, 
                nik,
                f_tv_employee_name(nik) AS emp_name, 
                f_tv_class_name(group_id, subgroup_id, class_id) AS class_name,
                date,
                f_tv_customer_name(cust_id) AS cust_name,
                f_tv_user_cust_name(cust_user_id) AS cust_user_name, 
                description,
                flg_non_route,
                flg_absence
            ") 
            ->where('nik', $nik)
            ->where('date >=', $tanggal_1)
            ->where('date <=', $tanggal_2)
            ->where('flg_approve', 't')
            ->orderBy('cust_id, date');

            return $builder->get()->getResult();
    }

    //data detail monitoring rencana kunjungan
    public function getDataMonitoringRencanaDet($nik, $tanggal, $pelanggan)
    {
        $builder = $this->db->table('trn_plan')
            ->select("
                f_tv_user_cust_name(cust_user_id) AS cust_user_name,
                group_id,
                f_tv_group_name(group_id) AS group_name,
                subgroup_id,
                f_tv_subgroup_name(group_id, subgroup_id) AS subgroup_name,
                class_id, 
                f_tv_class_name(group_id, subgroup_id, class_id) AS class_name,
            ") 
            ->where('nik', $nik)
            ->where('date', $tanggal)
            ->where('cust_id', $pelanggan)
            ->where('flg_approve', 't')
            ->orderBy('group_id, subgroup_id, class_id');

            return $builder->get()->getResult();
    }

    //data buka verifikasi rencana kunjungan
    public function getDataBukaVerifikasiRencana($nik, $tanggal)
    {
        $builder = $this->db->table('trn_plan')
            ->select("
                DISTINCT ON (cust_id) cust_id,
                id,
                branch_id, 
                f_tv_branch_name(branch_id) AS branch_name, 
                nik,
                f_tv_employee_name(nik) AS emp_name, 
                f_tv_class_name(group_id, subgroup_id, class_id) AS class_name,
                date,
                f_tv_customer_name(cust_id) AS cust_name,
                f_tv_user_cust_name(cust_user_id) AS cust_user_name, 
                description
            ") 
            ->where('nik', $nik)
            ->where('date', $tanggal)
            ->where('flg_approve IS NULL')
            ->where('status', 5)
            ->orderBy('cust_id, date');

            return $builder->get()->getResult();
    }

    //data detail buka verifikasi rencana kunjungan
    public function getDataBukaVerifikasiRencanaDet($nik, $tanggal, $pelanggan)
    {
        $builder = $this->db->table('trn_plan')
            ->select("
                f_tv_user_cust_name(cust_user_id) AS cust_user_name,
                group_id,
                f_tv_group_name(group_id) AS group_name,
                subgroup_id,
                f_tv_subgroup_name(group_id, subgroup_id) AS subgroup_name,
                class_id, 
                f_tv_class_name(group_id, subgroup_id, class_id) AS class_name,
            ") 
            ->where('nik', $nik)
            ->where('date', $tanggal)
            ->where('cust_id', $pelanggan)
            ->where('flg_approve IS NULL')
            ->where('status', 5)
            ->orderBy('group_id, subgroup_id, class_id');

            return $builder->get()->getResult();
    }

    // Update data buka verifikasi rencana kunjungan
    public function updateBukaVerifRencana($cust_id, $nik, $date, $data)
    {
        return $this->db->table($this->table)
            ->where('cust_id', $cust_id)
            ->where('nik', $nik)
            ->where('date', $date)
            ->update($data);
    }
}