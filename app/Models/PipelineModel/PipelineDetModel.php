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
        $query =    "SELECT a.year, a.month, a.nik, a.group_id, a.subgroup_id, a.class_id, f_tv_class_name(a.group_id, a.subgroup_id, a.class_id) class_name,
                    b.id, b.id_ref, b.cust_id, f_tv_customer_name(b.cust_id) AS cust_name, 
                    b.freq_visit, b.target_value, b.probability
                    FROM trn_pipeline a, trn_pipeline_det b
	                    WHERE a.id = b.id_ref
	                    AND a.nik = '" . $username . "'
                    AND a.year = '" . $tahun . "'
                    AND a.month = '" . $bulan . "'
                    AND a.group_id = '" . $group_id . "'
                    AND a.subgroup_id = CASE WHEN '" . $subgroup_id . "' = '' THEN a.subgroup_id ELSE '" . $subgroup_id . "' END
	                    AND a.class_id = CASE WHEN '" . $class_id . "' = '' THEN a.class_id ELSE '" . $class_id . "' END	
                    AND b.flg_approve IS NULL
                        ";
        return $this->db->query($query)->getResult();
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

    
    //data monitoring pipeline
    public function getDataPipelineMonitoring($nik, $tahun, $bulan, $grp_id, $subgrp_id, $clsgrp_id)
    {
        $query =    "SELECT a.year, a.month, a.nik, a.group_id, a.subgroup_id, a.class_id, f_tv_class_name(a.group_id, a.subgroup_id, a.class_id) class_name,
                     b.id, b.id_ref, b.cust_id, f_tv_customer_name(b.cust_id) AS cust_name, 
                     b.freq_visit, b.target_value, b.real_value, b.adj_value, b.probability
                     FROM trn_pipeline a, trn_pipeline_det b
	                     WHERE a.id = b.id_ref
	                     AND a.nik = [$nik]
                         AND a.year = [$tahun]
                         AND a.month = [$bulan]
                         AND a.group_id = [$grp_id]
                         AND a.subgroup_id = CASE WHEN [$subgrp_id] = '' THEN a.subgroup_id ELSE [$subgrp_id] END
	                     AND a.class_id = CASE WHEN [$clsgrp_id] = '' THEN a.class_id ELSE [$clsgrp_id] END
					     AND b.flg_approve = 't'
				     ORDER BY b.id, a.class_id
                        ";
        return $this->db->query($query)->getResult();
    }

}