<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Master extends BaseController
{
    // Function untuk mendapatkan Subgrup Barang berdasarkan Grup Barang
    public function getSubGrupBarang()
    {
        $grp_prod = session()->get('group_id');

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
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('master/regis_pelanggan', $data);
    }

    public function dataRegisPelanggan()
    {
        $cabang = session()->get('branch_id');

        $data = $this->pelangganModel->getRegisPelanggan($cabang);
        echo json_encode($data);
    }

    public function updateVerifRegisPelanggan()
    {
        // Log request untuk debugging
        log_message('debug', 'Request Data: ' . json_encode($this->request->getPost()));

        // Ambil data dari POST
        $data = $this->request->getPost();
        $id = $data['id'] ?? null;

        // Validasi input
        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter id kosong'
            ]);
        }

        // Ambil username dari session
        $username = $this->session->get('username');
        if (!$username) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session username tidak ditemukan'
            ]);
        }

        // Hapus id dari data yang akan diupdate
        unset($data['id']);

        // Konversi string kosong ke NULL untuk kolom date
        $dateFields = ['exp_date_sia', 'exp_date_sipa'];
        foreach ($dateFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        // Konversi string kosong ke NULL untuk kolom numeric
        $numericFields = ['plafond', 'payment_term'];
        foreach ($numericFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        // Tambahkan user_update dan update_date
        $data['user_update'] = $username;
        $data['update_date'] = date('Y-m-d H:i:s');

        // Update data di database
        $result = $this->pelangganModel->updateVerifPelanggan($id, $data);

        // Response JSON
        if ($result) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data'
            ]);
        }
    }

    //KEBUTUHAN FILTER DATA DENGAN OPSI KESELURUHAN DATA SUBGROUP DAN CLASS DIAMBIL
    // Function untuk mendapatkan Subgroup Barang berdasarkan Group Product
    public function getFilterSubgrp()
    {
        // $data = $this->builder->where('group_id', $this->request->getPost('group'))->get()->getResult();
        $data = $this->kelasProdModel->getFilterMstprodsubgrp($this->request->getPost('group_prod'))->getResult();
        // echo '<option selected value="">Pilih SubGroup Product</option>';
        foreach ($data as $row) {
            echo '<option value="' . $row->subgroup_id . '">' . $row->subgroup_id . ' - ' . $row->subgroup_name . '</option>';
        }
        // echo json_encode($data);
    }

    // Function untuk mendapatkan Class Barang berdasarkan Group dan Subgroup Barang
    public function getFilterClass()
    {
        // $data = $this->builder2->where('subgroup_id', $this->request->getPost('subgroup'))->where('group_id', $this->request->getPost('group_id'))->get()->getResult();
        $data = $this->kelasProdModel->getFilterMstclass($this->request->getPost('group_prod'), $this->request->getPost('subgroup_prod'))->getResult();
        // echo '<option selected value="">Pilih Class Product</option>';
        foreach ($data as $row) {
            echo '<option value="' . $row->class_id . '"> ' . $row->class_id . ' - ' . $row->class_name . '</option>';
        }

        // echo json_encode($data);
    }

    // Function untuk mendapatkan Data Unit/Cabang
    public function getMstCabang()
    {
        $data = $this->cabangModel->getCabang();
        echo json_encode($data);
    }

    // Data Sales/Marketing
    public function getMstSalesMarketing()
    {
        $grp_prod = session()->get('group_id');
        $branch_id = $this->request->getPost('branch_id');
        if (empty($branch_id)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Branch ID tidak boleh kosong']);
        }

        $data = $this->salesMarketingModel->getSalesMarketingByFilter($branch_id, $grp_prod);
        echo json_encode($data);
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

    public function dataFilterProbabilitas()
    {
        // Ambil username dari session
        $username = session()->get('username');
    
        $data = $this->probabilitasModel->getSkalaProbabilitas($username);
    
        // Format data agar cocok dengan Tabulator
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                "value" => $item['scale'],       // Ini yang dipilih saat update
                "label" => $item['description']  // Ini yang ditampilkan di dropdown
            ];
        }

        return $this->response->setJSON($formattedData);
    }
}