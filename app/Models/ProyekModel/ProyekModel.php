<?php

namespace App\Models\ProyekModel;

use CodeIgniter\Model;

class ProyekModel extends Model
{
    protected $tableProyek = 'trn_job_project';

    protected $allowedFields = ['id','no_doc','wbs_no','so_no','job_name','company_id','company_address','company_pic','hp_no','email','job_location','project_manager','inspector','report_no','ar_balance','invoice_send_date','invoice_receive_date','invoice_receive_name','job_start_date','job_finish_date','job_tot_time','contract_amt','revenue_amt','cost_plan_amt', 'cost_real_amt', 'payment_amt', 'progress', 'job_category', 'file_spk', 'flg_used','user_create','create_date','user_update','update_date'];

    public function getProyekFilter()
    {
        $builder = $this->db->table('trn_job_project')
            ->select('id, no_doc, wbs_no, so_no')
            ->where('flg_used', 't')
            ->orderBy('id');

            return $builder->get()->getResult();
    }

    public function getProyekAnggaranFilter()
    {
        $builder = $this->db->table('trn_job_project')
        ->select('id, no_doc, wbs_no, so_no')
        ->where('flg_used', 't')
        ->where('wbs_no <>', '0')
        ->whereNotIn('id', function ($builder) {
            $builder->select('id_ref')
                    ->from('trn_cost_plan')
                    ->where('id_ref IS NOT NULL');
        })
        ->orderBy('id');

        return $builder->get()->getResult();
    }


    public function getProyekRealisasiFilter()
    {
        $db = \Config\Database::connect();

        $query = $db->table('trn_job_project')
            ->select('id, no_doc, wbs_no, so_no')
            ->where('flg_used', 't')
            ->whereIn('id', function ($builder) {
                $builder->select('id_ref')
                        ->from('trn_cost_plan')
                        ->where('id_ref IS NOT NULL');
            })
            ->orderBy('id')
            ->get();

        return $query->getResultArray();
    }

    public function insertProyek($data)
    {
        return $this->db->table($this->tableProyek)->insert($data);
    }

    public function getDataProyek()
    {
        $builder = $this->db->table('trn_job_project')
            ->select('id, no_doc, wbs_no, so_no, job_name, company_id, f_tv_customer_name(company_id) AS company_name,
                     company_address, company_pic, hp_no, email, job_location, project_manager, f_tv_employee_name(project_manager) AS pm_name, inspector, f_tv_employee_name(inspector) AS inspector_name, report_no, ar_balance, invoice_send_date, invoice_receive_date, invoice_receive_name, job_start_date, job_finish_date, job_tot_time, contract_amt, revenue_amt, cost_plan_amt, cost_real_amt, payment_amt, progress, flg_used, user_create, create_date, user_update, update_date')
            ->where('flg_used', 't')
            ->orderBy('id');

            return $builder->get()->getResult();
    }

    public function getDataProyekId($id)
    {
        $builder = $this->db->table('trn_job_project')
            ->select('id, no_doc, wbs_no, so_no, job_name, company_id, f_tv_customer_name(company_id) AS company_name,
                     company_address, company_pic, hp_no, email, job_location, project_manager, f_tv_employee_name(project_manager) AS pm_name, inspector, f_tv_employee_name(inspector) AS inspector_name, report_no, ar_balance, invoice_send_date, invoice_receive_date, invoice_receive_name, job_start_date, job_finish_date, job_tot_time, contract_amt, revenue_amt,cost_plan_amt, cost_real_amt, payment_amt, progress, file_spk, flg_used, user_create, create_date, user_update, update_date')
            ->where('id', $id);

            return $builder->get()->getRow();
    }

    public function updateProyek($id, $data)
    {
        return $this->db->table('trn_job_project')
            ->where('id', $id)
            ->update($data);
    }

}
