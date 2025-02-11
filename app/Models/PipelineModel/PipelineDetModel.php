<?php

namespace App\Models\PipelineModel;

use CodeIgniter\Model;

class PipelineDetModel extends Model
{
    protected $table      = 'trn_pipeline_det';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_ref', 'cust_id', 'freq_visit', 'target_value', 'probability', 'user_update', 'update_date', 'flg_approve', 'user_approve', 'date_approve', 'reason_reject'];

    //data draft pipeline
    public function getDataPipelineDet($username, $tahun, $bulan, $group_id, $subgroup_id, $class_id)
    {
        return $this->db->table('trn_pipeline a')
            ->select('a.year, a.month, a.nik, a.group_id, a.subgroup_id, a.class_id, 
                    b.id, b.id_ref, b.cust_id, f_tv_customer_name(b.cust_id) AS cust_name, 
                    b.freq_visit, b.target_value, b.probability')
            ->join('trn_pipeline_det b', 'a.id = b.id_ref')
            ->where('a.nik', $username)
            ->where('a.year', $tahun)
            ->where('a.month', $bulan)
            ->where('a.group_id', $group_id)
            ->where('a.subgroup_id', $subgroup_id)
            ->where('a.class_id', $class_id)
            ->where('b.flg_approve', null)
            ->orderBy('b.id')
            ->get()
            ->getResultArray();
    }

    // Update data draft pipeline berdasarkan ID
    public function updatePipelineDet($id, $data)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update($data);
    }

    // Hapus data draft pipeline berdasarkan ID
    public function deletePipelineDet($id)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->delete();
    }

    //data verifikasi pipeline
    public function getDataPipelineVerifikasi($nik, $thn, $bln, $grp_prod, $subgrp_prod)
    {
        return $this->db->table('trn_pipeline a')
            ->select('a.year, a.month, a.nik, a.group_id, a.subgroup_id, a.class_id, 
                    b.id, b.id_ref, b.cust_id, f_tv_customer_name(b.cust_id) AS cust_name, 
                    b.freq_visit, b.target_value, b.probability')
            ->join('trn_pipeline_det b', 'a.id = b.id_ref')
            ->where('a.nik', $nik)
            ->where('a.year', $thn)
            ->where('a.month', $bln)
            ->where('a.group_id', $grp_prod)
            ->where('a.subgroup_id', $subgrp_prod)
            ->where('b.flg_approve', null)
            ->orderBy('b.id')
            ->get()
            ->getResultArray();
    }

}