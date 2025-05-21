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
        //filter data aktivitas kunjungan
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
        //filter data distribusi prod
        $tgl = $this->request->getPost('tanggal');
        $cabang = $this->request->getPost('cabang');
        $nik = $this->request->getPost('sales_marketing');
        $pelanggan = $this->request->getPost('pelanggan');

        $data = $this->pelaporanModel->getDataDistribusiProd($tgl, $cabang, $nik, $pelanggan);
        echo json_encode($data);
    }

    // Function Distribusi Produk
    public function distribusiProd()
    {
        // Ambil group barang
        $grp_prod = $this->request->getPost('group_id');

        $data = [
            'title' => "Aktivitas Kunjungan",
            'group_barang' => $this->kelasProdModel->getFilterGrupBarang(),
            'subgroup_barang' => $this->kelasProdModel->getSubGrupBarang($grp_prod),
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('pelaporan/distribusi_prod', $data);
    }

    // Data distribusi produk dengan lokasi
    public function dataDistribusiProdLoc()
    {
        //filter data distribusi produksi dengan lokasi
        $tanggal_1 = $this->request->getPost('tanggal_1');
        $tanggal_2 = $this->request->getPost('tanggal_2');
        $cabang = $this->request->getPost('cabang');
        $grupprod = $this->request->getPost('grp_prod');
        $subgrupprod = $this->request->getPost('subgrp_prod');
        $klsgrupprod = $this->request->getPost('klsgrp_prod');

        $data = $this->pelaporanModel->getDataDistribusiProdLoc($tanggal_1, $tanggal_2, $cabang, $grupprod, $subgrupprod, $klsgrupprod);
        echo json_encode($data);
    }
}