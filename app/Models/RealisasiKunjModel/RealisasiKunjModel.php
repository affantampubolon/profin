<?php

namespace App\Models\RealisasiKunjModel;

use CodeIgniter\Model;

class RealisasiKunjModel extends Model
{
    protected $table      = 'trn_real';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_ref', 'value', 'probability', 'latitude', 'longitude', 'pict', 'description', 'user_update', 'update_date', 'flg_verify', 'user_verify', 'date_verify', 'feedback'];

    //data verifikasi realisasi kunjungan
    public function getDataVerifikasiRealisasi($tanggal, $cabang, $grp_id, $subgrp_id, $clsgrp_id)
    {
        $builder = $this->db->table('trn_plan a')
            ->select("
                b.id, 
                b.id_ref, 
                a.branch_id, 
                f_tv_branch_name(a.branch_id) AS branch_name, 
                a.nik, 
                f_tv_employee_name(a.nik) AS emp_name, 
                a.group_id, 
                a.subgroup_id, 
                a.class_id, 
                f_tv_class_name(a.group_id, a.subgroup_id, a.class_id) AS class_name, 
                a.date, 
                a.cust_id, 
                f_tv_customer_name(a.cust_id) AS cust_name, 
                b.value, 
                b.probability, 
                b.latitude, 
                b.longitude, 
                b.description, 
                b.status
            ")
            ->join('trn_real b', 'a.id = b.id_ref', 'inner')
            ->where('b.flg_verify IS NULL')
            ->where('a.date', $tanggal)
            ->where('a.branch_id', $cabang)
            ->where('a.group_id', $grp_id)
            ->orderBy('emp_name, b.id');

            // Kondisi untuk subgroup_id (jika tidak kosong, tambahkan filter)
            if (!empty($subgrp_id)) {
                $builder->where('a.subgroup_id', $subgrp_id);
            }

            // Kondisi untuk class_id (jika tidak kosong, tambahkan filter)
            if (!empty($clsgrp_id)) {
                $builder->where('a.class_id', $clsgrp_id);
            }

        return $builder->get()->getResult();
    }

    // Update data realisasi kunjungan berdasarkan ID
    public function updateRealisasi($id, $data)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update($data);
    }

    //data monitoring realisasi kunjungan
    public function getDataMonitoringRealisasi($nik, $tanggal_1, $tanggal_2, $grp_id, $subgrp_id, $clsgrp_id)
    {
        $builder = $this->db->table('trn_plan a')
            ->select("
                b.id, 
                b.id_ref, 
                a.branch_id, 
                f_tv_branch_name(a.branch_id) AS branch_name, 
                a.nik, 
                f_tv_employee_name(a.nik) AS emp_name, 
                a.group_id, 
                a.subgroup_id, 
                a.class_id, 
                f_tv_class_name(a.group_id, a.subgroup_id, a.class_id) AS class_name, 
                a.date, 
                a.cust_id, 
                f_tv_customer_name(a.cust_id) AS cust_name, 
                b.value, 
                b.probability, 
                b.latitude, 
                b.longitude, 
                b.description, 
                b.status,
                b.flg_visit
            ")
            ->join('trn_real b', 'a.id = b.id_ref', 'inner')
            ->where('b.flg_verify', 't')
            ->where('a.nik', $nik)
            ->where('a.date >=', $tanggal_1)
            ->where('a.date <=', $tanggal_2)
            ->where('a.group_id', $grp_id)
            ->orderBy('emp_name, b.id');

            // Kondisi untuk subgroup_id (jika tidak kosong, tambahkan filter)
            if (!empty($subgrp_id)) {
                $builder->where('a.subgroup_id', $subgrp_id);
            }

            // Kondisi untuk class_id (jika tidak kosong, tambahkan filter)
            if (!empty($clsgrp_id)) {
                $builder->where('a.class_id', $clsgrp_id);
            }

        return $builder->get()->getResult();
    }

}