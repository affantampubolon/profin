<?php

namespace App\Models\KeuanganModel;

use CodeIgniter\Model;

class PembayaranModel extends Model
{

    protected $tablePembayaran = 'trn_payment';

    protected $allowedFields = ['id','no_doc','id_ref', 'description','payment_amt', 'payment_date', 'flg_used','user_create','create_date','user_update','update_date'];

    public function insertPembayaran($data)
    {
        return $this->db->table($this->tablePembayaran)->insertBatch($data);
    }
    
}
