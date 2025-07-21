<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class PosisiModel extends Model
{
    protected $table = 'mst_position';
    protected $allowedFields = ['id', 'name', 'flg_used', 'user_create', 'create_date', 'user_update', 'update_date'];

    public function getPosisiFilter()
    {
        return $this->where('flg_used', TRUE)->orderBy('id')->findAll();
    }
}
