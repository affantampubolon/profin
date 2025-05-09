<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class KelasProdModel extends Model
{
    protected $table = 'mst_prod_class';
    protected $allowedFields = ['group_id', 'subgroup_id', 'class_id', 'group_name', 'subgroup_name', 'class_name'];

    //Master Kelas Produk
    public function getDataMstKlsProd($grupprod)
    {
        $builder = $this->db->table('mst_prod_class')
            ->select("
                id,
                group_id,
                subgroup_id,
                class_id,
                group_name,
                subgroup_name,
                class_name,
                flg_used
            ")
            ->where('group_id', $grupprod)
            ->orderBy('id');

        return $builder->get()->getResult();
    }

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

        return $query->getRowArray();
    }

    /**
     * Get distinct subgroup_id and subgroup_name where flg_used = 't' and group_id = [$group_prod].
     * 
     * @param string $group_prod
     * @return array
     */
    public function getSubGrupBarang($grp_prod)
    {
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

    //KEBUTUHAN FILTER DATA DENGAN OPSI KESELURUHAN DATA SUBGROUP DAN CLASS DIAMBIL
    //Filter Dropdown Subgroup
    /**
    * Get distinct subgroup_id dan subgroup_name dari tabel mst_prod_subgrp dengan opsi default "ALL SUBGROUP".
    *
    * @param string $group_prod
    * @return object
    */
    public function getFilterMstprodsubgrp($group_prod) 
    {
        $query = "SELECT subgroup_id, subgroup_name
                    FROM (SELECT DISTINCT a.subgroup_id, a.subgroup_name
                        FROM mst_prod_class a
						WHERE group_id = '" . $group_prod . "'
						UNION ALL
						SELECT NULL,'Semua Subgrup') b 
				  ORDER BY subgroup_id IS NOT NULL, subgroup_id ASC";
        return $this->db->query($query);
    }


    //Filter Dropdown Class
    /**
    * Get distinct class_id dan class_name dari tabel mst_prod_class dengan opsi default "ALL CLASS GROUP".
    *
    * @param string $group_prod
    * @param string $subgroup_prod
    * @return object
    */
    public function getFilterMstclass($group_prod, $subgroup_prod) 
    {
        $query = "SELECT class_id, class_name
                  FROM (SELECT DISTINCT a.class_id, a.class_name
                        FROM mst_prod_class a
                        WHERE group_id = '" . $group_prod . "'
                        AND subgroup_id = '" . $subgroup_prod . "'
							UNION ALL
							SELECT NULL,'Semua Kelas') b
				  ORDER BY class_id IS NOT NULL, class_id ASC";
        return $this->db->query($query);
    }

}
