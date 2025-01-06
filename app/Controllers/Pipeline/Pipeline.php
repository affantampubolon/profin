<?php

namespace App\Controllers\Pipeline;

use App\Controllers\BaseController;
use App\Models\PipelineModel\PipelineModel;

class Pipeline extends BaseController
{
    protected $pipelineModel;
    // Parent Construct
    public function __construct()
    {
        $this->pipelineModel = new PipelineModel();
        $this->Session = \Config\Services::session();
    }

    // Function Index -> halaman LOGIN
    public function index()
    {
       
        $data = [
            'title' => "Pembuatan Pipeline",
        ];
        return view('pipeline/pembuatan', $data);
    }

    
}
