<?php

namespace App\Models\UserModel;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'mst_user';
    protected $primaryKey = 'id'; // Kunci utama tabel mst_param_emp
    protected $useTimestamps = true;
    protected $allowedFields = ['id', 'id_ref', 'username', 'email', 'password_web', 'password_mob', 'flg_used', 'user_create', 'create_date', 'user_update', 'update_date'];

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->where('flg_used', 'T')->first();
    }

    public function updatePassword($npwd, $id)
    {
        $builder = $this->db->table('mst_user');
        $builder->set('password_web', $npwd);
        $builder->where('username', $id);
        $builder->update();
        if ($this->db->affectedRows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
