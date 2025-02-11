<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class SkalaProbabilitasModel extends Model
{

    // Master Skala Probabilitas
    public function getSkalaProbabilitas($username)
    {
        $db = \Config\Database::connect();

        $query = $db->table('mst_user a')
            ->select('y.scale, y.description')
            ->join('mst_param_emp b', 'a.id_ref = b.id', 'inner')
            ->join('mst_department c', 'b.department_id = c.id', 'inner')
            ->join('mst_scale_probability y', 'c.group_id = y.group_id', 'inner')
            ->where('a.username', $username)
            ->orderBy('y.id')
            ->get();

        return $query->getResultArray();
    }
}
