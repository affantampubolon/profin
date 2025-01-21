<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Master extends BaseController
{
    // Function untuk mendapatkan Subgrup Barang berdasarkan Grup Barang
    public function getSubGrupBarang()
    {
        $grp_prod = $this->request->getPost('grp_prod');

        // Validasi input
        if (empty($grp_prod)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['message' => 'Group Product tidak boleh kosong']);
        }

        // Ambil data dari model
        $data = $this->kelasProdModel->getSubGrupBarang($grp_prod);

        if (empty($data)) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['message' => 'Sub Grup Barang tidak ditemukan']);
        }

        // Buat opsi untuk dropdown
        $options = '<option selected value="">Pilih Sub Grup Barang</option>';
        foreach ($data as $row) {
            $options .= '<option value="' . $row['subgroup_id'] . '">'
                . $row['subgroup_id'] . ' - ' . $row['subgroup_name']
                . '</option>';
        }

        return $this->response->setBody($options);
    }

    // Function untuk mendapatkan Kelas Barang berdasarkan Grup dan Subgrup Barang
    public function getKelasBarang()
    {
        $grp_prod = $this->request->getPost('grp_prod');
        $subgrp_prod = $this->request->getPost('subgrp_prod');

        // 
        $data = $this->kelasProdModel->getKelasBarang($grp_prod, $subgrp_prod);
        // Buat opsi untuk dropdown
        $options = '<option selected value="">Pilih Kelas Barang</option>';
        foreach ($data as $row) {
            $options .= '<option value="' . $row['class_id'] . '">'
                . $row['class_id'] . ' - ' . $row['class_name']
                . '</option>';
        }

        return $this->response->setBody($options);
    }

    // Function untuk mendapatkan Pelanggan
    public function getMstPelanggan()
    {
        $data = $this->pelangganModel->getMstPelanggan();
        echo json_encode($data);
    }
}