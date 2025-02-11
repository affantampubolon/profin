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

    //MASTER PELANGGAN

    // Function Index -> halaman Registrasi Pelanggan
    public function indexRegisPelanggan()
    {
        $data = [
            'title' => "Registrasi pelanggan",
            'breadcrumb' => $this->breadcrumb
        ];
        return view('master/regis_pelanggan', $data);
    }

    // Function untuk mendapatkan Pelanggan
    public function getMstPelanggan()
    {
        // Ambil username dari session
        $username = session()->get('username');

        $data = $this->pelangganModel->getMstPelangganCab($username);
        echo json_encode($data);
    }

    // Function Data Kategori Pelanggan
    public function getMstKategoriPelanggan()
    {
        $data = $this->pelangganModel->getMstKategoriPelanggan();
        echo json_encode($data);
    }

    // Function Data Wilayah
    // Area Provinsi
    public function getMstAreaProvinsi()
    {
        $data = $this->wilayahModel->getAreaProvinsi();
        echo json_encode($data);
    }

    // Area Kota/Kab
    public function getMstAreaKotaKab()
    {
        $province_id = $this->request->getPost('province_id');
        if (empty($province_id)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Province ID tidak boleh kosong']);
        }

        $data = $this->wilayahModel->getAreaKotaKab($province_id);
        echo json_encode($data);
    }

    // Area Kecamatan
    public function getMstAreaKecamatan()
    {
        $province_id = $this->request->getPost('province_id');
        $city_id = $this->request->getPost('city_id');
        if (empty($province_id) || empty($city_id)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Province ID dan City ID tidak boleh kosong']);
        }

        $data = $this->wilayahModel->getAreaKecamatan($province_id, $city_id);
        echo json_encode($data);
    }

    // Area Kelurahan
    public function getMstAreaKelurahan()
    {
        $province_id = $this->request->getPost('province_id');
        $city_id = $this->request->getPost('city_id');
        $district_id = $this->request->getPost('district_id');
        if (empty($province_id) || empty($city_id) || empty($district_id)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Semua parameter wilayah harus diisi']);
        }

        $data = $this->wilayahModel->getAreaKelurahan($province_id, $city_id, $district_id);
        echo json_encode($data);
    }

    // Kode Pos
    public function getMstAreaKodePos()
    {
        $province_id = $this->request->getPost('province_id');
        $city_id = $this->request->getPost('city_id');
        $district_id = $this->request->getPost('district_id');
        $subdistrict_id = $this->request->getPost('subdistrict_id');
        if (empty($province_id) || empty($city_id) || empty($district_id) || empty($subdistrict_id)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Semua parameter wilayah harus diisi']);
        }

        $data = $this->wilayahModel->getAreaKodePos($province_id, $city_id, $district_id, $subdistrict_id);
        echo json_encode($data);
    }
}