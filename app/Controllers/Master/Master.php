<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Master extends BaseController
{
    // Data pm inspector
    public function dataPmInspector()
    {

        $data = $this->pmInspectorModel->getPmInspector();
        echo json_encode($data);
    }

    public function dataFilterPmInspector()
    {
        // Ambil data dari model
        $data = $this->pmInspectorModel->getFilterPmInspector();

        // Set header untuk menandakan bahwa respons adalah JSON
        header('Content-Type: application/json');

        // Encode data ke JSON dan kembalikan
        echo json_encode($data);
    }

    // Data coa
    public function dataCoaFilter()
    {   
        $term = $this->request->getGet('term'); // Ambil parameter 'term' dari query string
        $builder = $this->db->table('mst_coa')
            ->select('coa_code AS value, coa_name AS label'); // Sesuaikan dengan struktur tabel Anda
        if ($term) {
            $builder->like('coa_code', $term) // Filter berdasarkan coa_code
                    ->orLike('coa_name', $term); // Atau berdasarkan coa_name (label)
        }
        $builder->where('flg_used', 't');
        $data = $builder->get()->getResultArray();

        return $this->response->setJSON($data); // Mengembalikan data dalam format JSON
    }

    // Data nilai anggaran
    public function dataCoaVal()
    {
        $coa = $this->request->getPost('coa');
        $id_ref = $this->request->getPost('id_ref');

        $data = $this->coaModel->getCoaVal($coa, $id_ref);
        echo json_encode($data);
    }

    // Data pelanggan
    public function dataFilterPelanggan()
    {

        $data = $this->pelangganModel->getDataFilterMstPelanggan();
        echo json_encode($data);
    }

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
    // Function Index -> halaman Pelanggan
    public function indexMstPelanggan()
    {
        $data = [
            'title' => "Master pelanggan",
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('master/pelanggan', $data);
    }

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

    //USER PELANGGAN
    // Function Index -> halaman Master User Pelanggan
    public function indexUserPelanggan()
    {
        $data = [
            'title' => "User pelanggan",
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('master/user_pelanggan', $data);
    }

    //data master user pelanggan
    public function dataMstUserPelanggan()
    {
        $cabang = $this->request->getPost('branch_id');

        $data = $this->pelangganModel->getDataMstUserPelanggan($cabang);
        echo json_encode($data);
    }

    //data master posisi user pelanggan
    public function dataMstPosUserPelanggan()
    {
        
        $data = $this->pelangganModel->getMstPosUserPelanggan();
        echo json_encode($data);
    }

    public function updateUserPelanggan()
    {
        // Log request untuk debugging
        log_message('debug', 'Update Request Data: ' . json_encode($this->request->getPost()));

        // Ambil data dari POST
        $data = $this->request->getPost();
        $id = $data['id'] ?? null;

        // Validasi input
        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter id kosong'
            ], 400);
        }

        // Ambil username dari session
        $username = $this->session->get('username');
        if (!$username) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session username tidak ditemukan'
            ], 401);
        }

        // Validasi role_id
        $roleId = $this->session->get('role_id');
        if (!in_array($roleId, ['1', '2'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        // Hapus id dari data yang akan diupdate
        unset($data['id']);

        // Konversi string kosong ke NULL untuk kolom tertentu
        $nullableFields = ['name', 'user_cat', 'no_phone'];
        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        // Tambahkan user_update dan update_date
        $data['user_update'] = $username;
        $data['update_date'] = date('Y-m-d H:i:s');

        // Update data di database
        $result = $this->pelangganModel->updateUserPelanggan($id, $data);

        // Response JSON
        if ($result) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data'
            ], 500);
        }
    }


    //KELAS PRODUK
    //MASTER KELAS PRODUK
    // Function Index -> halaman Kelas Produk
    public function indexMstKlsProduk()
    {
        $data = [
            'title' => "Master kelas produk",
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('master/kelas_produk', $data);
    }

    //data master kelas produk
    public function dataMstKlsProduk()
    {
        $grupprod = session()->get('group_id');

        $data = $this->kelasProdModel->getDataMstKlsProd($grupprod);
        echo json_encode($data);
    }

    //KEBUTUHAN FILTER DATA DENGAN OPSI KESELURUHAN DATA SUBGROUP DAN CLASS DIAMBIL
    // Function untuk mendapatkan Subgroup Barang berdasarkan Group Product
    public function getFilterSubgrp()
    {
        $data = $this->kelasProdModel->getFilterMstprodsubgrp($this->request->getPost('group_prod'));
        foreach ($data as $row) {
            $label = $row['subgroup_id'] !== null ? $row['subgroup_id'] . ' - ' . $row['subgroup_name'] : $row['subgroup_name'];
            echo '<option value="' . $row['subgroup_id'] . '">' . $label . '</option>';
        }
    }

    // Function untuk mendapatkan Class Barang berdasarkan Group dan Subgroup Barang
    public function getFilterClass()
    {
        $data = $this->kelasProdModel->getFilterMstclass(
            $this->request->getPost('group_prod'),
            $this->request->getPost('subgroup_prod')
        );
        foreach ($data as $row) {
            $label = $row['class_id'] !== null ? $row['class_id'] . ' - ' . $row['class_name'] : $row['class_name'];
            echo '<option value="' . $row['class_id'] . '">' . $label . '</option>';
        }
    }

    // Function untuk mendapatkan Data Unit/Cabang
    public function getMstCabang()
    {
        $data = $this->cabangModel->getCabang();
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

    //MASTER KARYAWAN
    // Function Index -> halaman Karyawan
    public function indexMstKaryawan()
    {
        
        $data = [
            'title' => "Master karyawan",
            'cabang' => $this->cabangModel->getCabangFilter(),
            'departemen' => $this->DeptModel->getDepartemenFilter(),
            'jabatan' => $this->posisiModel->getPosisiFilter(),
            'role' => $this->hakAksesModel->getHakAksesFilter(),
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('master/karyawan', $data);
    }

    // Data karyawan
    public function dataKaryawan()
    {
        //filter data
        $cabang = $this->request->getPost('cabang');

        $data = $this->karyawanModel->getDataKaryawan($cabang);
        echo json_encode($data);
    }

    public function getKaryawan($id)
    {
        $data = $this->karyawanModel->getKaryawanById($id);
        return $this->response->setJSON($data);
    }

    public function updateDataKaryawan()
    {
        $id = $this->request->getPost('id');
        $nik = $this->request->getPost('nikKaryawan');
        $cabang = $this->request->getPost('cabKaryawan');
        $departemen = $this->request->getPost('depKaryawan');
        $jabatan = $this->request->getPost('jabKaryawan');
        $role = $this->request->getPost('roleKaryawan');

        $username = $this->session->get('username');

        $data = [
            'branch_id' => $cabang,
            'department_id' => $departemen,
            'position_id' => $jabatan,
            'role_id' => $role,
            'user_update' => $username,
            'update_date' => date('Y-m-d H:i:s')
        ];

        $result = $this->karyawanModel->updateKaryawan($id, $data);

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data karyawan berhasil diperbarui']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data karyawan']);
        }
    }

    //halaman form karyawan
    public function indexMstKaryawanForm()
    {
        
        $data = [
            'title' => "Formulir Registrasi Karyawan",
            'cabang' => $this->cabangModel->getCabangFilter(),
            'departemen' => $this->DeptModel->getDepartemenFilter(),
            'jabatan' => $this->posisiModel->getPosisiFilter(),
            'role' => $this->hakAksesModel->getHakAksesFilter(),
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('master/form_karyawan', $data);
    }

    public function insertKaryawan()
    {
        // Ambil data dari form
        $nik = $this->request->getPost('nikKaryawan');
        $nama = $this->request->getPost('namaKaryawan');
        $cabang = $this->request->getPost('cabKaryawan');
        $departemen = $this->request->getPost('depKaryawan');
        $jabatan = $this->request->getPost('jabKaryawan');
        $role = $this->request->getPost('roleKaryawan');

        $nama = strtoupper($nama);

        // Ambil username dari session
        $username = $this->session->get('username');

        // Data untuk tabel mst_employee
        $dataEmployee = [
            'nik' => $nik,
            'name' => $nama,
            'user_create' => $username,
            'create_date' => date('Y-m-d H:i:s')
        ];

        // Data untuk tabel mst_param_emp
        $dataParamEmp = [
            'nik' => $nik,
            'branch_id' => $cabang,
            'department_id' => $departemen,
            'position_id' => $jabatan,
            'role_id' => $role,
            'user_create' => $username,
            'create_date' => date('Y-m-d H:i:s')
        ];

        // Insert ke tabel mst_employee
        $this->karyawanModel->insertKaryawan($dataEmployee);

        // Insert ke tabel mst_param_emp
        $this->karyawanModel->insertParamKaryawan($dataParamEmp);

        // Redirect dengan pesan sukses
        return redirect()->to('/master/karyawan/index')->with('success', 'Data karyawan berhasil disimpan');
    }

    //MASTER USER
    // Function Index -> halaman User
    public function indexMstUser()
    {
        
        $data = [
            'title' => "Master User",
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('master/user', $data);
    }

    // Data user
    public function dataUser()
    {

        $data = $this->karyawanModel->getDataUser();
        echo json_encode($data);
    }

    public function updateDataUser()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $username = $this->session->get('username');

        $data = [
            'flg_used' => $status,
            'user_update' => $username,
            'update_date' => date('Y-m-d H:i:s')
        ];

        $result = $this->karyawanModel->updateUser($id, $data);

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data karyawan berhasil diperbarui']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data karyawan']);
        }
    }

    //halaman form user
    public function indexMstUserForm()
    {
        
        $data = [
            'title' => "Formulir Registrasi User",
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('master/form_user', $data);
    }

    // Function untuk mendapatkan Karyawan
    public function getFilterKaryawan()
    {
        $data = $this->karyawanModel->getFilterDataKaryawan();
        echo json_encode($data);
    }

    public function insertUser()
    {
        // Ambil data dari form
        $idRef = $this->request->getPost('idRefUser');
        $username = $this->request->getPost('usernameKaryawan');
        $password = $this->request->getPost('passwordKaryawan');

        // Validasi data
        if (empty($idRef) || empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Semua field wajib diisi');
        }

        // Ambil username dari session
        $userCreate = $this->session->get('username');

        // Konversi password ke hash untuk password_web (cost 12)
        $hashedPasswordWeb = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Konversi password ke hash untuk password_mob (cost 10)
        $hashedPasswordMob = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        // Data untuk tabel mst_user
        $dataUser = [
            'id_ref' => $idRef,
            'username' => $username,
            'password_web' => $hashedPasswordWeb,
            'password_mob' => $hashedPasswordMob,
            'user_create' => $userCreate,
            'create_date' => date('Y-m-d H:i:s')
        ];

        // Insert ke tabel mst_user
        $result = $this->karyawanModel->insertUser($dataUser);

        if ($result) {
            return redirect()->to('/master/user/index')->with('success', 'Data user berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data user');
        }
    }
}