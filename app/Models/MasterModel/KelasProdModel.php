<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class KelasProdModel extends Model
{
    protected $table = 'mst_prod_class';
    protected $allowedFields = ['group_id', 'subgroup_id', 'class_id', 'group_name', 'subgroup_name', 'class_name'];

    /**
     * Get distinct group_id and group_name where flg_used = 't'.
     * 
     * @return array
     */
    public function getGrupBarang($username)
    {
        $db = \Config\Database::connect();

        $query = $db->table('mst_department c')
            ->select('c.group_id, f_tv_group_name(c.group_id) as group_name')
            ->join('mst_param_emp p', 'p.department_id = c.id', 'inner')
            ->join('mst_user u', 'u.id_ref = p.id', 'inner')
            ->where('u.username', $username)
            ->get();

        return $query->getResult();
    }

    /**
     * Get distinct subgroup_id and subgroup_name where flg_used = 't' and group_id = [$group_prod].
     * 
     * @param string $group_prod
     * @return array
     */
    public function getSubGrupBarang($grp_prod)
    {
        if (!$grp_prod) return [];
        return $this->distinct()
            ->select('subgroup_id, subgroup_name')
            ->where('flg_used', 't')
            ->where('group_id', $grp_prod)
            ->orderBy('subgroup_id')
            ->findAll();
    }

    /**
     * Get distinct class_id and class_name where flg_used = 't', group_id = [$group_prod], and subgroup_id = [$subgroup_prod].
     * 
     * @param string $group_prod
     * @param string $subgroup_prod
     * @return array
     */
    public function getKelasBarang($grp_prod, $subgrp_prod)
    {
        if (!$grp_prod || !$subgrp_prod) return [];
        return $this->distinct()
            ->select('class_id, class_name')
            ->where('flg_used', 't')
            ->where('group_id', $grp_prod)
            ->where('subgroup_id', $subgrp_prod)
            ->orderBy('class_id')
            ->findAll();
    }

}
