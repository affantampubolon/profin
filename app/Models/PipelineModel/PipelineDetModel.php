<?php

namespace App\Models\PipelineModel;

use CodeIgniter\Model;

class PipelineDetModel extends Model
{
    protected $table      = 'trn_pipeline_det';
    protected $allowedFields = ['id_ref', 'cust_id', 'target_call', 'target_ec', 'target_value', 'probability'];

    // ... (method-method lainnya jika diperlukan)
}