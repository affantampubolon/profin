<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table = 'mst_customer';
    protected $allowedFields = ['branch_id', 'cust_id', 'cust_name'];

    /**
     * Get distinct group_id and group_name where flg_used = 't'.
     * 
     * @return array
     */
    // Master Pelanggan
    public function getMstPelangganCab($username)
    {
        $db = \Config\Database::connect();

        $query = $db->table('mst_customer c')
            ->select('c.cust_id, c.cust_name')
            ->join('mst_param_emp p', 'p.branch_id = c.branch_id', 'inner')
            ->join('mst_user u', 'u.id_ref = p.id', 'inner')
            ->where('u.username', $username)
            ->orderBy('c.cust_id')
            ->get();

        return $query->getResultArray();
    }

    // Kategori Pelanggan
    public function getMstKategoriPelanggan()
    {
        return $this->db->table('mst_category_cust')
            ->select('id, category_id, category_name, flg_pharmacist')
            ->where('flg_used', 't')
            ->orderBy('category_id')
            ->get()
            ->getResultArray();
    }
}
