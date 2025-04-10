<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class CabangModel extends Model
{
    protected $table = 'mst_branch';
    protected $allowedFields = ['branch_id', 'init', 'branch_name', 'address', 'province_id', 'city_id', 'district_id', 'subdistrict_id', 'zip_code', 'latitude', 'longitude', 'flg_used', 'user_create', 'create_date', 'user_update', 'update_date'];

    public function getCabang()
    {
        return $this->where('branch_id <>', '11')->where('flg_used', TRUE)->orderBy('branch_id')->findAll();
    }

    public function getCabangBySession($branch_id)
    {
        return $this->where('branch_id', $branch_id)->where('flg_used', TRUE)->first();
    }
}
