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
    public function getDataMstPelanggan()
    {
        $builder = $this->db->table('mst_customer')
            ->select("
                id,
                branch_id,
                cust_id,
                cust_name,
                category_id,
                f_tv_catcust_name(category_id) AS catcust_name,
                address,
                f_tv_province_name(province_id) AS province_name,
                f_tv_city_name(province_id,city_id) AS city_name,
                f_tv_district_name(province_id,city_id,district_id) AS district_name,
                f_tv_subdistrict_name(province_id,city_id,district_id,subdistrict_id) AS subdistrict_name,
                zip_code,
                email,
                phone_no,
                tax_status,
                npwp,
                siup,
                cust_name_tax,
                address_tax,
                id_card,
                plafond,
                payment_term,
                construction_type,
                status_building,
                owner_name,
                pharmacist,
                sipa,
                sia,
                exp_date_sia,
                exp_date_sipa,
                flg_noo,
                f_tv_catcust_pharmacist(category_id) AS flg_pharmacist
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
    
    //Registrasi Pelanggan
    public function getRegisPelanggan($cabang)
    {
        $builder = $this->db->table('mst_customer')
            ->select("
                id,
                branch_id,
                req_no,
                cust_name,
                category_id,
                f_tv_catcust_name(category_id) AS catcust_name,
                f_tv_catcust_pharmacist(category_id) AS flg_pharmacist
            ")
            ->where('branch_id', $cabang)
            ->where('req_no IS NOT NULL')
            ->where('flg_noo', 't')
            ->where('flg_verify_noo', 'f')
            ->orderBy('id');

        return $builder->get()->getResult();
    }

    public function updateVerifPelanggan($id, $data) 
    {
       return $this->db->table($this->table)
                        ->where('id', $id)
                        ->update($data);
    }

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
