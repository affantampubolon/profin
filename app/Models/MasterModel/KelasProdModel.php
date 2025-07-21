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

    //KEBUTUHAN FILTER DATA DENGAN OPSI KESELURUHAN DATA GROUP, SUBGROUP DAN CLASS DIAMBIL
    //Filter Dropdown Group
    public function getFilterGrupBarang()
    {
        // Ambil data dari mst_prod_class
        $data = $this->db->table('mst_prod_class')
            ->distinct()
            ->select('group_id, group_name')
            ->where('flg_used', 't')
            ->get()
            ->getResultArray();

        // Tambahkan baris statis
        $data[] = [
            'group_id' => null,
            'group_name' => 'SEMUA GRUP'
        ];

        // Urutkan data: group_id IS NULL diutamakan, lalu group_id ASC
        usort($data, function ($a, $b) {
            // Prioritaskan group_id yang null
            $aIsNull = is_null($a['group_id']);
            $bIsNull = is_null($b['group_id']);

            if ($aIsNull && !$bIsNull) {
                return -1; // a diutamakan (group_id null)
            } elseif (!$aIsNull && $bIsNull) {
                return 1; // b diutamakan (group_id null)
            } else {
                // Jika keduanya null atau tidak null, urutkan berdasarkan group_id
                return ($a['group_id'] ?? '') <=> ($b['group_id'] ?? '');
            }
        });

        return $data;
    }

    //Filter Dropdown Subgroup
    /**
    * Get distinct subgroup_id dan subgroup_name dari tabel mst_prod_subgrp dengan opsi default "ALL SUBGROUP".
    *
    * @param string $group_prod
    * @return object
    */
    public function getFilterMstprodsubgrp($group_prod) 
    {
        $builder = $this->db->table('mst_prod_class')
            ->distinct()
            ->select('subgroup_id, subgroup_name')
            ->where('flg_used', 't');

        // Hanya filter berdasarkan group_prod jika tidak null atau kosong
        if (!is_null($group_prod) && $group_prod !== '') {
            $builder->where('group_id', $group_prod);
        }

        $data = $builder->get()->getResultArray();

        // Tambahkan baris statis
        $data[] = [
            'subgroup_id' => null,
            'subgroup_name' => 'SEMUA SUBGRUP'
        ];

        // Urutkan data: subgroup_id IS NULL diutamakan, lalu subgroup_id ASC
        usort($data, function ($a, $b) {
            $aIsNull = is_null($a['subgroup_id']);
            $bIsNull = is_null($b['subgroup_id']);
            if ($aIsNull && !$bIsNull) {
                return -1; // SEMUA SUBGRUP di awal
            } elseif (!$aIsNull && $bIsNull) {
                return 1;
            } else {
                return ($a['subgroup_id'] ?? '') <=> ($b['subgroup_id'] ?? '');
            }
        });

        return $data;
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
        $builder = $this->db->table('mst_prod_class')
            ->distinct()
            ->select('class_id, class_name')
            ->where('flg_used', 't');

        // Hanya filter berdasarkan group_prod jika tidak null atau kosong
        if (!is_null($group_prod) && $group_prod !== '') {
            $builder->where('group_id', $group_prod);
        }

        // Hanya filter berdasarkan subgroup_prod jika tidak null atau kosong
        if (!is_null($subgroup_prod) && $subgroup_prod !== '') {
            $builder->where('subgroup_id', $subgroup_prod);
        }

        $data = $builder->get()->getResultArray();

        // Tambahkan baris statis
        $data[] = [
            'class_id' => null,
            'class_name' => 'SEMUA KELAS'
        ];

        // Urutkan data: class_id IS NULL diutamakan, lalu class_id ASC
        usort($data, function ($a, $b) {
            $aIsNull = is_null($a['class_id']);
            $bIsNull = is_null($b['class_id']);
            if ($aIsNull && !$bIsNull) {
                return -1; // SEMUA KELAS di awal
            } elseif (!$aIsNull && $bIsNull) {
                return 1;
            } else {
                return ($a['class_id'] ?? '') <=> ($b['class_id'] ?? '');
            }
        });

        return $data;
    }
}
