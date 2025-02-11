<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class SalesMarketingModel extends Model
{

    // Master Tim Sales Marketing Kebutuhan Cabang
    public function getSalesMarketingCab($username)
    {
        $db = \Config\Database::connect();

        // Subquery pertama (x)
        $subQueryX = $db->table('mst_employee a')
            ->select('b.branch_id, a.nik, a.name, c.group_id')
            ->join('mst_param_emp b', 'a.nik = b.nik', 'inner')
            ->join('mst_department c', 'b.department_id = c.id', 'inner')
            ->where('b.position_id', 14)
            ->where('b.flg_used', 't');

        // Subquery kedua (y)
        $subQueryY = $db->table('mst_user a')
            ->select('b.branch_id, c.group_id')
            ->join('mst_param_emp b', 'a.id_ref = b.id', 'inner')
            ->join('mst_department c', 'b.department_id = c.id', 'inner')
            ->where('a.username', $username);

        // Query utama yang menggabungkan subquery x dan y
        $query = $db->table("({$subQueryX->getCompiledSelect()}) x")
            ->select('x.nik, x.name')
            ->join("({$subQueryY->getCompiledSelect()}) y", 'x.branch_id = y.branch_id AND x.group_id = y.group_id', 'inner')
            ->orderBy('x.name')
            ->get();

        return $query->getResult();
    }
}
