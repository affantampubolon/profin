<?php

namespace App\Controllers\Pipeline;

use App\Controllers\BaseController;
use App\Models\PipelineModel\PipelineModel;

class Pipeline extends BaseController
{
    // Parent Construct
    public function __construct() {}

    public function index()
    {
        $data = [
            'title' => "Pembuatan Pipeline",
            'breadcrumb' => $this->breadcrumb
        ];
        return view('pipeline/pembuatan', $data);
    }
}
