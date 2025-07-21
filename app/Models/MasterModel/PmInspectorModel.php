<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class PmInspectorModel extends Model
{

    // Master Tim PM dan Inspector
    public function getPmInspector()
    {
        $builder = $this->db->table('mst_param_emp')
            ->select('id, nik, f_tv_employee_name(nik) AS emp_name, flg_used')
            ->where('flg_used', 't')
            ->where('position_id', 4)
            ->orderBy('nik');

            return $builder->get()->getResult();
    }

    //Filter Project Manager
    public function getFilterPmInspector()
    {
        // Ambil data dari mst_param_emp
        $data = $this->db->table('mst_param_emp')
            ->select('nik, f_tv_employee_name(nik) AS emp_name,')
            ->where('position_id', 4)
            ->where('flg_used', 't')
            ->get()
            ->getResultArray();

        // Tambahkan baris statis
        $data[] = [
            'nik' => null,
            'emp_name' => 'SEMUA PERSONIL'
        ];

        // Urutkan data: nik IS NULL diutamakan, lalu nik ASC
        usort($data, function ($a, $b) {
            // Prioritaskan nik yang null
            $aIsNull = is_null($a['nik']);
            $bIsNull = is_null($b['nik']);

            if ($aIsNull && !$bIsNull) {
                return -1; // a diutamakan (nik null)
            } elseif (!$aIsNull && $bIsNull) {
                return 1; // b diutamakan (nik null)
            } else {
                // Jika keduanya null atau tidak null, urutkan berdasarkan nik
                return ($a['nik'] ?? '') <=> ($b['nik'] ?? '');
            }
        });

        return $data;
    }
}
