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
    public function getMstPelanggan()
    {
        return $this
            ->select(['cust_id', 'cust_name'])
            ->where('flg_used', 't')
            ->where('branch_id', '11')
            ->orderBy('cust_id')
            ->findAll();
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
