<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class KategoriProyekModel extends Model
{
    protected $table = 'mst_job_category';
    protected $allowedFields = ['id', 'name', 'flg_used', 'user_create', 'create_date', 'user_update', 'update_date'];

    public function getKatProyekFilter()
    {
        return $this->where('flg_used', TRUE)->orderBy('id')->findAll();
    }
}
