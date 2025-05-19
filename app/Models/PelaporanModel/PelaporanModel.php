<?php

namespace App\Models\PelaporanModel;

use CodeIgniter\Model;

class PelaporanModel extends Model
{
    public function getDataAktivitasSales($cabang, $nik)
    {
        $builder = $this->db->table('vw_report_sales_activity')
            ->select('branch_id, branch_name, nik, emp_name, cust_id, cust_name, latitude, longitude, flg_noo');
            // Kondisi untuk branch_id (jika tidak kosong, tambahkan filter)
            if (!empty($cabang)) {
                $builder->where('branch_id', $cabang);
            }

            // Kondisi untuk nik (jika tidak kosong, tambahkan filter)
            if (!empty($nik)) {
                $builder->where('nik', $nik);
            }

            return $builder->get()->getResult();
    }

    public function getDataDistribusiProd($cabang, $nik, $pelanggan)
    {
        $builder = $this->db->table('vw_report_distribution_prod')
            ->select('date, branch_id, branch_name, nik, emp_name, group_id, group_name, subgroup_id, subgroup_name, class_id, class_name, cust_id, cust_name, tot_real_value, tot_target_value, flg_non_route, flg_visit')
            ->where('branch_id', $cabang)
            ->where('nik', $nik)
            ->where('cust_id', $pelanggan);

            return $builder->get()->getResult();
    }
}
