<?php

namespace App\Controllers\Pelaporan;

use App\Controllers\BaseController;

class Pelaporan extends BaseController
{
    // Parent Construct
    public function __construct() {}

    // Function Aktivitas Kunjungan
    public function aktvitasKunj()
    {
        // Ambil username dari session
        $username = session()->get('username');

        $data = [
            'title' => "Aktivitas Kunjungan",
            'data_salesmarketing' => $this->salesMarketingModel->getSalesMarketingCab($username),
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('pelaporan/aktivitas_kunjungan', $data);
    }

    // Data aktivitas kunjungan
    public function dataAktivitasKunj()
    {
        //filter data verifikasi rencana kunjungan
        $tanggal_1 = $this->request->getPost('tanggal_1');
        $tanggal_2 = $this->request->getPost('tanggal_2');
        $cabang = $this->request->getPost('cabang');
        $nik = $this->request->getPost('sales_marketing');

        $data = $this->pelaporanModel->getDataAktivitasSales($tanggal_1, $tanggal_2, $cabang, $nik);
        echo json_encode($data);
    }

    // Data distribusi produk
    public function dataDistribusiProd()
    {
        //filter data verifikasi rencana kunjungan
        $tgl = $this->request->getPost('tanggal');
        $cabang = $this->request->getPost('cabang');
        $nik = $this->request->getPost('sales_marketing');
        $pelanggan = $this->request->getPost('pelanggan');

        $data = $this->pelaporanModel->getDataDistribusiProd($tgl, $cabang, $nik, $pelanggan);
        echo json_encode($data);
    }
}