<?php

namespace App\Models\BerandaModel;

use CodeIgniter\Model;

class BerandaModel extends Model
{
    public function getDataPencapaianCab($cabang)
    {
        $builder = $this->db->table('vw_report_achiev_branch')
            ->select('year, month, branch_id, tot_amt_pipeline, tot_amt_real, prs_achiev')
            ->where('branch_id', $cabang);

            return $builder->get()->getResult();
    }

    public function getDataVerifikasiTertundaCab($cabang)
    {
        $builder = $this->db->table('vw_report_pending_job_emp')
            ->select('branch_id, nik, emp_name, branch_name, tot_job_pipeline, tot_job_plan, tot_job_real')
            ->where('branch_id', $cabang)
            ->orderBy('branch_id, nik');

            return $builder->get()->getResult();
    }

    public function getDataVerifikasiTertundaSales($cabang, $nik)
    {
        $builder = $this->db->table('vw_report_pending_job_emp')
            ->select('branch_id, nik, emp_name, branch_name, tot_job_pipeline, tot_job_plan, tot_job_real')
            ->where('branch_id', $cabang)
            ->where('nik', $nik)
            ->orderBy('branch_id, nik');

            return $builder->get()->getResult();
    }
}
