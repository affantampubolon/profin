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

    // Data doughnut chart pembayaran invoice
    public function getGraphicPayment($year, $project_manager)
    {
        $builder = $this->db->table('vw_payment_status_rec')
            ->select('COALESCE(SUM(payment_tot),0)::numeric AS payment_tot, COALESCE(SUM(not_payment_tot),0)::numeric AS not_payment_tot, COALESCE(SUM(revenue_tot),0)::numeric AS revenue_tot, TRUNC(COALESCE(NULLIF(sum(payment_tot), 0::numeric) / NULLIF(sum(revenue_tot), 0::numeric) * 100::numeric, 0::numeric), 0)::numeric AS prs_payment, TRUNC(COALESCE(NULLIF(sum(not_payment_tot), 0::numeric) / NULLIF(sum(revenue_tot), 0::numeric) * 100::numeric, 0::numeric), 0)::numeric AS prs_not_payment');
            //  Kondisi untuk tahun
            if (empty($year)) {
                $year = date('Y'); // Gunakan tahun berjalan jika kosong
            }
            $builder->where('year', $year);

            // Kondisi untuk project_manager (jika tidak kosong, tambahkan filter)
            if (!empty($project_manager)) {
                $builder->where('project_manager', $project_manager);
            }

            return $builder->get()->getResult();
    }

    public function getGraphicProject($year, $project_manager)
    {
        // Tentukan tahun default jika kosong
        if (empty($year)) {
            $year = date('Y'); // Gunakan tahun berjalan (2025 saat ini)
        }

        // Buat subquery untuk semua bulan (01 sampai 12) dengan raw query
        $all_months = $this->db->query("SELECT to_char(generate_series, 'FM00') AS month FROM generate_series(1, 12)")->getResult();
        $all_months_subquery = implode(',', array_map(function ($row) {
            return "'{$row->month}'";
        }, $all_months));
        $all_months = "SELECT unnest(ARRAY[{$all_months_subquery}]) AS month";

        // Buat subquery untuk total per bulan
        $tot_project = $this->db->table('vw_project_payment_rec')
            ->select('month')
            ->select([
                'COALESCE(SUM(contract_amt), 0)::numeric AS contract_tot',
                'COALESCE(SUM(project_amt), 0)::numeric AS project_tot',
                'COALESCE(SUM(revenue_amt), 0)::numeric AS revenue_tot',
                'COALESCE(SUM(payment_amt), 0)::numeric AS payment_tot'
            ])
            ->where('year', $year);

        // Kondisi untuk project_manager (jika tidak kosong, tambahkan filter)
        if (!empty($project_manager)) {
            $tot_project->where('project_manager', $project_manager);
        }

        $tot_project->groupBy('month');
        $tot_project_query = $tot_project->getCompiledSelect();

        // Gabungkan subquery dengan LEFT JOIN
        $builder = $this->db->table('(' . $all_months . ') AS trn_month')
            ->select('trn_month.month')
            ->select([
                'COALESCE(SUM(tot.contract_tot) OVER (), 0)::numeric AS contract_tot_year',
                'COALESCE(SUM(tot.revenue_tot) OVER (), 0)::numeric AS revenue_tot_year',
                'COALESCE(SUM(tot.project_tot) OVER (), 0)::numeric AS project_tot_year',
                'COALESCE(tot.project_tot, 0)::numeric AS project_tot',
                'COALESCE(tot.revenue_tot, 0)::numeric AS revenue_tot',
                'COALESCE(tot.payment_tot, 0)::numeric AS payment_tot'
            ])
            ->join('(' . $tot_project_query . ') AS tot', 'trn_month.month = tot.month', 'left')
            ->orderBy('trn_month.month');

        // Eksekusi query
        return $builder->get()->getResult();
    }

    public function getGraphicBudgetProject($year, $project_manager)
    {
        // Tentukan tahun default jika kosong
        if (empty($year)) {
            $year = date('Y'); // Gunakan tahun berjalan (2025 saat ini)
        }

        // Buat subquery untuk semua bulan (01 sampai 12) dengan raw query
        $all_months = $this->db->query("SELECT to_char(generate_series, 'FM00') AS month FROM generate_series(1, 12)")->getResult();
        $all_months_subquery = implode(',', array_map(function ($row) {
            return "'{$row->month}'";
        }, $all_months));
        $all_months = "SELECT unnest(ARRAY[{$all_months_subquery}]) AS month";

        // Buat subquery untuk total per bulan
        $budget_cost = $this->db->table('vw_cost_budget_rec')
            ->select('month')
            ->select([
                'COALESCE(SUM(budget_amt), 0)::numeric AS budget_tot',
                'COALESCE(SUM(real_amt), 0)::numeric AS real_tot',
                'COALESCE(SUM(real_drop_amt), 0)::numeric AS real_drop_tot',
				'trunc(COALESCE(NULLIF(sum(real_amt), 0::numeric) / NULLIF(sum(budget_amt), 0::numeric) * 100::numeric, 0::numeric), 2)::numeric AS prs_real'
            ])
            ->where('year', $year);

        // Kondisi untuk project_manager (jika tidak kosong, tambahkan filter)
        if (!empty($project_manager)) {
            $budget_cost->where('project_manager', $project_manager);
        }

        $budget_cost->groupBy('month');
        $budget_cost_query = $budget_cost->getCompiledSelect();

        // Gabungkan subquery dengan LEFT JOIN
        $builder = $this->db->table('(' . $all_months . ') AS trn_month')
            ->select('trn_month.month')
            ->select([
                'COALESCE(SUM(budget.budget_tot) OVER (), 0)::numeric AS budget_tot_year',
                'COALESCE(SUM(budget.real_tot) OVER (), 0)::numeric AS real_tot_year',
                'COALESCE(budget.budget_tot, 0)::numeric AS budget_tot',
                'COALESCE(budget.real_tot, 0)::numeric AS real_tot',
                'COALESCE(budget.real_drop_tot, 0)::numeric AS real_drop_tot',
		        'COALESCE(budget.prs_real, 0)::numeric AS prs_real'
            ])
            ->join('(' . $budget_cost_query . ') AS budget', 'trn_month.month = budget.month', 'left')
            ->orderBy('trn_month.month');

        // Eksekusi query
        return $builder->get()->getResult();
    }
}
