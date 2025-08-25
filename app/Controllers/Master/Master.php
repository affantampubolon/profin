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

    public function getNpwpCustFile($fileName)
  {
      $filePath = WRITEPATH . 'uploads/pelanggan/npwp' . DIRECTORY_SEPARATOR . $fileName;

      if (file_exists($filePath) && is_file($filePath)) {
          // Set header untuk file PDF
          return $this->response
              ->setHeader('Content-Type', 'application/pdf')
              ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
              ->setHeader('Content-Length', filesize($filePath))
              ->setBody(file_get_contents($filePath));
      } else {
          return $this->response
              ->setStatusCode(404)
              ->setJSON(['status' => 'error', 'message' => 'File tidak ditemukan']);
      }
  }

  public function getNibCustFile($fileName)
  {
      $filePath = WRITEPATH . 'uploads/pelanggan/nib' . DIRECTORY_SEPARATOR . $fileName;

      if (file_exists($filePath) && is_file($filePath)) {
          // Set header untuk file PDF
          return $this->response
              ->setHeader('Content-Type', 'application/pdf')
              ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
              ->setHeader('Content-Length', filesize($filePath))
              ->setBody(file_get_contents($filePath));
      } else {
          return $this->response
              ->setStatusCode(404)
              ->setJSON(['status' => 'error', 'message' => 'File tidak ditemukan']);
      }
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

    // Ambil username dari session
    $username = $this->session->get('username');
    if (!$username) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Session username tidak ditemukan'
        ]);
    }

    // Inisialisasi array data
    $data = $this->request->getPost(); // Ambil data teks
    $data['user_create'] = $username;
    $data['create_date'] = date('Y-m-d H:i:s');

    // Penanganan unggahan file NPWP
    $fileNpwp = $this->request->getFile('fileNpwp');
    log_message('debug', 'File NPWP detected: ' . ($fileNpwp ? 'Yes' : 'No') . ', Is Valid: ' . ($fileNpwp ? ($fileNpwp->isValid() ? 'Yes' : 'No') : 'N/A'));
    if ($fileNpwp && $fileNpwp->isValid() && !$fileNpwp->hasMoved()) {
        $fileType = $fileNpwp->getClientMimeType();
        $fileSize = $fileNpwp->getSize();
        log_message('debug', 'File NPWP Type: ' . $fileType . ', File Size: ' . $fileSize . ' bytes');
        if ($fileType !== 'application/pdf') {
            return $this->response->setJSON(['success' => false, 'message' => 'File NPWP harus dalam format .pdf']);
        }
        if ($fileSize > 1 * 1024 * 1024) {
            return $this->response->setJSON(['success' => false, 'message' => 'File NPWP melebihi kapasitas 1 MB']);
        }
        $uploadPath = WRITEPATH . 'uploads/pelanggan/npwp';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }
        $fileName = $fileNpwp->getRandomName();
        $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
        if ($fileNpwp->move($uploadPath, $fileName)) {
            $data['file_npwp'] = $fileName; // Simpan hanya nama file
            log_message('debug', 'File NPWP saved successfully: ' . $fullPath);
        } else {
            log_message('error', 'Failed to move NPWP file: ' . $fileNpwp->getErrorString());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan file NPWP: ' . $fileNpwp->getErrorString()]);
        }
    } else {
        $data['file_npwp'] = null; // Jika tidak ada file, set NULL
    }

    // Penanganan unggahan file NIB
    $fileNib = $this->request->getFile('fileNib');
    log_message('debug', 'File NIB detected: ' . ($fileNib ? 'Yes' : 'No') . ', Is Valid: ' . ($fileNib ? ($fileNib->isValid() ? 'Yes' : 'No') : 'N/A'));
    if ($fileNib && $fileNib->isValid() && !$fileNib->hasMoved()) {
        $fileType = $fileNib->getClientMimeType();
        $fileSize = $fileNib->getSize();
        log_message('debug', 'File NIB Type: ' . $fileType . ', File Size: ' . $fileSize . ' bytes');
        if ($fileType !== 'application/pdf') {
            return $this->response->setJSON(['success' => false, 'message' => 'File NIB harus dalam format .pdf']);
        }
        if ($fileSize > 2.5 * 1024 * 1024) {
            return $this->response->setJSON(['success' => false, 'message' => 'File NIB melebihi kapasitas 2.5 MB']);
        }
        $uploadPath = WRITEPATH . 'uploads/pelanggan/nib';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }
        $fileName = $fileNib->getRandomName();
        $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
        if ($fileNib->move($uploadPath, $fileName)) {
            $data['file_nib'] = $fileName; // Simpan hanya nama file
            log_message('debug', 'File NIB saved successfully: ' . $fullPath);
        } else {
            log_message('error', 'Failed to move NIB file: ' . $fileNib->getErrorString());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan file NIB: ' . $fileNib->getErrorString()]);
        }
    } else {
        $data['file_nib'] = null; // Jika tidak ada file, set NULL
    }

    // Masukkan data ke database
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

    
    public function indexGantiPasswordForm()
    {
        
        $data = [
            'title' => "Perbarui Password",
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('auth/ganti_password', $data);
    }

    public function updateUserPassword()
    {
        $password = $this->request->getPost('updatePassword');

        // Konversi password ke hash untuk password_web (cost 12)
        $hashedPasswordWeb = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Konversi password ke hash untuk password_mob (cost 10)
        $hashedPasswordMob = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        $username = $this->session->get('username');

        $data = [
            'password_web' => $hashedPasswordWeb,
            'password_mob' => $hashedPasswordMob,
            'user_update' => $username,
            'update_date' => date('Y-m-d H:i:s')
        ];

        $result = $this->karyawanModel->updatePassword($username, $data);

        if ($result) {
            return redirect()->to('/beranda')->with('success', 'Password berhasil diperbarui');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui password');
        }
    }

  
}