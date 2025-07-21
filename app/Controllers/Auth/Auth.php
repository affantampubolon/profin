<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    // Parent Construct
    public function __construct() {}

    // Function Index -> halaman LOGIN
    public function index()
    {
        // jika sudah login dan ingin masuk ke laman login maka akan diarahkan ke default page
        if ($this->session->logged_in == true) {
            $this->goToDefaultPage();
        }
        $data = [
            'title' => "PROFIN - LOGIN",
            'validation' => $this->validation,
        ];
        return view('auth/login', $data);
    }

    // Function Index -> halaman LOGIN
    public function beranda()
    {
        // jika sudah login dan ingin masuk ke laman login maka akan diarahkan ke default page
        if ($this->session->logged_in == true) {
            $this->goToDefaultPage();
        }

        $cabang = session()->get('branch_id');
        $nik = session()->get('username');

        $data = [
            'title' => "Beranda",
            'validation' => $this->validation,
            'breadcrumb' => $this->breadcrumb,
            'session' => $this->session
        ];
        return view('auth/dashboard', $data);
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $rules = [
                'username' => 'required',
                'password' => 'required'
            ];

            if (!$this->validate($rules)) {
                // Validasi gagal
                return redirect()->to('/')->withInput()->with('validation', $this->validation); // Gunakan $this->validator
            }

            // Memproses data login
            $username = trim($this->request->getPost('username'));
            $password = $this->request->getPost('password');

            $data = $this->userModel->getUserByUsername($username);

            // cek ada data atau tidak
            if ($data) {
                // jika ada data maka cek aktif atau tidak
                if ($data['flg_used'] == TRUE) {
                    // jika user data aktif maka cek password
                    if (password_verify($password, $data['password_web'])) {
                        // jika password cocok maka cek role nya 
                        // Mendapatkan data mst_param_emp
                        $paramEmp = $this->paramEmpModel->getParamEmpByIdRef($data['id_ref']);
                        // Jika paramEmp ada maka ambil datanya
                        if ($paramEmp) {
                            $empData = $this->empModel->getEmployeeByNik($paramEmp['nik']);
                            $deptData = $this->DeptModel->getGroupByDeptID($paramEmp['department_id']);
                            $groupData = $this->kelasProdModel->getGrupBarang($paramEmp['nik']);
                            $cabData = $this->cabangModel->getCabangBySession($paramEmp['branch_id']);
                            // Set sessionnya berdasarkan role
                            if ($empData == TRUE) {
                                // Jika Role id nya == 1 maka dia admin , maka -> Create session loginnya :
                                // Data session login
                                $ses_data = [
                                    'username'      => $data['username'],
                                    'name'          => $empData['name'],
                                    'role_id'       => $paramEmp['role_id'],
                                    'branch_id'     => $paramEmp['branch_id'],
                                    'branch_name'    => $cabData['branch_name'],
                                    'position_id'   => $paramEmp['position_id'],
                                    'department_id' => $paramEmp['department_id'],
                                    'group_id'      => $deptData['group_id'],
                                    'group_name'    => $groupData['group_name'],
                                    'logged_in'     => TRUE,
                                ];
                                // Set session
                                $this->session->set($ses_data);
                                // Redirect ke halaman admin
                                return redirect()->to('/beranda');
                            }
                        } else {
                            $this->session->setFlashdata('msg', 'Hubungi IT untuk melengkapi data!');
                            return redirect()->to('/');
                        }
                    } else {
                        $this->session->setFlashdata('msg', 'Pengguna atau kata sandi salah!');
                        return redirect()->to('/');
                    }
                } else {
                    $this->session->setFlashdata('msg', 'Pengguna tidak aktif hubungi it!');
                    return redirect()->to('/');
                }
            } else {
                $this->session->setFlashdata('msg', 'Pengguna tidak terdaftar!');
                return redirect()->to('/');
            }
        } else {
            redirect()->to('/')->withInput()->with('msg', 'Error!');
        }
    }

    public function logout()
    {
        $session_data = ['username', 'role_id', 'logged_in'];
        $this->session->remove($session_data);
        $this->session->setFlashdata('logout', 'Anda berhasil keluar!');
        return redirect()->to('/');
    }
    // public function login()
    // {
    //     // Helper Form
    //     helper(['form']);

    //     if (!$this->validate([
    //         'username' => 'required',
    //         'password' => 'required'
    //     ])) {
    //         $validation = \Config\Services::validation();
    //         return redirect()->to('/')->withInput()->with('validation', $validation);
    //     }

    //     // Ambil Inputan username dan password
    //     $username = trim($this->request->getVar('username'));
    //     $password = $this->request->getVar('password');
    //     // Cek Data inputan dengan database apakah ada user / email dari inputan 
    //     $data = $this->userModel->where('username', $username)->orWhere('email', $username)->first();
    //     // Jika data ada maka -> cek password
    //     if ($data) {
    //         // Cek Apakah user Aktif atau tidak :
    //         if ($data['is_active'] == 1) {
    //             // Jika User Active -> Cocokan password hasil inputan dengan password database :
    //             if (password_verify($password, $data['password'])) {
    //                 // Jika password cocok maka -> cek role id nya :
    //                 if ($data['role_id'] == 1) {
    //                     // Jika Role id nya == 1 maka dia admin , maka -> Create session loginnya :
    //                     // Data session login
    //                     $ses_data = [
    //                         'username'   => $data['username'],
    //                         'role_id'    => $data['role_id'],
    //                         'branch_id'  => $data['branch_id'],
    //                         'foto'       => $data['image'],
    //                         'logged_in'  => TRUE
    //                     ];
    //                     // Set session
    //                     $this->session->set($ses_data);
    //                     // Redirect ke halaman admin
    //                     return redirect()->to('/admin');
    //                 } elseif ($data['role_id'] == 2) {
    //                     // Jika Role id nya == 2 maka dia Operasional , maka -> Create session loginnya :
    //                     // Data session login
    //                     $ses_data = [
    //                         'username'   => $data['username'],
    //                         'role_id'    => $data['role_id'],
    //                         'branch_id'  => $data['branch_id'],
    //                         'foto'       => $data['image'],
    //                         'logged_in'  => TRUE
    //                     ];
    //                     // Set session
    //                     $this->session->set($ses_data);
    //                     // Redirect ke halaman marketing
    //                     return redirect()->to('/marketing');
    //                 } elseif ($data['role_id'] == 3) {
    //                     // Jika Role id nya == 3 maka dia NON OPERASIONAL , maka -> Create session loginnya :
    //                     // Data session login
    //                     $ses_data = [
    //                         'username'   => $data['username'],
    //                         'role_id'    => $data['role_id'],
    //                         'branch_id'  => $data['branch_id'],
    //                         'foto'       => $data['image'],
    //                         'logged_in'  => TRUE
    //                     ];
    //                     // Set session
    //                     $this->session->set($ses_data);
    //                     // Redirect ke halaman finance
    //                     return redirect()->to('/finance');
    //                 } elseif ($data['role_id'] == 4) {
    //                     // Jika Role id nya == 4 maka dia MASTER , maka -> Create session loginnya :
    //                     // Data session login
    //                     $ses_data = [
    //                         'username'   => $data['username'],
    //                         'role_id'    => $data['role_id'],
    //                         'branch_id'  => $data['branch_id'],
    //                         'foto'       => $data['image'],
    //                         'logged_in'  => TRUE
    //                     ];
    //                     // Set session
    //                     $this->session->set($ses_data);
    //                     // Redirect ke halaman finance
    //                     return redirect()->to('/marketing');
    //                 }
    //             }
    //             // Jika password Salah maka -> Kembali kehalaman login dan berikan pesan salah password 
    //             else {
    //                 $this->session->setFlashdata('msg', 'Password yang anda masukkan salah!');
    //                 return redirect()->to('/');
    //             }
    //         }
    //         // Jika user tidak active maka tidak bisa login : 
    //         else {
    //             $this->session->setFlashdata('msg', 'User tidak Active silahkan kontak Administrator!');
    //             return redirect()->to('/');
    //         }
    //     }
    //     // Jika data tidak ada berdasarkan inputan maka -> Kembali ke halaman login dan berikan pesan user tidak terdaftar 
    //     else {
    //         $this->session->setFlashdata('msg', 'Username atau Email tidak terdaftar!');
    //         return redirect()->to('/');
    //     }
    // }

    // public function logout()
    // {
    //     $ses_data = ['username', 'role_id', 'branch_id', 'logged_in'];
    //     $this->session->remove($ses_data);
    //     $this->session->setFlashdata('logout', 'Anda Berhasil Logout!');
    //     return redirect()->to('/');
    // }

    // DefaultPage after login
    public function goToDefaultPage()
    {
        // Jika Sudah login maka tidak bisa ke menu login sebelum logout
        // Admin
        if ($this->session->role_id == 1) {
            return redirect()->to('/dashboard');
        }
        // Jika Bukan Admin maka dia Operasional
        else if ($this->session->role_id  == 2) {
            return redirect()->to('/marketing');
        }
        // Jika Bukan operasional maka dia non operasional 
        else if ($this->session->role_id  == 3) {
            return redirect()->to('/finance');
        }
        // End
    }

    // Blocked because required user access
    // public function blocked()
    // {
    //     $data = [
    //         'title' => "403 - Forbidden"
    //     ];
    //     echo view('auth/blocked', $data);
    //     // $this->load->view('templates/auth_header', $data);
    //     // $this->load->view('auth/blocked');
    // }

    // Change password
    // public function changePassword()
    // {
    //     // Helper Form
    //     helper(['form']);
    //     $validator = \Config\Services::validation();

    //     $data = [
    //         'title'   => "Change Password",
    //         'session' => $this->session,
    //     ];
    //     $data['userdata'] = $this->userModel->where('username', $this->session->username)->first();

    //     if ($this->request->getMethod() == 'post') {
    //         $rules = [
    //             'current_password'  => 'required',
    //             'new_password'      => 'required|min_length[6]|max_length[16]',
    //             'repeat_password'   => 'required|matches[new_password]',
    //         ];
    //         if ($this->validate($rules)) {

    //             $current   = $this->request->getPost('current_password');
    //             $newpassword = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);

    //             if (password_verify($current, $data['userdata']['password'])) {
    //                 if ($this->userModel->updatePassword($newpassword, $this->session->username)) {
    //                     session()->setTempdata('success', 'Password berhasil diganti', 3);
    //                     return redirect()->to(current_url());
    //                 } else {
    //                     session()->setTempdata('error', 'Maaf password gagal diganti, ulangi kembali', 3);
    //                     return redirect()->to(current_url());
    //                 }
    //             } else {
    //                 session()->setTempdata('error', 'Password lama tidak sesuai', 3);
    //                 return redirect()->to(current_url());
    //             }
    //         } else {

    //             $data['validation'] = $this->validator;
    //         }
    //     }

    //     echo view('auth/change_password', $data);
    // }
}
