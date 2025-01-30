<?php

namespace App\Models\IzinModel;

use CodeIgniter\Model;

class IzinModel extends Model
{
  protected $table      = 'trn_absence';
  protected $allowedFields = ['start_date', 'end_date', 'category_id', 'nik', 'description', 'flg_verify', 'user_verify', 'date_verify', 'flg_used', 'user_create', 'create_date', 'user_update', 'update_date'];

  // ... (method-method lainnya jika diperlukan)
}
