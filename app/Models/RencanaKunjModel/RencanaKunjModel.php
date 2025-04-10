<?php

namespace App\Models\RencanaKunjModel;

use CodeIgniter\Model;

class RencanaKunjModel extends Model
{
    protected $table      = 'trn_plan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['branch_id', 'nik', 'group_id', 'subgroup_id', 'class_id', 'date', 'cust_id', 'cust_user_id', 'description', 'user_update', 'update_date', 'flg_approve', 'user_approve', 'date_approve', 'reason_reject'];

    //data verifikasi rencana kunjungan
    public function getDataVerifikasiRencana($nik, $tanggal, $grp_id, $subgrp_id, $clsgrp_id)
    {
        $builder = $this->db->table('trn_plan')
            ->select("
                id, 
                branch_id, 
                f_tv_branch_name(branch_id) AS branch_name, 
                nik,
                f_tv_employee_name(nik) AS emp_name,
                group_id,
                subgroup_id, 
                class_id, 
                f_tv_class_name(group_id, subgroup_id, class_id) AS class_name,
                date,
                cust_id, 
                f_tv_customer_name(cust_id) AS cust_name,
                f_tv_user_cust_name(cust_user_id) AS cust_user_name, 
                description
            ")
            ->where('nik', $nik)
            ->where('date', $tanggal)
            ->where('group_id', $grp_id)
            ->where('flg_approve IS NULL')
            ->orderBy('id, class_id');

            // Kondisi untuk subgroup_id (jika tidak kosong, tambahkan filter)
            if ($subgrp_id !== '') {
                $builder->where('subgroup_id', $subgrp_id);
            }

            // Kondisi untuk class_id (jika tidak kosong, tambahkan filter)
            if ($clsgrp_id !== '') {
                $builder->where('class_id', $clsgrp_id);
            }

            return $builder->get()->getResult();
    }

    // Update data rencana kunjungan berdasarkan ID
    public function updateRencana($id, $data)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update($data);
    }

    //data monitoring rencana kunjungan
    public function getDataMonitoringRencana($nik, $tanggal_1, $tanggal_2, $grp_id, $subgrp_id, $clsgrp_id)
    {
        $builder = $this->db->table('trn_plan')
            ->select("
                id, 
                branch_id, 
                f_tv_branch_name(branch_id) AS branch_name, 
                nik,
                f_tv_employee_name(nik) AS emp_name,
                group_id,
                subgroup_id, 
                class_id, 
                f_tv_class_name(group_id, subgroup_id, class_id) AS class_name,
                date,
                cust_id, 
                f_tv_customer_name(cust_id) AS cust_name,
                f_tv_user_cust_name(cust_user_id) AS cust_user_name, 
                description
            ")
            ->where('nik', $nik)
            ->where('date >=', $tanggal_1)
            ->where('date <=', $tanggal_2)
            ->where('group_id', $grp_id)
            ->where('flg_approve', 't')
            ->orderBy('date, id, class_id');

            // Kondisi untuk subgroup_id (jika tidak kosong, tambahkan filter)
            if ($subgrp_id !== '') {
                $builder->where('subgroup_id', $subgrp_id);
            }

            // Kondisi untuk class_id (jika tidak kosong, tambahkan filter)
            if ($clsgrp_id !== '') {
                $builder->where('class_id', $clsgrp_id);
            }

            return $builder->get()->getResult();
    }

}