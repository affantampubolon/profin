<?php

namespace App\Models\PelaporanModel;

use CodeIgniter\Model;

class PelaporanModel extends Model
{
    public function getDataAktivitasSales($tanggal_1, $tanggal_2, $cabang, $nik)
    {
        $builder = $this->db->table('vw_report_sales_activity')
            ->select('date, branch_id, branch_name, nik, emp_name, cust_id, cust_name, latitude, longitude, flg_noo')
            ->where('date >=', $tanggal_1)
            ->where('date <=', $tanggal_2);
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

    public function getDataDistribusiProd($tgl, $cabang, $nik, $pelanggan)
    {
        $builder = $this->db->table('vw_report_distribution_prod')
            ->select('month, date, branch_id, branch_name, nik, emp_name, group_id, group_name, subgroup_id, subgroup_name, class_id, class_name, cust_id, cust_name, tot_real_value, tot_target_value, prs_value, flg_non_route, flg_visit')
            ->where('date', $tgl)
            ->where('branch_id', $cabang)
            ->where('nik', $nik)
            ->where('cust_id', $pelanggan)
            ->where("TO_CHAR(date, 'YYYY') = TO_CHAR(CURRENT_DATE, 'YYYY')", NULL, FALSE)
            ->orderBy('month, date, nik, group_id, subgroup_id, class_id, cust_id');

            return $builder->get()->getResult();
    }

    public function getDataDistribusiProdLoc($tanggal_1, $tanggal_2, $cabang, $grupprod, $subgrupprod, $klsgrupprod)
    {
        $builder = $this->db->table('vw_report_distribution_prod_loc')
            ->select('date, branch_id, branch_name, nik, emp_name, group_id, group_name, subgroup_id, subgroup_name, class_id, class_name, cust_id, cust_name, tot_real_value, tot_target_value, latitude, longitude, flg_non_route, flg_visit, flg_noo')
            ->where('date >=', $tanggal_1)
            ->where('date <=', $tanggal_2);
            // Kondisi untuk branch_id (jika tidak kosong, tambahkan filter)
            if (!empty($cabang)) {
                $builder->where('branch_id', $cabang);
            }

            // Kondisi untuk grup (jika tidak kosong, tambahkan filter)
            if (!empty($grupprod)) {
                $builder->where('group_id', $grupprod);
            }

            // Kondisi untuk subgrup (jika tidak kosong, tambahkan filter)
            if (!empty($subgrupprod)) {
                $builder->where('subgroup_id', $subgrupprod);
            }

            // Kondisi untuk kelas (jika tidak kosong, tambahkan filter)
            if (!empty($klsgrupprod)) {
                $builder->where('class_id', $klsgrupprod);
            }

            return $builder->get()->getResult();
    }

    public function getDataKunjunganSales($cabang)
    {
        $builder = $this->db->table('vw_report_cust_hc_cons')
            ->select('year, month, branch_id, branch_name, nik, emp_name, cust_id, cust_name, real_call, target_call, prs_call, real_ec, target_ec, prs_ec, real_value, target_value, prs_value')
            ->orderBy('month, nik, cust_id');

            // Kondisi untuk branch_id (jika tidak kosong, tambahkan filter)
            if (!empty($cabang)) {
                $builder->where('branch_id', $cabang);
            }

            return $builder->get()->getResult();
    }

    public function getDataKunjMarketingPenggunaan($cabang)
    {
        $builder = $this->db->table('vw_report_act_marketing')
            ->select('year, month, branch_id, nik, emp_name, tot_real_plan, tot_target_plan, prs_plan, tot_real, tot_target_real, prs_real')
            ->orderBy('month, nik');

            // Kondisi untuk branch_id (jika tidak kosong, tambahkan filter)
            if (!empty($cabang)) {
                $builder->where('branch_id', $cabang);
            }

            return $builder->get()->getResult();
    }

    public function getDataKunjMarketingOutlet($subgrupprod, $klsgrupprod)
    {
        $builder = $this->db->table('vw_report_marketing_outlet')
            ->select('branch_id, branch_name, tot_visit_inst, tot_visit_lab, tot_visit_rs, tot_visit_rs_swasta, tot_visit, tot_customer, TRUNC(COALESCE(((NULLIF(tot_visit,0))/(NULLIF(tot_customer,0)))::numeric *100,0),2)::numeric AS prs_er')
            ->where('subgroup_id', $subgrupprod)
            ->where('class_id', $klsgrupprod)
            ->orderBy('branch_id');

            return $builder->get()->getResult();
    }

    public function getDataKunjMarketingUser($cabang, $nik, $subgrupprod, $klsgrupprod)
    {
        $builder = $this->db->table('vw_report_marketing_user_cust')
            ->select('cust_user_name, cust_name, user_cat, tot_jan, tot_feb, tot_mar, tot_apr, tot_mei, tot_jun, tot_jul, tot_agt, tot_sep, tot_okt, tot_nov, tot_des, tot_visit')
            ->where('subgroup_id', $subgrupprod)
            ->where('class_id', $klsgrupprod)
            ->orderBy('cust_user_name, cust_name, user_cat_name');

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

    public function getDataKunjMarketingUserCat($cabang, $subgrupprod, $klsgrupprod)
    {
        $builder = $this->db->table('vw_report_marketing_user_cat')
            ->select('emp_name, tot_mnj, tot_mds, tot_non_mds, tot_dll, tot_visit')
            ->where('subgroup_id', $subgrupprod)
            ->where('class_id', $klsgrupprod)
            ->orderBy('emp_name');

            // Kondisi untuk branch_id (jika tidak kosong, tambahkan filter)
            if (!empty($cabang)) {
                $builder->where('branch_id', $cabang);
            }

            return $builder->get()->getResult();
    }
}
