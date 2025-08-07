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

    // Data kategori proyek
    public function dataKatProyek()
    {
        $data = $this->kategoriProyekModel->getKatProyekFilter();
        echo json_encode($data);
    }

    // Data pelanggan
    public function dataFilterPelanggan()
    {

        $data = $this->pelangganModel->getDataFilterMstPelanggan();
        echo json_encode($data);
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

     public function dataMstPelanggan()
    {
        $cabang = session()->get('branch_id');

        $data = $this->pelangganModel->getDataMstPelanggan($cabang);
        echo json_encode($data);
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

    public function insertPelanggan()
    {
        // Log request untuk debugging
        log_message('debug', 'Request Data: ' . json_encode($this->request->getPost()));

        // Ambil data dari POST
        $data = $this->request->getPost();

        // Ambil username dari session
        $username = $this->session->get('username');
        if (!$username) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session username tidak ditemukan'
            ]);
        }


        // Tambahkan user_update dan update_date
        $data['user_create'] = $username;
        $data['create_date'] = date('Y-m-d H:i:s');

        // masukkan data di database
        $result = $this->pelangganModel->insertPelanggan($data);

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