<?php

namespace App\Models\MonitoringModel;

use CodeIgniter\Model;

class MonitoringModel extends Model
{

    protected $tableDetProyek = 'vw_det_project';

    // Detail data proyek
    public function getDetProyek($tahun = '')
    {
        $builder = $this->db->table('vw_det_project')
            ->select('id, year, month, no_spi, wbs_no, so_no, job_name, company_id, company_name, 
                      company_address, company_pic, hp_no, email, job_location, project_manager, pm_name, 
                      inspector, insp_name, report_no, ar_balance, invoice_send_date, invoice_receive_date, 
                      invoice_receive_name, job_start_date, job_finish_date, job_tot_time, contract_amt, revenue_amt, 
                      cost_plan_amt, cost_real_amt, payment_amt, last_payment_amt, last_payment_date, prs_payment, status_payment, reason, progress, progress_name, user_create_project, create_date_project, 
                      user_update_project, update_date_project,
	                  id_ref, COALESCE(budget_amt, 0)::numeric AS budget_amt, COALESCE(real_amt, 0)::numeric AS real_amt, prs_achiev,
                      COALESCE(req_drop_amt, 0)::numeric AS req_drop_amt, COALESCE(real_drop_amt, 0)::numeric AS real_drop_amt, 
                      COALESCE(net_plan_amt, 0)::numeric AS net_plan_amt,
	                  user_create_budget, emp_name_budget, create_date_budget,
	                  user_create_real, emp_name_real, create_date_real,
	                  user_create_drop, emp_name_drop, create_date_drop')

            ->orderBy('year, month, id');

            //  Kondisi untuk tahun
            if (empty($tahun)) {
                $tahun = date('Y'); // Gunakan tahun berjalan jika kosong
            }
            $builder->where('year', $tahun);

            return $builder->get()->getResult();
    }

    public function getDetProyekkId($id)
    {
        $builder = $this->db->table('vw_det_project')
            ->select('id, year, month, no_spi, wbs_no, so_no, job_name, company_id, company_name, 
                      company_address, company_pic, hp_no, email, job_location, project_manager, pm_name, 
                      inspector, insp_name, report_no, ar_balance, invoice_send_date, invoice_receive_date, 
                      invoice_receive_name, job_start_date, job_finish_date, job_tot_time, contract_amt, revenue_amt, 
                      cost_plan_amt, cost_real_amt, payment_amt, progress, progress_name, user_create_project, create_date_project, 
                      user_update_project, update_date_project,
	                  id_ref, COALESCE(budget_amt, 0)::numeric AS budget_amt, COALESCE(real_amt, 0)::numeric AS real_amt, prs_achiev,
                      COALESCE(req_drop_amt, 0)::numeric AS req_drop_amt, COALESCE(real_drop_amt, 0)::numeric AS real_drop_amt, 
                      COALESCE(net_plan_amt, 0)::numeric AS net_plan_amt,
	                  user_create_budget, emp_name_budget, create_date_budget,
	                  user_create_real, emp_name_real, create_date_real,
	                  user_create_drop, emp_name_drop, create_date_drop')
            ->where('id', $id);

            return $builder->get()->getRow();
    }
    
    // Data Anggaran dan Biaya
    public function getDataAnggaranBiaya($nowbs)
    {
        $builder = $this->db->table('vw_data_budget_project')
            ->select('id, no_doc, id_ref, wbs_no, coa, coa_name, budget_amt, real_amt, req_drop_amt, real_drop_amt,
                     net_plan_amt')
            ->where('id_ref', $nowbs)
            ->orderBy('coa');

            return $builder->get()->getResult();
    }

    // Data Detail Realisasi
    public function getDataDetRealisasi($nowbs, $coa)
    {
        $builder = $this->db->table('trn_cost_real')
            ->select('id, no_doc, id_ref, coa, description, real_amt, f_tv_employee_name(user_create) AS emp_name, create_date')
            ->where('id_ref', $nowbs)
            ->where('coa', $coa)
            ->orderBy('create_date');

            return $builder->get()->getResult();
    }

    // Data Detail Dropping
    public function getDataDetDropping($nowbs, $coa)
    {
        $builder = $this->db->table('trn_cost_dropping')
            ->select('id, no_doc, id_ref, coa, description, real_drop_amt, f_tv_employee_name(user_create) AS emp_name, create_date')
            ->where('id_ref', $nowbs)
            ->where('coa', $coa)
            ->orderBy('create_date');

            return $builder->get()->getResult();
    }

    // Data Pembayaran Piutang Unduh
    public function getDataPembayaranPiutangDet($tahun)
    {
        $builder = $this->db->table('vw_summary_payment_det')
            ->select('id, year, month, 
                     wbs_no, so_no, company_name, job_name, no_doc, invoice_date, payment_date, period_payment,
                     description, reason, payment_amt, emp_name')
            ->orderBy('year, month, id');

            //  Kondisi untuk tahun
            if (empty($tahun)) {
                $tahun = date('Y'); // Gunakan tahun berjalan jika kosong
            }
            $builder->where('year', $tahun);

            return $builder->get()->getResult();
    }
    
    // Data Pembayaran Piutang
    public function getDataPembayaranPiutang($tahun = '')
    {
        $builder = $this->db->table('vw_summary_payment')
            ->select('id, year, month, 
                     wbs_no, so_no, f_tv_customer_name(company_id) AS company_name, job_name, revenue_amt, 
                     payment_amt, ar_balance')
            ->orderBy('year, month, id');

            //  Kondisi untuk tahun
            if (empty($tahun)) {
                $tahun = date('Y'); // Gunakan tahun berjalan jika kosong
            }
            $builder->where('year', $tahun);

            return $builder->get()->getResult();
    }

    // Data Detail Pembayaran Piutang
    public function getDataDetPembayaranPiutang($idref)
    {
        $builder = $this->db->table('vw_payment_hist')
            ->select('no_doc, id_ref, create_date, description, reason, payment_amt, invoice_date, payment_date, period_payment, emp_name')
            ->where('id_ref', $idref)
            ->orderBy('create_date');

            return $builder->get()->getResult();
    }
}
