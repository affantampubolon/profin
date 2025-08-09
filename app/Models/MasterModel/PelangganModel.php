<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table = 'mst_customer';
    protected $allowedFields = ['branch_id', 'cust_id', 'cust_name'];
    protected $primaryKey = ['branch_id', 'id'];

    /**
     * Get distinct group_id and group_name where flg_used = 't'.
     * 
     * @return array
     */

    //Master Pelanggan
    public function getDataMstPelanggan($cabang)
    {
        $builder = $this->db->table('mst_customer')
            ->select("
                id,
                branch_id,
                cust_id,
                cust_name,
                address,
                email,
                phone_no,
                npwp,
                cust_name_tax,
                address_tax,
                pic_name,
            ")
            ->where('branch_id', $cabang)
            ->orderBy('id');

        return $builder->get()->getResult();
    }

    //Master Filter Pelanggan
    public function getDataFilterMstPelanggan()
    {
        $builder = $this->db->table('mst_customer')
            ->select("
                id,
                branch_id,
                cust_id,
                cust_name,
                address,
                pic_name,
                email,
                phone_no
            ")
            ->where('flg_used', 't')
            ->orderBy('cust_id');

        return $builder->get()->getResult();
    }

    public function insertPelanggan($data) 
    {
       return $this->db->table($this->table)->insert($data);
    }

}
